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
   * Used as a cache
   * @var DataSet
   */
  private static $_Perks = array();

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

  /**
   * Gets the number of ranks currently specified in the database.
   * 
   * @return int
   */
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
    $Ranks = $this->Get();
    
    foreach($Ranks as $Rank) {
      if($Rank->RankID == $RankID) {
        return $Rank;
      }
    }
    
    return NULL;
  }

  /**
   * Returns the highest rank a user can currently achieve
   *
   * @param object $User
   * @return mixed NULL if no qualifying ranks are found, Rank object otherwise
   */
  public function GetHighestQualifyingRank($User) {
    $Points = $User->Points;
    $Posts = $User->CountDiscussions + $User->CountComments;
    $StartDate = strtotime($User->DateInserted);
    
    $Ranks = $this->Get();
    
    $HighestRank = NULL;
    foreach($Ranks as $Rank) {
      // skip disabled ranks
      if(!$Rank->Enabled) {
        continue;
      }
      
      $TargetDate = time() - $Rank->AgeReq;
      if($Points >= $Rank->PointReq && $Posts >= $Rank->PostReq && $StartDate <= $TargetDate) {
        $HighestRank = $Rank;
      }
      else {
        // Don't continue if we do not qualify
        break;
      }
    }

    return $HighestRank;
  }
  
  /**
   * Get a list of perks associated with the specified Rank ID
   * 
   * @param int $RankID
   * @return array
   */
  public function GetPerks($RankID) {
    if(!array_key_exists($RankID, self::$_Perks)) {
      $Ranks = $this->Get();
      foreach($Ranks as $Rank) {
        self::$_Perks[$Rank->RankID] = unserialize($Rank->Perks);
        
        if(self::$_Perks[$Rank->RankID] === FALSE) {
          self::$_Perks[$Rank->RankID] = array();
        }
      }
    }
    
    return (array_key_exists($RankID, self::$_Perks)) ? self::$_Perks[$RankID] : array();
  }
  
  /**
   * Returns all role IDs the specified rank confers as a perk
   * 
   * @param int $RankID
   * @return array
   */
  public function GetPerkRoleIDs($RankID) {
    $RoleIDs = array();
    
    $Perks = $this->GetPerks($RankID);
    
    if(empty($Perks)) {
      return $RoleIDs;
    }
    
    foreach($Perks as $Perk => $Value) {
      if(substr($Perk, 0, 4) === 'Role') {
        $RoleIDs[] = $Value;
      }
    }

    return $RoleIDs;
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
   * @return boolean
   */
  public function Delete($RankID) {
    $Rank = $this->GetByID($RankID);
    if($Rank) {
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
   * @param bool $Activity Whether or not to insert an activity record.
   */
  public function Set($RankID, $UserID, $Activity = FALSE) {
    $Rank = $this->GetByID($RankID);
    $UserModel = Gdn::UserModel();
    $OldRankID = $UserModel->GetID($UserID)->RankID;
    
    // Don't bother setting a rank that they already have
    if($Rank->RankID == $OldRankID) {
        return;
    }
    
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
          'HeadlineFormat' => T('Yaga.Rank.PromotedHeadlineFormat'),
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
    
    $UserModel->SetField($UserID, 'RankID', $Rank->RankID);

    // Update the roles if necessary
    $this->_UpdateUserRoles($UserID, $OldRankID, $Rank->RankID);
    
    $this->EventArguments['Rank'] = $Rank;
    $this->EventArguments['UserID'] = $UserID;
    $this->FireEvent('AfterRankChange');
  }
  
  /**
   * Updates the sort field for each rank in the sort array
   * 
   * @param array $SortArray
   * @return boolean
   */
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
  
  /**
   * Updates a user roles by removing role perks from the old rank and adding the
   * roles from the new rank
   * @param int $UserID
   * @param int $OldRankID
   * @param int $NewRankID
   */
  private function _UpdateUserRoles($UserID, $OldRankID, $NewRankID) {
    $UserModel = Gdn::UserModel();
    
    // Get the user's current roles
    $CurrentRoleData = $UserModel->GetRoles($UserID);
    $CurrentRoleIDs = ConsolidateArrayValuesByKey($CurrentRoleData->Result(), 'RoleID');

    // Get the associated role perks
    $OldPerkRoles = $this->GetPerkRoleIDs($OldRankID);
    $NewPerkRoles = $this->GetPerkRoleIDs($NewRankID);

    // Remove any role perks the old rank had
    $TempRoleIDs = array_diff($CurrentRoleIDs, $OldPerkRoles);

    // Add our selected roles
    $NewRoleIDs = array_unique(array_merge($TempRoleIDs, $NewPerkRoles));

    // Set the combined roles
    if($NewRoleIDs != $CurrentRoleIDs) {
      $UserModel->SaveRoles($UserID, $NewRoleIDs, FALSE);
    }

  }
}
