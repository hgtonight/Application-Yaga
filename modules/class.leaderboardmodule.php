<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * Renders a leaderboard in the panel detailing points earned of all time
 */
class LeaderBoardModule extends Gdn_Module {
  protected $Title = FALSE;

  public function __construct($Sender = '') {
    parent::__construct($Sender);
  }

  public function AssetTarget() {
    return 'Panel';
  }

  public function GetData($SlotType = 'a') {
    // Get the leaderboard data
    $Leaders = Gdn::SQL()
            ->Select('up.Points as YagaPoints, u.*')
            ->From('User u')
            ->Join('UserPoints up', 'u.UserID = up.UserID')
            ->Where('up.SlotType', $SlotType)
            ->Where('up.TimeSlot', gmdate('Y-m-d', Gdn_Statistics::TimeSlotStamp($SlotType)))
            ->Where('up.Source', 'Total')
            ->OrderBy('up.Points', 'desc')
            ->Limit(C('Yaga.LeaderBoard.Limit', 10), 0)
            ->Get()
            ->Result();

    $this->Data = $Leaders;
    switch($SlotType) {
      case 'a':
        $this->Title = T('Yaga.LeaderBoard.AllTime');
        break;
      case 'w':
        $this->Title = T('Yaga.LeaderBoard.Week');
        break;
      case 'm':
        $this->Title = T('Yaga.LeaderBoard.Month');
        break;
      case 'y':
        $this->Title = T('Yaga.LeaderBoard.Year');
        break;
    }

  }

  public function ToString() {
    if(!$this->Data && !$this->Title) {
      $this->GetData();
    }

    if($this->Visible && count($this->Data)) {
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
