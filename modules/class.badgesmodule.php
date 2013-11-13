<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * Renders a users badges in a nice grid in the panel
 */
class BadgesModule extends Gdn_Module {

  public function __construct($Sender = '') {
    parent::__construct($Sender);

    // default to the user object on the controller/the currently logged in user
    if(property_exists($Sender, 'User')) {
      $UserID = $Sender->User->UserID;
    }
    else {
      $UserID = Gdn::Session()->UserID;
    }
    $BadgeModel = new BadgeModel();
    $this->Data = $BadgeModel->GetUserBadgeAwards($UserID);
  }

  public function AssetTarget() {
    return 'Panel';
  }

  public function ToString() {
    if($this->Data) {
      if($this->Visible) {
        $ViewPath = $this->FetchViewLocation('badges', 'yaga');
        $String = '';
        ob_start();
        include ($ViewPath);
        $String = ob_get_contents();
        @ob_end_clean();
        return $String;
      }
    }
    return '';
  }

}