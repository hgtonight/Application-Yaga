<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * Renders a user's badges in a nice grid in the panel
 * 
 * @package Yaga
 * @since 1.0
 */
class BadgesModule extends Gdn_Module {

  /**
   * Retrieves the user's badgelist upon construction of the module object.
   * 
   * @param string $Sender
   */
  public function __construct($Sender = '') {
    parent::__construct($Sender);

    // default to the user object on the controller/the currently logged in user
    if(property_exists($Sender, 'User')
            && $Sender->User) {
      $UserID = $Sender->User->UserID;
    }
    else {
      $UserID = Gdn::Session()->UserID;
    }

    if(Gdn::Session()->UserID == $UserID) {
      $this->Title = T('Yaga.Badges.Mine');
    }
    else {
      $this->Title = T('Yaga.Badges');
    }

    $BadgeAwardModel = Yaga::BadgeAwardModel();
    $this->Data = $BadgeAwardModel->GetByUser($UserID);
  }

  /**
   * Specifies the asset this module should be rendered to.
   * 
   * @return string
   */
  public function AssetTarget() {
    return 'Panel';
  }

  /**
   * Renders a badge list in a nice little box.
   * 
   * @return string
   */
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
