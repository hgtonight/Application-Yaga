<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * This handles all the core settings for the gamification application.
 * 
 * @since 1.0
 * @package Yaga
 */
class YagaSettingsController extends DashboardController {

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
    if ($this->Menu) {
      $this->Menu->HighlightRoute('/yagasettings');
    }
    //$this->AddJsFile('yaga.js');
    $this->AddCssFile('yaga.css');
  }

  /**
   * A simple configuration page for the Yaga Application
   */
  public function Index() {
    $this->Permission('Garden.Settings.Manage');
    
    $ConfigModule = new ConfigurationModule($this);
    
    $ConfigModule->Initialize(array(
      'Yaga.Reactions.Enabled' => array('LabelCode' => 'Use Reactions', 'Control' => 'Checkbox'),
      'Yaga.Badges.Enabled' => array('LabelCode' => 'Use Badges', 'Control' => 'Checkbox'),
      'Yaga.Ranks.Enabled' => array('LabelCode' => 'Use Ranks', 'Control' => 'Checkbox'),
      'Yaga.LeaderBoard.Enabled' => array('LabelCode' => 'Show leaderboard on activity page', 'Control' => 'Checkbox'),
      'Yaga.LeaderBoard.Limit' => array('LabelCode' => 'Maximum number of leaders to show', 'Control' => 'Textbox', 'Options' => array('Size' => 45, 'class' => 'SmallInput'))
    ));
    $this->AddSideMenu('yagasettings');
    $this->Title(T('Yaga.Settings'));
    $this->ConfigurationModule = $ConfigModule;
    
    $ConfigModule->RenderAll();
  }
}
