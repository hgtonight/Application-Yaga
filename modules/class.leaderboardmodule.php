<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * Renders a leaderboard in the panel detailing points earned of all time
 */
class LeaderBoardModule extends Gdn_Module {

  public function __construct($Sender = '') {
    parent::__construct($Sender);

    // Get the leaderboard data
    $UserModel = new UserModel();
    $this->Data = $UserModel->GetWhere(FALSE, 'Points', 'desc', 10, 0);
    $this->Title = T('All Time Leaders');
  }

  public function AssetTarget() {
    return 'Panel';
  }

  public function ToString() {
    if($this->Data) {
      if($this->Visible) {
        $ViewPath = $this->FetchViewLocation('leaderboard', 'yaga');
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