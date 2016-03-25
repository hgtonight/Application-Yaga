<?php if(!defined('APPLICATION')) exit();
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
    $this->removeCssFile('magnific-popup.css');
  }

  /**
   * Redirect to settings by default
   * 
   * @since 1.0
   */
  public function Index() {
    $this->Settings();
  }

  /**
   * This handles all the core settings for the gamification application.
   * 
   * @since 1.0
   */
  public function Settings() {
    $this->Permission('Garden.Settings.Manage');
    $this->Title(T('Yaga.Settings'));

    // Get list of actions from the model and pass to the view
    $ConfigModule = new ConfigurationModule($this);

    $ConfigModule->Initialize(array(
        'Yaga.Reactions.Enabled' => array(
            'LabelCode' => 'Yaga.Reactions.Use',
            'Control' => 'Checkbox'
        ),
        'Yaga.Badges.Enabled' => array(
            'LabelCode' => 'Yaga.Badges.Use',
            'Control' => 'Checkbox'
        ),
        'Yaga.Ranks.Enabled' => array(
            'LabelCode' => 'Yaga.Ranks.Use',
            'Control' => 'Checkbox'
        ),
        'Yaga.MenuLinks.Show' => array(
            'LabelCode' => 'Yaga.MenuLinks.Show',
            'Control' => 'Checkbox'
        ),
        'Yaga.LeaderBoard.Enabled' => array(
            'LabelCode' => 'Yaga.LeaderBoard.Use',
            'Control' => 'Checkbox'
        ),
        'Yaga.LeaderBoard.Limit' => array(
            'LabelCode' => 'Yaga.LeaderBoard.Max',
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
  
  /**
   * Performs the necessary functions to change a backend controller into a
   * frontend controller
   * 
   * @since 1.1
   */
  private function FrontendStyle() {
    $this->RemoveCssFile('admin.css');
    unset($this->Assets['Panel']['SideMenuModule']);
    $this->AddCssFile('style.css');
    $this->MasterView = 'default';
    
    $WeeklyModule = new LeaderBoardModule();
    $WeeklyModule->SlotType = 'w';
    $this->AddModule($WeeklyModule);
    $AllTimeModule = new LeaderBoardModule();
    $this->AddModule($AllTimeModule);
  }
  
  /**
   * Displays a summary of ranks currently configured on a frontend page to help
   * users understand what is valued in this community
   * 
   * @since 1.1
   */
  public function Ranks() {
    $this->permission('Yaga.Ranks.View');
    $this->FrontendStyle();
    $this->AddCssFile('ranks.css');
    $this->Title(T('Yaga.Ranks.All'));

    // Get list of ranks from the model and pass to the view
    $this->SetData('Ranks', Yaga::RankModel()->Get());
    
    $this->Render('ranks');
  }
  
  /**
   * Displays a summary of badges currently configured on a frontend page to
   * help users understand what is valued in this community.
   * 
   * Also provides a convenience redirect to badge details
   * 
   * @param int $BadgeID The badge ID you want to see details
   * @param string $Slug The badge slug you want to see details
   * @since 1.1
   */
  public function Badges($BadgeID = FALSE, $Slug = NULL) {
    $this->permission('Yaga.Badges.View');
    $this->FrontendStyle();
    $this->AddCssFile('badges.css');
    $this->AddModule('BadgesModule');
    
    if(is_numeric($BadgeID)) {
      return $this->BadgeDetail($BadgeID, $Slug);
    }
    
    $this->Title(T('Yaga.Badges.All'));

    // Get list of badges from the model and pass to the view
    $UserID = Gdn::Session()->UserID;
    $AllBadges = Yaga::BadgeModel()->GetWithEarned($UserID);
    $this->SetData('Badges', $AllBadges);

    $this->Render('badges');
  }
  
  /**
   * Displays information about the specified badge including recent recipients 
   * of the badge.
   * 
   * @param int $BadgeID
   * @param string $Slug
   */
  public function BadgeDetail($BadgeID, $Slug = NULL) {
    $this->permission('Yaga.Badges.View');
    $Badge = Yaga::BadgeModel()->GetByID($BadgeID);
    
    if(!$Badge) {
      throw NotFoundException('Badge');
    }

    $UserID = Gdn::Session()->UserID;
    $BadgeAwardModel = Yaga::BadgeAwardModel();
    $AwardCount = $BadgeAwardModel->GetCount($BadgeID);
    $UserBadgeAward = $BadgeAwardModel->Exists($UserID, $BadgeID);
    $RecentAwards = $BadgeAwardModel->GetRecent($BadgeID);

    
    $this->SetData('AwardCount', $AwardCount);
    $this->SetData('RecentAwards', $RecentAwards);
    $this->SetData('UserBadgeAward', $UserBadgeAward);
    $this->SetData('Badge', $Badge);

    $this->Title(T('Yaga.Badge.View') . $Badge->Name);

    $this->Render('badgedetail');
  }

  /**
   * Import a Yaga transport file
   * 
   * @since 1.0
   */
  public function Import() {
    $this->Title(T('Yaga.Import'));
    $this->SetData('TransportType', 'Import');
    
    if(!class_exists('ZipArchive')) {
      $this->Form->AddError(T('Yaga.Error.TransportRequirements'));
    }
    
    if($this->Form->IsPostBack() == TRUE) {
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
        $this->SetData('TransportPath', $ZipFile);
      }

      $Include = $this->_FindIncludes();
      if(count($Include)) {
        $Info = $this->_ExtractZip($ZipFile);
        $this->_ImportData($Info, $Include);
        Gdn_FileSystem::RemoveFolder(PATH_UPLOADS . DS . 'import' . DS . 'yaga');
      }
      else {
        $this->Form->AddError(T('Yaga.Error.Includes'));
      }
    }
    
    if($this->Form->ErrorCount() == 0 && $this->Form->IsPostBack()) {
      $this->Render('transport-success');
    }
    else {
      $this->Render();
    }
  }

  /**
   * Create a Yaga transport file
   * 
   * @since 1.0
   */
  public function Export() {
    $this->Title(T('Yaga.Export'));
    $this->SetData('TransportType', 'Export');

    if(!class_exists('ZipArchive')) {
      $this->Form->AddError(T('Yaga.Error.TransportRequirements'));
    }

    if($this->Form->IsPostBack()) {
      $Include = $this->_FindIncludes();
      if(count($Include)) {
        $Filename = $this->_ExportData($Include);
        $this->SetData('TransportPath', $Filename);
      }
      else {
        $this->Form->AddError(T('Yaga.Error.Includes'));
      }
    }

    if($this->Form->ErrorCount() == 0 && $this->Form->IsPostBack()) {
      $this->Render('transport-success');
    }
    else {
      $this->Render();
    }
  }

  /**
   * This searches through the submitted checkboxes and constructs an array of
   * Yaga sections to be included in the transport file.
   * 
   * @return array
   * @since 1.0
   */
  protected function _FindIncludes() {
    $FormValues = $this->Form->FormValues();
    $Sections = $FormValues['Checkboxes'];

    // Figure out which boxes were checked
    $Include = array();
    foreach($Sections as $Section) {
      $Include[$Section] = $FormValues[$Section];
    }
    return $Include;
  }
  
  /**
   * Creates a transport file for easily transferring Yaga configurations across
   * installs
   *
   * @param array An array containing the config areas to transfer
   * @param string Where to save the transport file
   * @return mixed False on failure, the path to the transport file on success
   * @since 1.0
   */
  protected function _ExportData($Include = array(), $Path = NULL) {
    $StartTime = microtime(TRUE);
    $Info = new stdClass();
    $Info->Version = C('Yaga.Version', '?.?');
    $Info->StartDate = date('Y-m-d H:i:s');

    if(is_null($Path)) {
      $Path = PATH_UPLOADS . DS . 'export' . date('Y-m-d-His') . '.yaga.zip';
    }
    $FH = new ZipArchive();
    $Images = array();
    $Hashes = array();

    if($FH->open($Path, ZipArchive::CREATE) !== TRUE) {
      $this->Form->AddError(sprintf(T('Yaga.Error.ArchiveCreate'), $FH->getStatusString()));
      return FALSE;
    }

    // Add configuration items
    $Info->Config = 'configs.yaga';
    $Configs = Gdn::Config('Yaga', array());
    unset($Configs['Version']);
    $ConfigData = serialize($Configs);
    $FH->addFromString('configs.yaga', $ConfigData);
    $Hashes[] = md5($ConfigData);
    
    // Add actions
    if($Include['Action']) {
      $Info->Action = 'actions.yaga';
      $Actions = Yaga::ActionModel()->Get('Sort', 'asc');
      $this->SetData('ActionCount', count($Actions));
      $ActionData = serialize($Actions);
      $FH->addFromString('actions.yaga', $ActionData);
      $Hashes[] = md5($ActionData);
    }

    // Add ranks and associated image
    if($Include['Rank']) {
      $Info->Rank = 'ranks.yaga';
      $Ranks = Yaga::RankModel()->Get('Level', 'asc');
      $this->SetData('RankCount', count($Ranks));
      $RankData = serialize($Ranks);
      $FH->addFromString('ranks.yaga', $RankData);
      array_push($Images, C('Yaga.Ranks.Photo'), NULL);
      $Hashes[] = md5($RankData);
    }

    // Add badges and associated images
    if($Include['Badge']) {
      $Info->Badge = 'badges.yaga';
      $Badges = Yaga::BadgeModel()->Get();
      $this->SetData('BadgeCount', count($Badges));
      $BadgeData = serialize($Badges);
      $FH->addFromString('badges.yaga', $BadgeData);
      $Hashes[] = md5($BadgeData);
      foreach($Badges as $Badge) {
        array_push($Images, $Badge->Photo);
      }
    }

    // Add in any images
    $FilteredImages = array_filter($Images);
    $ImageCount = count($FilteredImages);
    $this->SetData('ImageCount', $ImageCount);
    if($ImageCount > 0) {
      $FH->addEmptyDir('images');
    }

    foreach($FilteredImages as $Image) {
      if($FH->addFile('.' . $Image, 'images/' . $Image) === FALSE) {
        $this->Form->AddError(sprintf(T('Yaga.Error.AddFile'), $FH->getStatusString()));
        //return FALSE;
      }
      $Hashes[] = md5_file('.' . $Image);
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
      $this->Form->AddError(sprintf(T('Yaga.Error.ArchiveSave'), $FH->getStatusString()));
      return FALSE;
    }
  }

  /**
   * Extract the transport file and validate
   *
   * @param string The transport file path
   * @return boolean Whether or not the transport file was extracted successfully
   * @since 1.0
   */
  protected function _ExtractZip($Filename) {
    if(!file_exists($Filename)) {
      $this->Form->AddError(T('Yaga.Error.FileDNE'));
			return FALSE;
		}

    $ZipFile = new ZipArchive();
    $Result = $ZipFile->open($Filename);
    if($Result !== TRUE) {
      $this->Form->AddError(T('Yaga.Error.ArchiveOpen'));
      return FALSE;
    }

    // Get the metadata from the comment
    $Comment = $ZipFile->comment;
    $MetaData = unserialize($Comment);

    $Result = $ZipFile->extractTo(PATH_UPLOADS . DS . 'import' . DS . 'yaga');
    if($Result !== TRUE) {
      $this->Form->AddError(T('Yaga.Error.ArchiveExtract'));
      return FALSE;
    }

    $ZipFile->close();

    // Validate checksum
    if($this->_ValidateChecksum($MetaData) === TRUE) {
      return $MetaData;
    }
    else {
      $this->Form->AddError(T('Yaga.Error.ArchiveChecksum'));
      return FALSE;
    }
  }

  /**
   * Overwrites Yaga configurations, dumps Yaga db tables, inserts data via the 
   * model, and copies uploaded files to the server
   * 
   * @param stdClass The info object read in from the archive
   * @param array Which tables should be overwritten
   * @return bool Pass/Fail on the import being executed. Errors can exist on the
   * form with a passing return value.
   * @since 1.0
   */
  protected function _ImportData($Info, $Include) {
    if(!$Info) {
      return FALSE;
    }
    
    // Import Configs
    $Configs = unserialize(file_get_contents(PATH_UPLOADS . DS . 'import' . DS . 'yaga' . DS . $Info->Config));
    $Configurations = $this->_NestedToDotNotation($Configs, 'Yaga');
    foreach($Configurations as $Name => $Value) {
      SaveToConfig($Name, $Value);
    }
    
    // Import model data
    foreach($Include as $Key => $Value) {
      if($Value) {
        $Data = unserialize(file_get_contents(PATH_UPLOADS . DS . 'import' . DS . 'yaga' . DS . $Info->$Key));
        Gdn::SQL()->EmptyTable($Key);
        $ModelName = $Key . 'Model';
        $Model = Yaga::$ModelName();
        foreach($Data as $Datum) {
          $Model->Insert((array)$Datum);
        }
        $this->SetData($Key . 'Count', $Model->GetCount());
      }
    }
    
    // Import uploaded files
    if(Gdn_FileSystem::Copy(PATH_UPLOADS . DS . 'import' . DS . 'yaga' . DS . 'images' . DS . 'uploads' . DS, PATH_UPLOADS . DS) === FALSE) {
      $this->Form->AddError(T('Yaga.Error.TransportCopy'));
    }
    
    return TRUE;
  }
  
  /**
   * Converted a nest config array into an array where indexes are the configuration
   * strings and the value is the value
   * 
   * @param array The nested array
   * @param string What should the configuration strings be prefixed with
   * @return array
   * @since 1.0
   */
  protected function _NestedToDotNotation($Configs, $Prefix = '') {
    $ConfigStrings = array();
    
    foreach($Configs as $Name => $Value) {
      if(is_array($Value)) {
        $ConfigStrings = array_merge($ConfigStrings, $this->_NestedToDotNotation($Value, "$Prefix.$Name"));
      }
      else {
        $ConfigStrings["$Prefix.$Name"] = $Value;
      }
    }
    
    return $ConfigStrings;
  }

  /**
   * Inspects the Yaga transport files and calculates a checksum
   *
   * @param stdClass The metadata object read in from the transport file
   * @return boolean Whether or not the checksum is valid
   * @since 1.0
   */
  protected function _ValidateChecksum($MetaData) {
    $Hashes = array();
    
    // Hash the config file
    $Hashes[] = md5_file(PATH_UPLOADS . DS . 'import' . DS . 'yaga' . DS . $MetaData->Config);
    
    // Hash the data files
    if(property_exists($MetaData, 'Action')) {
      $Hashes[] = md5_file(PATH_UPLOADS . DS . 'import' . DS . 'yaga' . DS . $MetaData->Action);
    }

    if(property_exists($MetaData, 'Badge')) {
      $Hashes[] = md5_file(PATH_UPLOADS . DS . 'import' . DS . 'yaga' . DS . $MetaData->Badge);
    }

    if(property_exists($MetaData, 'Rank')) {
      $Hashes[] = md5_file(PATH_UPLOADS . DS . 'import' . DS . 'yaga' . DS . $MetaData->Rank);
    }

    // Hash the image files
		$Files = $this->_GetFiles(PATH_UPLOADS . DS . 'import' . DS . 'yaga' . DS . 'images');
    $this->SetData('ImageCount', count($Files));
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
   * Returns a list of all files in a directory, recursively (Thanks @businessdad)
   *
   * @param string Directory The directory to scan for files
   * @return array A list of Files and, optionally, Directories.
   * @since 1.0
   */
  protected function _GetFiles($Directory) {
    $Files = array_diff(scandir($Directory), array('.', '..'));
    $Result = array();
    foreach($Files as $File) {
      $FileName = $Directory . '/' . $File;
      if(is_dir($FileName)) {
        $Result = array_merge($Result, $this->_GetFiles($FileName));
        continue;
      }
      $Result[] = $FileName;
    }
    return $Result;
  }

}
