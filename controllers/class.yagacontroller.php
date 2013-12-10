<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * This is the base class for controllers throughout the gamification applicati0n.
 * 
 * @since 1.0
 * @package Yaga
 */
class YagaController extends DashboardController {

  /**
   * @var array These objects will be created on instantiation and available via
   * $this->ObjectName
   */
  public $Uses = array('Database', 'Form');

  /**
   * Make this look like a dashboard page and add the resources
   *
   * @since 1.0
   * @access public
   */
  public function Initialize() {
    parent::Initialize();
    Gdn_Theme::Section('Dashboard');
    if ($this->Menu) {
      $this->Menu->HighlightRoute('/yaga/settings');
    }
    $this->AddJsFile('yaga.js');
    $this->AddCssFile('yaga.css');
  }

  /**
   * Convenience reroute to settings
   */
  public function Index() {
    $this->Settings();
  }
  
  /**
   * A simple configuration page for the Yaga Application
   */
  public function Settings() {
    $this->Permission('Garden.Settings.Manage');
    
    $ConfigModule = new ConfigurationModule($this);
    
    $ConfigModule->Initialize(array(
      'Yaga.Reactions.Enabled' => array('LabelCode' => 'Use Reactions', 'Control' => 'Checkbox'),
      'Yaga.Badges.Enabled' => array('LabelCode' => 'Use Badges', 'Control' => 'Checkbox'),
      'Yaga.Ranks.Enabled' => array('LabelCode' => 'Use Ranks', 'Control' => 'Checkbox'),
      'Yaga.LeaderBoard.Enabled' => array('LabelCode' => 'Show leaderboard on activity page', 'Control' => 'Checkbox'),
      'Yaga.LeaderBoard.Limit' => array('LabelCode' => 'Maximum number of leaders to show', 'Control' => 'Textbox', 'Options' => array('Size' => 45, 'class' => 'SmallInput'))
    ));
    $this->AddSideMenu('yaga/settings');
    $this->Title(T('Yaga.Settings'));
    $this->ConfigurationModule = $ConfigModule;
    $ConfigModule->RenderAll();
  }
}
