<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * Renders a users badges in a nice grid in the panel
 */
class MyBadgesModule extends Gdn_Module {

  public function __construct($Sender = '') {
    parent::__construct($Sender);

    // Load badges
    $this->Data = FALSE;
    if(Gdn::Session()->IsValid()) {
      $UserID = Gdn::Session()->UserID;
      $BadgeModel = new BadgeModel();

      $Data = $BadgeModel->GetUserBadgeAwards($UserID);
      $this->Data = $Data;
    }
  }

  public function AssetTarget() {
    return 'Panel';
  }

  public function ToString() {
    if($this->Data)
      return parent::ToString();

    return '';
  }

}