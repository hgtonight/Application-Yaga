<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * Renders a leaderboard in the panel detailing points earned of all time
 */
class LeaderBoardModule extends Gdn_Module {

  public function __construct($Sender = '') {
    parent::__construct($Sender);
  }

  public function AssetTarget() {
    return 'Panel';
  }

  public function GetData($SlotType = 'a') {
    // Get the leaderboard data
    $Leaders = Gdn::SQL()
            ->Select('up.Points as Points, u.*')
            ->From('User u')
            ->Join('UserPoints up', 'u.UserID = up.UserID')
            ->Where('up.SlotType', $SlotType)
            ->Where('up.Source', 'Total')
            ->OrderBy('up.Points', 'desc')
            ->Limit(10, 0)
            ->Get()
            ->Result();
    
    $this->Data = $Leaders;
    switch($SlotType) {
      case 'a':
        $this->Title = T('All Time Leaders');
        break;
      case 'w':
        $this->Title = T("This Week's Leaders");
        break;
      case 'm':
        $this->Title = T("This Month's Leaders");
        break;
      case 'y':
        $this->Title = T("This Years's Leaders");
        break;
    }
    
  }
  
  public function ToString() {
    if(!$this->Data) {
      $this->GetData();      
    }
    
    if($this->Visible) {
      $ViewPath = $this->FetchViewLocation('leaderboard', 'yaga');
      $String = '';
      ob_start();
      include ($ViewPath);
      $String = ob_get_contents();
      @ob_end_clean();
      return $String;
    }
    return '';
  }

}