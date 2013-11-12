<?php if (!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * Cross table functions global to the Yaga application
 * 
 * Events:
 * 
 * @package Yaga
 * @since 1.0
 */

class YagaModel extends Gdn_Model {
  
  /**
   * Used as a cache for reactions
   * 
   * @var array
   */
  private static $_Reactions = array();
  
  /**
   * Used as a cache for available actions
   * @var DataSet
   */
  private static $_Actions = NULL;
  
  /**
   * Defines the related database table name.
   */
  public function __construct() {
    parent::__construct();
  }

  /**
   * Returns the total points a user has accumulated through out the application
   * 
   * @todo Move this to a calculated column
   * @param int $UserID
   * @return int
   */
  public function GetUserPoints($UserID) {
    $Points = 0;
    
    // Reaction Points
    $ActionPoints = $this->SQL
            ->Select('a.ActionID, a.Name, a.AwardValue, COUNT(*) Count')
            ->From('Action a')
            ->Join('Reaction r', 'a.ActionID = r.ActionID')
            ->Where('ParentAuthorID', $UserID)
            ->GroupBy('a.ActionID')
            ->Get()
            ->Result();
    
    foreach($ActionPoints as $Action) {
      $Points += $Action->AwardValue * $Action->Count;
    }
    
    // Badge Award Points
    
    $BadgePoints = $this->SQL
            ->Select('b.AwardValue')
            ->From('BadgeAward ba')
            ->Join('Badge b', 'ba.BadgeID = b.BadgeID')
            ->Where('ba.UserID', $UserID)
            ->Get()
            ->Result();
    
    foreach($BadgePoints as $Badge) {
      $Points += $Badge->AwardValue;
    }
    
    return $Points;
  }
}