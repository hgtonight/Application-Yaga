<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * This is all the frontend pages dealing with badges
 *
 * @since 1.0
 * @package Yaga
 */
class BadgesController extends Gdn_Controller {

  /**
   * @var array These objects will be created on instantiation and available via
   * $this->ObjectName
   */
  public $Uses = array('BadgeModel');

  public function Initialize() {
    parent::Initialize();
    $this->Head = new HeadModule($this);
    $this->AddJsFile('jquery.js');
    $this->AddJsFile('jquery-ui.js');
    $this->AddJsFile('jquery.livequery.js');
    $this->AddJsFile('jquery.popup.js');
    $this->AddJsFile('global.js');
    $this->AddCssFile('style.css');
    $this->AddCssFile('badges.css');
    $this->AddModule('BadgesModule');
  }

  /**
   * Render a blank page if no methods were specified in dispatch
   */
  public function Index() {
    //$this->Render('Blank', 'Utility', 'Dashboard');
    $this->All();
  }
  
  /**
   * This renders out the full list of badges
   */
  public function All() {
    $this->Title(T('All Badges'));

    $UserID = Gdn::Session()->UserID;
    // Get list of badges from the model and pass to the view
    $this->SetData('Badges', $this->BadgeModel->GetAllBadgesUserAwards($UserID));
    
    // TODO: Add leaderboard module
    
    $this->Render('all');
  }
  
  /**
   * Show some facets about a specific badge
   * 
   * @param int $BadgeID
   * @param string $Slug
   */
  public function Detail($BadgeID, $Slug = NULL) {
    $UserID = Gdn::Session()->UserID;
    
    $Badges = $this->BadgeModel->GetBadgesToCheckForUser($UserID);
    decho($Badges);
    
    $this->Render('Blank', 'Utility', 'Dashboard');
  }
}
