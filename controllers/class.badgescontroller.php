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
  public $Uses = array('BadgeModel', 'BadgeAwardModel');

  /**
   * This sets the badges controller up to look like a front end page. It also
   * adds some leader board modules.
   */
  public function Initialize() {
    parent::Initialize();
    $this->Application = 'Yaga';
    $this->Head = new HeadModule($this);
    $this->AddJsFile('jquery.js');
    $this->AddJsFile('jquery-ui.js');
    $this->AddJsFile('jquery.livequery.js');
    $this->AddJsFile('jquery.popup.js');
    $this->AddJsFile('global.js');
    $this->AddCssFile('style.css');
    $this->AddCssFile('badges.css');
    $this->AddModule('BadgesModule');
    $Module = new LeaderBoardModule();
    $Module->SlotType = 'w';
    $this->AddModule($Module);
    $Module = new LeaderBoardModule();
    $this->AddModule($Module);
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
    $this->Title(T('Yaga.Badges.All'));

    $UserID = Gdn::Session()->UserID;

    // Get list of badges from the model and pass to the view
    $AllBadges = $this->BadgeModel->GetWithEarned($UserID);

    $this->SetData('Badges', $AllBadges);
    //$this->SetData('Earned')

    $this->Render('all');
  }

  /**
   * Show some facets about a specific badge
   *
   * @param int $BadgeID
   * @param string $Slug
   * @throws NotFoundException
   */
  public function Detail($BadgeID, $Slug = NULL) {
    $UserID = Gdn::Session()->UserID;
    $Badge = $this->BadgeModel->GetByID($BadgeID);
    $AwardCount = $this->BadgeAwardModel->GetCount($BadgeID);
    $UserBadgeAward = $this->BadgeAwardModel->Exists($UserID, $BadgeID);
    $RecentAwards = $this->BadgeAwardModel->GetRecent($BadgeID);

    if(!$Badge) {
      throw NotFoundException('Badge');
    }

    $this->SetData('AwardCount', $AwardCount);
    $this->SetData('RecentAwards', $RecentAwards);
    $this->SetData('UserBadgeAward', $UserBadgeAward);
    $this->SetData('Badge', $Badge);

    $this->Title(T('Yaga.Badge.View') . $Badge->Name);

    $this->Render();
  }
}
