<?php if (!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * Describes ranks and the associated rule criteria
 *
 * @todo Consider splitting into two models
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
  public function GetRanks() {
    if(empty(self::$_Ranks)) {
      self::$_Ranks = $this->SQL
              ->Select()
              ->From('Rank')
              ->OrderBy('RankID')
              ->Get()
              ->Result();
    }
    return self::$_Ranks;
  }
  
  public function GetRankCount() {
    return count($this->GetRanks());
  }

  /**
   * Returns a list of currently enabled ranks
   * 
   * @return DataSet
   */
  public function GetEnabledRanks() {
    return $this->SQL
              ->Select()
              ->From('Rank')
              ->Where('Enabled', TRUE)
              ->OrderBy('RankID')
              ->Get()
              ->Result();
  }
  
  /**
   * Returns data for a specific rank
   * 
   * @param int $RankID
   * @return DataSet
   */
  public function GetRank($RankID) {
    $Rank = $this->SQL
                    ->Select()
                    ->From('Rank')
                    ->Where('RankID', $RankID)
                    ->Get()
                    ->FirstRow();
    return $Rank;
  }

  /**
   * Returns the last inserted rank
   * 
   * @return DataSet
   */
  public function GetNewestRank() {
    $Rank = $this->SQL
                    ->Select()
                    ->From('Rank')
                    ->OrderBy('RankID', 'desc')
                    ->Get()
                    ->FirstRow();
    return $Rank;
  }
  
  public function GetRankAwardCount($RankID) {
    $Wheres = array('RankID' => $RankID);
    return $this->SQL
            ->GetCount('RankAward', $Wheres);
  }
  
  public function GetRecentRankAwards($RankID, $Limit = 15) {
    return $this->SQL
            ->Select('ba.UserID, ba.DateInserted, u.Name, u.Photo, u.Gender, u.Email')
            ->From('RankAward ba')
            ->Join('User u', 'ba.UserID = u.UserID')
            ->Where('RankID', $RankID)
            ->OrderBy('DateInserted', 'Desc')
            ->Limit($Limit)
            ->Get()
            ->Result();
  }

  /**
   * Convenience function to determin if a rank id currently exists
   * 
   * @param int $RankID
   * @return bool
   */
  public function RankExists($RankID) {
    $temp = $this->GetRank($RankID);
    return !empty($temp);
  }

  /**
   * Enable or disable a rank
   * 
   * @param int $RankID
   * @param bool $Enable
   */
  public function EnableRank($RankID, $Enable) {
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
  public function DeleteRank($RankID) {
    if($this->RankExists($RankID)) {
      $this->SQL->Delete('Rank', array('RankID' => $RankID));
      $this->SQL->Delete('RankAward', array('RankID' => $RankID));
    }
  }
  
  /**
   * Award a rank to a user and record some activity
   * 
   * @param int $RankID
   * @param int $UserID This is the user that should get the award
   * @param int $InsertUserID This is the user that gave the award
   * @param string $Reason This is the reason the giver gave with the award
   */
  public function AwardRank($RankID, $UserID, $InsertUserID = NULL, $Reason = '') {
    $Rank = $this->GetRank($RankID);
    if(!empty($Rank)) {
      if(!$this->UserHasRank($UserID, $RankID)) {
        $this->SQL->Insert('RankAward', array(
            'RankID' => $RankID,
            'UserID' => $UserID,
            'InsertUserID' => $InsertUserID,
            'Reason' => $Reason,
            'DateInserted' => date(DATE_ISO8601)
        ));
        
        // Record the points for this rank
        UserModel::GivePoints($UserID, $Rank->AwardValue, 'Rank');
        
        // Record some activity
        $Rank = $this->GetRank($RankID);
        $ActivityModel = new ActivityModel();
        
        $Activity = array(
            'ActivityType' => 'RankAward',
            'ActivityUserID' => Gdn::Session()->UserID,
            'RegardingUserID' => $UserID,
            'Photo' => '/uploads/' . $Rank->Photo,
            'RecordType' => 'Rank',
            'RecordID' => $RankID,
            'Route' => '/ranks/detail/' . $Rank->RankID . '/' . Gdn_Format::Url($Rank->Name),
            'HeadlineFormat' => '{RegardingUserID,You} earned the <a href="{Url,html}">{Data.Name,text}</a> rank.',
            'Data' => array(
               'Name' => $Rank->Name
            ),
            'Story' => $Rank->Description
         );
         
         $ActivityModel->Queue($Activity);
         
         // Notify the user of the award
         $Activity['NotifyUserID'] = $UserID;
         $Activity['Emailed'] = ActivityModel::SENT_PENDING;
         $ActivityModel->Queue($Activity, 'Ranks', array('Force' => TRUE));
         
         $ActivityModel->SaveQueue();
         
         $this->EventArguments['UserID'] = $UserID;
         $this->FireEvent('AfterRankAward');
      }
    } 
  }
  
  /**
   * Returns how many ranks the user has of this particular id. It should only 
   * ever be 1 or zero.
   * 
   * @param int $UserID
   * @param int $RankID
   * @return int
   */
  public function UserHasRank($UserID, $RankID) {
    return $this->SQL
            ->Select()
            ->From('RankAward')
            ->Where('RankID', $RankID)
            ->Where('UserID', $UserID)
            ->GetCount();
  }
  
  /**
   * Returns the ranks a user already has
   * 
   * @param int $UserID
   * @return array
   */
  public function GetUserRankAward($UserID, $RankID) {
    return $this->SQL
            ->Select()
            ->From('Rank b')
            ->Join('RankAward ba', 'ba.RankID = b.RankID', 'left')
            ->Where('b.RankID', $RankID)
            ->Where('ba.UserID', $UserID)
            ->Get()
            ->FirstRow();
  }
  
  /**
   * Returns the ranks a user already has
   * 
   * @param int $UserID
   * @return array
   */
  public function GetUserRankAwards($UserID) {
    return $this->SQL
            ->Select()
            ->From('Rank b')
            ->Join('RankAward ba', 'ba.RankID = b.RankID', 'left')
            ->Where('ba.UserID', $UserID)
            ->Get()
            ->Result(DATASET_TYPE_ARRAY);
  }
  
  /**
   * Returns the full list of ranks and the associated user awards if applicable
   * 
   * @param int $UserID
   * @return DataSet
   */
  public function GetAllRanksUserAwards($UserID) {
    return $this->SQL
            ->Select('b.*, ba.UserID, ba.InsertUserID, ba.Reason, ba.DateInserted, ui.Name as InsertUserName')
            ->From('Rank b')
            ->Join('RankAward ba', 'ba.RankID = b.RankID', 'left')
            ->Join('User ui', 'ba.InsertUserID = ui.UserID', 'left')
            ->Where('ba.UserID', $UserID)
            ->OrWhere('b.RankID is not null') // needed to get the full set of ranks
            ->GroupBy('b.RankID')
            ->OrderBy('b.RankID', 'Desc')
            ->Get();
  }
  
  /**
   * Returns the list of unobtained but enabled ranks for a specific user
   * 
   * @param int $UserID
   * @param bool $Enabled Description
   * @return DataSet
   */
  public function GetRanksToCheckForUser($UserID) {
    return $this->SQL
            ->Select()
            ->From('Rank b')
            ->Join('RankAward ba', 'b.RankID = ba.RankID', 'left')
            ->Where('ba.UserID', $UserID)
            ->Where('b.Enabled', 1)
            //->OrWhere('b.RankID is not null') // needed to get the full set of ranks
            ->Get()
            ->Result();
  }
}