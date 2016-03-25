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
  public $Title = FALSE;
  
  /**
   * Holds the slot type of the module.
   * 
   * @var string Valid options are 'a': All Time, 'w': Weekly, 'm':
   * Monthly, 'y': Yearly 
   */
  public $SlotType = 'a';

  
  /**
   * Set the application folder on construct.
   */
  public function __construct($Sender = '') {
    parent::__construct($Sender, 'yaga');
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
   * Set the slot type of the leaderboard. Defaults to 'a' for all time.
   * 
   * @param string $SlotType Valid options are 'a': All Time, 'w': Weekly, 'm':
   * Monthly, 'y': Yearly
   */
  public function GetData() {
    switch(strtolower($this->SlotType)) {
      case 'w':
        $this->Title = T('Yaga.LeaderBoard.Week');
        $slot = 'w';
        break;
      case 'm':
        $this->Title = T('Yaga.LeaderBoard.Month');
        $slot = 'm';
        break;
      case 'y':
        $this->Title = T('Yaga.LeaderBoard.Year');
        $slot = 'y';
        break;
      default:
      case 'a':
        $this->Title = T('Yaga.LeaderBoard.AllTime');
        $slot = 'a';
        break;
    }

    // Get the leaderboard data
    $Leaders = Gdn::SQL()
            ->Select('up.Points as YagaPoints, u.*')
            ->From('User u')
            ->Join('UserPoints up', 'u.UserID = up.UserID')
            ->Where('u.Banned', 0)
            ->Where('u.Deleted', 0)
            ->Where('up.SlotType', $slot)
            ->Where('up.TimeSlot', gmdate('Y-m-d', Gdn_Statistics::TimeSlotStamp($slot)))
            ->Where('up.Source', 'Total')
            ->OrderBy('up.Points', 'desc')
            ->Limit(C('Yaga.LeaderBoard.Limit', 10), 0)
            ->Get()
            ->Result();

    $this->Data = $Leaders;
  }

  /**
   * Renders the leaderboard.
   * 
   * @return string
   */
  public function ToString() {
    $this->GetData();
    
    if(count($this->Data)) {
      return parent::ToString();
    }
    return '';
  }

}
