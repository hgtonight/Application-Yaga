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
        'Control'   => 'Checkbox'
      ),
      'Yaga.Badges.Enabled' => array(
        'LabelCode' => 'Use Badges',
        'Control'   => 'Checkbox'
      ),
      'Yaga.Ranks.Enabled' => array(
        'LabelCode' => 'Use Ranks',
        'Control'   => 'Checkbox'
      ),
      'Yaga.LeaderBoard.Enabled' => array(
        'LabelCode' => 'Show leaderboard on activity page',
        'Control'   => 'Checkbox'
      ),
      'Yaga.LeaderBoard.Limit' => array(
        'LabelCode' => 'Maximum number of leaders to show',
        'Control'   => 'Textbox',
        'Options'   => array(
          'Size'  => 45,
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
    // Todo: Implement
    $this->Render('transport');
  }
}
