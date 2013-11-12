<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * Handles the configuration of the Yaga Application
 * 
 * @todo Consider moving to the Yaga controller?
 * @since 1.0
 * @package Yaga
 */
class ConfigureController extends DashboardController {

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
      $this->Menu->HighlightRoute('/yaga/configure');
    }
    $this->AddJsFile('yaga.js');
    $this->AddCssFile('yaga.css');
  }

  /**
   * Convenience reroute to yaga
   */
  public function Index() {
    $this->Yaga();
  }
  
  /**
   * A simple configuration page for the Yaga Application
   */
  public function Yaga() {
    $this->Permission('Garden.Settings.Manage');
    
    $ConfigModule = new ConfigurationModule($this);
    
    $ConfigModule->Initialize(array(
      'Yaga.Reactions.Enabled' => array('LabelCode' => 'Use Reactions', 'Control' => 'Checkbox'),
      'Yaga.Badges.Enabled' => array('LabelCode' => 'Use Badges', 'Control' => 'Checkbox'),
      'Yaga.Ranks.Enabled' => array('LabelCode' => 'Use Ranks', 'Control' => 'Checkbox')
    ));
    $this->AddSideMenu('configure');
    $this->SetData('Title', 'Gamification Settings');
    $this->ConfigurationModule = $ConfigModule;
    $ConfigModule->RenderAll();
  }
}
