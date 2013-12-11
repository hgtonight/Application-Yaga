<?php if (!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * Describes badges and the associated rule criteria
 *
 * Events:
 *
 * @package Yaga
 * @since 1.0
 */

class BadgeModel extends Gdn_Model {

  /**
   * Used as a cache
   * @var DataSet
   */
  private static $_Badges = NULL;

  /**
   * Defines the related database table name.
   */
  public function __construct() {
    parent::__construct('Badge');
  }

  /**
   * Returns a list of all badges
   *
   * @return DataSet
   */
  public function Get() {
    if(empty(self::$_Badges)) {
      self::$_Badges = $this->SQL
              ->Select()
              ->From('Badge')
              ->OrderBy('BadgeID')
              ->Get()
              ->Result();
    }
    return self::$_Badges;
  }

  /**
   * Total number of badges in the system
   * @return int
   */
  public function GetCount() {
    return count($this->Get());
  }

  /**
   * Returns a list of currently enabled badges
   *
   * @return DataSet
   */
  public function GetEnabled() {
    return $this->SQL
              ->Select()
              ->From('Badge')
              ->Where('Enabled', TRUE)
              ->OrderBy('BadgeID')
              ->Get()
              ->Result();
  }

  /**
   * Returns data for a specific badge
   *
   * @param int $BadgeID
   * @return DataSet
   */
  public function GetByID($BadgeID) {
    $Badge = $this->SQL
                    ->Select()
                    ->From('Badge')
                    ->Where('BadgeID', $BadgeID)
                    ->Get()
                    ->FirstRow();
    return $Badge;
  }

  /**
   * Returns the last inserted badge
   *
   * @return DataSet
   */
  public function GetNewest() {
    $Badge = $this->SQL
                    ->Select()
                    ->From('Badge')
                    ->OrderBy('BadgeID', 'desc')
                    ->Get()
                    ->FirstRow();
    return $Badge;
  }

  /**
   * Convenience function to determine if a badge id currently exists
   *
   * @param int $BadgeID
   * @return bool
   */
  public function Exists($BadgeID) {
    $temp = $this->GetByID($BadgeID);
    return !empty($temp);
  }

  /**
   * Enable or disable a badge
   *
   * @param int $BadgeID
   * @param bool $Enable
   */
  public function Enable($BadgeID, $Enable) {
    $Enable = (!$Enable) ? FALSE : TRUE;
    $this->SQL
            ->Update('Badge')
            ->Set('Enabled', $Enable)
            ->Where('BadgeID', $BadgeID)
            ->Put();
  }

  /**
   * Remove a badge and associated awards
   *
   * @param int $BadgeID
   */
  public function Delete($BadgeID) {
    $Badge = $this->GetByID($BadgeID);
    if(!empty($Badge)) {
      try {
        $this->Database->BeginTransaction();
        // Delete the badge
        $this->SQL->Delete('Badge', array('BadgeID' => $BadgeID));

        // Find the affected users
        $UserIDSet = $this->SQL->Select('UserID')
                ->From('BadgeAward')
                ->Where('BadgeID', $BadgeID)
                ->Get()
                ->Result();

        $UserIDs = ConsolidateArrayValuesByKey($UserIDSet, 'UserID');

        // Decrement their badge count
        $this->SQL->Update('User')
                ->Set('CountBadges', 'CountBadges - 1', FALSE)
                ->Where('UserID', $UserIDs)
                ->Put();
        
        // Remove the award rows
        $this->SQL->Delete('BadgeAward', array('BadgeID' => $BadgeID));
        
        $this->Database->CommitTransaction();
      } catch(Exception $Ex) {
        $this->Database->RollbackTransaction();
        throw $Ex;
      }
      // Remove their points
      foreach($UserIDs as $UserID) {
        UserModel::GivePoints($UserID, -1 * $Badge->AwardValue, 'Badge');
      }
      return TRUE;
    }
    return FALSE;
  }
}