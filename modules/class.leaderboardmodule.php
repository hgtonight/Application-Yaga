<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * Renders a leaderboard in the panel detailing points earned of all time
 * 
 * @package Yaga
 * @since 1.0
 */
class LeaderBoardModule extends Gdn_Module {
  
  /**
   * Holds the title of the module.
   * 
   * @var string
   */
  protected $Title = FALSE;

  
  /**
   * Don't do anything special on construct.
   * 
   * @param string $Sender
   */
  public function __construct($Sender = '') {
    parent::__construct($Sender);
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
   * Load up the leaderboard module data based on a specific time slot
   * 
   * @param string $SlotType Valid options are 'a': All Time, 'w': Weekly, 'm':
   * Monthly, 'y': Yearly
   */
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

  /**
   * Renders the leaderboard.
   * 
   * @return string
   */
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
