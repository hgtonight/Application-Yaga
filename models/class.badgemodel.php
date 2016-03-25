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
              ->OrderBy('Sort')
              ->Get()
              ->Result();
    }
    return self::$_Badges;
  }

  /**
   * Gets the badge list with an optional limit and offset
   * 
   * @param int $Limit
   * @param int $Offset
   * @return DataSet
   */
  public function GetLimit($Limit = FALSE, $Offset = FALSE) {
      return $this->SQL
              ->Select()
              ->From('Badge')
              ->OrderBy('Sort')
              ->Limit($Limit, $Offset)
              ->Get()
              ->Result();
  }

  /**
   * Total number of badges in the system
   * @return int
   */
  public function GetCount() {
    return count($this->Get());
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
    $this->EventArguments['BadgeID'] = $BadgeID;
    $this->EventArguments['Enable'] = $Enable;
    $this->FireEvent('BadgeEnable');
  }

  /**
   * Remove a badge and associated awards
   *
   * @param int $BadgeID
   * @throws Exception
   * @return boolean
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

        // Remove their points
        foreach($UserIDs as $UserID) {
          Yaga::GivePoints($UserID, -1 * $Badge->AwardValue, 'Badge');
        }
        // Remove the award rows
        $this->SQL->Delete('BadgeAward', array('BadgeID' => $BadgeID));

        $this->Database->CommitTransaction();
      } catch(Exception $Ex) {
        $this->Database->RollbackTransaction();
        throw $Ex;
      }
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Get the full list of badges joined with the award data for a specific user
   * This shouldn't really be here, but I can't think of a good place to put it
   *
   * @param int $UserID
   * @return DataSet
   */
  public function GetWithEarned($UserID) {
    $Px = $this->Database->DatabasePrefix;
    $Sql = 'select b.BadgeID, b.Name, b.Description, b.Photo, b.AwardValue, '
            . 'ba.UserID, ba.InsertUserID, ba.Reason, ba.DateInserted, '
            . 'ui.Name AS InsertUserName '
            . "from {$Px}Badge as b "
            . "left join {$Px}BadgeAward as ba ON b.BadgeID = ba.BadgeID and ba.UserID = :UserID "
            . "left join {$Px}User as ui on ba.InsertUserID = ui.UserID "
            . 'order by b.Sort';

    return $this->Database->Query($Sql, array(':UserID' => $UserID))->Result();
  }
  
  /**
   * Updates the sort field for each badge in the sort array
   * 
   * @since 1.1
   * @param array $SortArray
   * @return boolean
   */
  public function SaveSort($SortArray) {
    foreach($SortArray as $Index => $Badge) {
      // remove the 'BadgeID_' prefix
      $BadgeID = substr($Badge, 8);
      $this->SetField($BadgeID, 'Sort', $Index);
    }
    return TRUE;
  }

}
