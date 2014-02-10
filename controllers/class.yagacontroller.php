<?php

if(!defined('APPLICATION'))
  exit();
/* Copyright 2013 Zachary Doll */

/**
 * Manage the yaga application including configuration and import/export
 *
 * @since 1.0
 * @package Yaga
 */
class YagaController extends DashboardController {

  /**
   * @var array These objects will be created on instantiation and available via
   * $this->ObjectName
   */
  public $Uses = array('Form');

  /**
   * Make this look like a dashboard page and add the resources
   *
   * @since 1.0
   * @access public
   */
  public function Initialize() {
    parent::Initialize();
    $this->Application = 'Yaga';
    Gdn_Theme::Section('Dashboard');
    if($this->Menu) {
      $this->Menu->HighlightRoute('/yaga');
    }
    $this->AddSideMenu('yaga/settings');

    $this->AddCssFile('yaga.css');
  }

  /**
   * Redirect to settings by default
   */
  public function Index() {
    $this->Settings();
  }

  /**
   * This handles all the core settings for the gamification application.
   *
   */
  public function Settings() {
    $this->Permission('Garden.Settings.Manage');
    $this->Title(T('Yaga.Settings'));

    // Get list of actions from the model and pass to the view
    $ConfigModule = new ConfigurationModule($this);

    $ConfigModule->Initialize(array(
        'Yaga.Reactions.Enabled' => array(
            'LabelCode' => 'Use Reactions',
            'Control' => 'Checkbox'
        ),
        'Yaga.Badges.Enabled' => array(
            'LabelCode' => 'Use Badges',
            'Control' => 'Checkbox'
        ),
        'Yaga.Ranks.Enabled' => array(
            'LabelCode' => 'Use Ranks',
            'Control' => 'Checkbox'
        ),
        'Yaga.LeaderBoard.Enabled' => array(
            'LabelCode' => 'Show leaderboard on activity page',
            'Control' => 'Checkbox'
        ),
        'Yaga.LeaderBoard.Limit' => array(
            'LabelCode' => 'Maximum number of leaders to show',
            'Control' => 'Textbox',
            'Options' => array(
                'Size' => 45,
                'class' => 'SmallInput'
            )
        )
    ));
    $this->ConfigurationModule = $ConfigModule;

    $this->Render('settings');
  }

  public function Import() {
    $this->Title(T('Yaga.Import'));
    $this->SetData('TransportType', 'Import');

    if($this->Form->IsPostBack() == FALSE) {
      // Nothing has been submitted
    }
    else {
      $FormValues = $this->Form->FormValues();
      $Sections = $FormValues['Checkboxes'];

      // Figure out which boxes were checked
      $Include = array();
      foreach($Sections as $Section) {
        $Include[$Section] = $FormValues[$Section];
      }

      // Handle the file upload
      $Upload = new Gdn_Upload();
      $TmpZip = $Upload->ValidateUpload('FileUpload', FALSE);

      $ZipFile = FALSE;
      if($TmpZip) {
        // Generate the target name
        $TargetFile = $Upload->GenerateTargetName(PATH_UPLOADS, 'zip');
        $BaseName = pathinfo($TargetFile, PATHINFO_BASENAME);

        // Save the uploaded zip
        $Parts = $Upload->SaveAs($TmpZip, $BaseName);
        $ZipFile = PATH_UPLOADS . DS . $Parts['SaveName'];
      }

      if(count($Include)) {
        if(class_exists('ZipArchive')) {
          $Info = $this->_ExtractZip($ZipFile);
          if($Info !== FALSE) {
            // Import the db entries
            foreach($Include as $Key => $Value) {
              decho($Key . ' => ' . $Value);
              if($Value) {
                $Data = unserialize(file_get_contents(PATH_UPLOADS . DS . 'import' . DS . 'yaga' . DS . $Info->$Key));
                // TODO: Change property members to singular form so I don't have to do this
                $Table = substr($Key, 0, -1);
                Gdn::SQL()->EmptyTable($Table);
                
                $ModelName = $Table . 'Model';
                $Model = Yaga::$ModelName();
                foreach($Data as $Datum) {
                  $Model->Insert((array)$Datum);
                }
              }
            }
            
            // Copy over the image files
            Gdn_FileSystem::Copy(PATH_UPLOADS . DS . 'import' . DS . 'yaga' . DS . 'images' . DS, PATH_UPLOADS . DS);
                       
            // Cleanup extracted files
            Gdn_FileSystem::RemoveFolder(PATH_UPLOADS . DS . 'import' . DS . 'yaga');
          }

          $this->SetData('TransportPath', $ZipFile);
          $this->Render('transport-success');
          return;
        }
        else {
          $this->Form->AddError('You do not seem to have the minimum requirements to import a Yaga configuration automatically. Please reference manual_transport.md for more information.');
        }
      }
      else {
        $this->Form->AddError('You must select at least one item to import.');
      }
    }

    $this->Render();
  }

