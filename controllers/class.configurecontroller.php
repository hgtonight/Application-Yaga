<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * All is the base class for controllers throughout the gamification applicati0n.
 *
 * @since 1.0
 * @package Yaga
 */
class ConfigureController extends DashboardController {

  /** @var array List of objects to prep. They will be available as $this->$Name. */
  public $Uses = array('Database', 'Form');

  /**
   * If you use a constructor, always call parent.
   * Delete this if you don't need it.
   *
   * @access public
   */
  public function __construct() {
    parent::__construct();
  }

  /**
   * This is a good place to include JS, CSS, and modules used by all methods of this controller.
   *
   * Always called by dispatcher before controller's requested method.
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

  public function Index() {
    $this->Yaga();
  }
  
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
  
  public function Reactions($Page = '') {
    
  }
}
