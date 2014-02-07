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
    // Todo: Implement
    $this->Render('transport');
  }

  public function Export() {
    $this->Title(T('Yaga.Export'));
    
    
    if(class_exists('ZipArchive')) {
      $this->_ExportAuto();
    }
    else {
      $this->Form->AddError('You do not seem to have the minimum requirements to export your Yaga configuration automatically. Please reference manual_export.md for more information.');
    }

    $this->Render('transport');
  }
  
  protected function _ExportAuto($Path = NULL) {
    $StartTime = microtime(TRUE);
    $Info = 'Yaga Export: ' . C('Yaga.Version', '??');
    $Info .= "\nStart Date: " . date('Y-m-d H:i:s');
    
    
    if(is_null($Path)) {
      $Path = PATH_UPLOADS . DS . 'export' . date('Y-m-d His') . '.yaga.zip';
    }
    $FH = new ZipArchive();
    
    if($FH->open($Path, ZipArchive::CREATE) != TRUE) {
      $this->Form->AddError('Unable to create archive; please check permissions at ' . $Path);
      return FALSE;
    }
    
    $Actions = Yaga::ActionModel()->Get('Sort', 'asc');
    $Ranks = Yaga::RankModel()->Get('Level', 'asc');
    $Badges = Yaga::BadgeModel()->Get();

    $FH->addFromString('actions.yaga', serialize($Actions));
    $FH->addFromString('badges.yaga', serialize($Badges));
    $FH->addFromString('ranks.yaga', serialize($Ranks));

    $Images = array();
    array_push($Images, C('Yaga.Ranks.Photo'), NULL);
    foreach($Badges as $Badge) {
      array_push($Images, $Badge->Photo);
    }
    
    foreach($Images as $Image) {
      if(!is_null($Image)) {
        if($FH->addFile(PATH_UPLOADS . DS . $Image, 'images' . DS . $Image) != TRUE) {
          $this->Form->AddError('Unable to add file: ' . PATH_UPLOADS . DS . $Image);
          return FALSE;
        }
      }
    }
    
    $Info .= "\nEnd Date: " . date('Y-m-d H:i:s');
    
    $EndTime = microtime(TRUE);
    $TotalTime = $EndTime - $StartTime;
    $m = floor($TotalTime / 60);
    $s = $TotalTime - ($m * 60);

    $Info .= "\n" . sprintf('Elapsed Time: %02d:%02.2f', $m, $s);
    
    $FH->addFromString('info.yaga', $Info);
    if($FH->close()) {
      return TRUE;
    }
    else {
      $this->Form->AddError('Unable to save archive: ' . $FH->getStatusString());
      return FALSE;
    }
  }
}