  public function Export() {
    $this->Title(T('Yaga.Export'));
    $this->SetData('TransportType', 'Export');

    if($this->Form->IsPostBack()) {
      $FormValues = $this->Form->FormValues();
      $Sections = $FormValues['Checkboxes'];

      // Figure out which boxes were checked
      $Include = array();
      foreach($Sections as $Section) {
        $Include[$Section] = $FormValues[$Section];
      }
      if(count($Include)) {
        if(class_exists('ZipArchive')) {
          $Filename = $this->_ExportAuto($Include);
          $this->SetData('TransportPath', $Filename);
          $this->Render('transport-success');
          return;
        }
        else {
          $this->Form->AddError('You do not seem to have the minimum requirements to export your Yaga configuration automatically. Please reference manual_transport.md for more information.');
        }
      }
      else {
        $this->Form->AddError('You must select at least one item to export.');
      }

    }
    $this->Render();
  }

  protected function _ExportAuto($Include = array(), $Path = NULL) {
    $StartTime = microtime(TRUE);
    $Info = new stdClass();
    $Info->Version = C('Yaga.Version', '?.?');
    $Info->StartDate = date('Y-m-d H:i:s');

    if(is_null($Path)) {
      $Path = PATH_UPLOADS . DS . 'export' . date('Y-m-d His') . '.yaga.zip';
    }
    $FH = new ZipArchive();
    $Images = array();
    $Hashes = array();

    if($FH->open($Path, ZipArchive::CREATE) !== TRUE) {
      $this->Form->AddError('Unable to create archive: ' . $FH->getStatusString());
      return FALSE;
    }

    // Add actions
    if($Include['Actions']) {
      $Info->Actions = 'actions.yaga';
      $Actions = Yaga::ActionModel()->Get('Sort', 'asc');
      $ActionData = serialize($Actions);
      $FH->addFromString('actions.yaga', $ActionData);
      $Hashes[] = md5($ActionData);
    }

    // Add ranks and associated image
    if($Include['Ranks']) {
      $Info->Ranks = 'ranks.yaga';
      $Ranks = Yaga::RankModel()->Get('Level', 'asc');
      $RankData = serialize($Ranks);
      $FH->addFromString('ranks.yaga', $RankData);
      array_push($Images, C('Yaga.Ranks.Photo'), NULL);
      $Hashes[] = md5($RankData);
    }

    // Add badges and associated images
    if($Include['Badges']) {
      $Info->Badges = 'badges.yaga';
      $Badges = Yaga::BadgeModel()->Get();
      $BadgeData = serialize($Badges);
      $FH->addFromString('badges.yaga', $BadgeData);
      $Hashes[] = md5($BadgeData);
      foreach($Badges as $Badge) {
        array_push($Images, $Badge->Photo);
      }
    }

    // Add in any images
    $FilteredImages = array_filter($Images);
    if(count($FilteredImages)) {
      $FH->addEmptyDir('images');
    }

    foreach($FilteredImages as $Image) {
      if($FH->addFile(PATH_UPLOADS . DS . $Image, 'images' . DS . $Image) !== TRUE) {
        $this->Form->AddError('Unable to add file: ' . $FH->getStatusString());
        return FALSE;
      }
      $Hashes[] = md5_file(PATH_UPLOADS . DS . $Image);
    }

    // Save all the hashes
    sort($Hashes);
    $Info->MD5 = md5(implode(',', $Hashes));
    $Info->EndDate = date('Y-m-d H:i:s');

    $EndTime = microtime(TRUE);
    $TotalTime = $EndTime - $StartTime;
    $m = floor($TotalTime / 60);
    $s = $TotalTime - ($m * 60);

    $Info->ElapsedTime = sprintf('%02d:%02.2f', $m, $s);

    $FH->setArchiveComment(serialize($Info));
    if($FH->close()) {
      return $Path;
    }
    else {
      $this->Form->AddError('Unable to save archive: ' . $FH->getStatusString());
      return FALSE;
    }
  }

