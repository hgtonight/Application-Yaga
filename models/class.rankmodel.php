<?php if (!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * Describes ranks and their associated requirements/rewards
 *
 * Events:
 *
 * @package Yaga
 * @since 1.0
 */

class RankModel extends Gdn_Model {

  /**
   * Used as a cache
   * @var DataSet
   */
  private static $_Ranks = NULL;

  /**
   * Defines the related database table name.
   */
  public function __construct() {
    parent::__construct('Rank');
  }

  /**
   * Returns a list of all ranks
   *
   * @return DataSet
   */
  public function Get() {
    if(empty(self::$_Ranks)) {
      self::$_Ranks = $this->SQL
              ->Select()
              ->From('Rank')
              ->OrderBy('Sort')
              ->Get()
              ->Result();
    }
    return self::$_Ranks;
  }

  public function GetCount() {
    return count($this->Get());
  }

  /**
   * Returns data for a specific rank
   *
   * @param int $RankID
   * @return DataSet
   */
  public function GetByID($RankID) {
    $Rank = $this->SQL
                    ->Select()
                    ->From('Rank')
                    ->Where('RankID', $RankID)
                    ->Get()
                    ->FirstRow();
    return $Rank;
  }

  /**
   * Returns the nearest rank below the value passed
   *
   * @param int $Points
   * @return DataSet
   */
  public function GetByPoints($Points) {
    $Rank = $this->SQL
                    ->Select()
                    ->From('Rank')
                    ->Where('Level <=', $Points)
                    ->Where('Enabled', '1')
                    ->OrderBy('Level', 'Desc')
                    ->Get()
                    ->FirstRow();
    return $Rank;
  }

  /**
   * Enable or disable a rank
   *
   * @param int $RankID
   * @param bool $Enable
   */
  public function Enable($RankID, $Enable) {
    $Enable = (!$Enable) ? FALSE : TRUE;
    $this->SQL
            ->Update('Rank')
            ->Set('Enabled', $Enable)
            ->Where('RankID', $RankID)
            ->Put();
  }

  /**
   * Remove a rank and associated awards
   *
   * @param int $RankID
   */
  public function Delete($RankID) {
    $Rank = $this->GetByID($RankID);
    if(!$Rank) {
      $this->SQL->Delete('Rank', array('RankID' => $RankID));
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Set a user's rank and record some activity if it was a promotion
   *
   * @param int $RankID
   * @param int $UserID This is the user that should get the award
   */
  public function Set($RankID, $UserID, $Activity = FALSE) {
    $Rank = $this->GetByID($RankID);
    if($Activity) {
      // Throw up a promotion activity
      $ActivityModel = new ActivityModel();

      $Activity = array(
          'ActivityType' => 'RankPromotion',
          'ActivityUserID' => $UserID,
          'RegardingUserID' => $UserID,
          'Photo' => C('Yaga.Ranks.Photo'),
          'RecordType' => 'Rank',
          'RecordID' => $Rank->RankID,
          'HeadlineFormat' => T('Yaga.HeadlineFormat.Promoted'),
          'Data' => array(
              'Name' => $Rank->Name
          ),
          'Story' => $Rank->Description
      );

      // Create a public record
      $ActivityModel->Queue($Activity, FALSE); // TODO: enable the grouped notifications after issue #1776 is resolved , array('GroupBy' => 'Story'));

      // Notify the user of the award
      $Activity['NotifyUserID'] = $UserID;
      $ActivityModel->Queue($Activity, 'RankPromotion', array('Force' => TRUE));

      $ActivityModel->SaveQueue();
    }
    // Update the rank id
    $UserModel = Gdn::UserModel();
    $UserModel->SetField($UserID, 'RankID', $Rank->RankID);

    // Get the user's roles
    $CurrentRoleData = $UserModel->GetRoles($UserID);
    $CurrentRoleIDs = ConsolidateArrayValuesByKey($CurrentRoleData->Result(), 'RoleID');

    // Remove the old roles
    $TempRoleIDs = array_diff($CurrentRoleIDs, array($OldRank->Role));

    // Add our selected roles
    $NewRoleIDs = array_unique(array_merge($TempRoleIDs, array($Rank->Role)));

    // Set the combined roles
    if($NewRoleIDs != $CurrentRoleIDs) {
      $UserModel->SaveRoles($UserID, $NewRoleIDs);
    }

    $this->EventArguments['Rank'] = $Rank;
    $this->EventArguments['UserID'] = $UserID;
    $this->FireEvent('AfterRankChange');
  }
  
  public function SaveSort($SortArray) {
    foreach($SortArray as $Index => $Rank) {
      // skip the header row
      if($Index == 0) {
        continue;
      }
      
      // remove the 'RankID_' prefix
      $RankID = substr($Rank, 7);
      $this->SetField($RankID, 'Sort', $Index);
    }
    return TRUE;
  }
}