  protected function _ExtractZip($Filename) {
    if(!file_exists($Filename)) {
			return FALSE;
      // File does not exist
		}

    $Hashes = array();
    $ZipFile = new ZipArchive();
    $Result = $ZipFile->open($Filename);
    if($Result !== TRUE) {
      return FALSE;
      // Unable to open file
    }

    // Get the metadata from the comment
    $Comment = $ZipFile->comment;
    $MetaData = unserialize($Comment);
    
    $Result = $ZipFile->extractTo(PATH_UPLOADS . DS . 'import' . DS . 'yaga');
    if($Result !== TRUE) {
      return FALSE;
      // Unable to extract file
    }

    $ZipFile->close();

    // Validate checksum
    if($this->_ValidateChecksum($MetaData) === TRUE) {
      return $MetaData;
    }
    else {
      return FALSE;
      // Invalid checksum
    }
  }

  protected function _ValidateChecksum($MetaData) {
    $Hashes = array();
    // Hash the data files
    if(property_exists($MetaData, 'Actions')) {
      $Hashes[] = md5_file(PATH_UPLOADS . DS . 'import' . DS . 'yaga' . DS . $MetaData->Actions);
    }
    
    if(property_exists($MetaData, 'Badges')) {
      $Hashes[] = md5_file(PATH_UPLOADS . DS . 'import' . DS . 'yaga' . DS . $MetaData->Badges);
    }
    
    if(property_exists($MetaData, 'Ranks')) {
      $Hashes[] = md5_file(PATH_UPLOADS . DS . 'import' . DS . 'yaga' . DS . $MetaData->Ranks);
    }
    
    // Hash the image files
		$Files = $this->GetFiles(PATH_UPLOADS . DS . 'import' . DS . 'yaga' . DS . 'images');
		foreach($Files as $File) {
			$Hashes[] = md5_file($File);
		}
    
    sort($Hashes);
		$CalculatedChecksum = md5(implode(',', $Hashes));

		if($CalculatedChecksum != $MetaData->MD5) {
      return FALSE;
		}
		else {
      return TRUE;
		}
  }

  /**
	 * Returns a list of all Files and, optionally, Directories found in a
	 * directory.
	 *
	 * @param string Directory The directory to scan for files.
	 * @param int Recursive Indicates if the directory should be scanned recursively.
	 * @param int FilesOnly Indicates if resulting list should only contain files.
	 * @result array A list of Files and, optionally, Directories.
	 */
	protected function GetFiles($Directory, $Recursive = TRUE, $FilesOnly = TRUE) {
    $Files = array_diff(scandir($Directory), array('.', '..'));
    $Result = array();

    foreach($Files as $File) {
      $FileName = $Directory . '/' . $File;
      if(is_dir($FileName)) {
        // If Recursive flag is set, find files in subdirectories
        if($Recursive) {
          $Result = array_merge($Result, $this->GetFiles($FileName));
        }

        // If only files are expected, move to next one (i.e. don't add
        // directories) to the Result list
        if($FilesOnly) {
          continue;
        }
      }
      $Result[] = $FileName;
    }

    return $Result;
  }

}
