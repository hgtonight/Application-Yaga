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
              ->OrderBy('PointsRequired')
              ->Get()
              ->Result();
    }
    return self::$_Ranks;
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
              ->OrderBy('PointsRequired')
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
    }
  }
  
  public function UpdateRank($Points, $UserID) {
    
  }
  
  /**
   * Set a user's rank and record some activity if it was a promotion
   * 
   * @param int $RankID
   * @param int $UserID This is the user that should get the award
   */
  public function SetRank($RankID, $UserID) {
    $NewRank = $this->GetRank($RankID);
    $UserModel = Gdn::UserModel();
    $User = $UserModel->GetID($UserID);
    
    if(!empty($NewRank)
            && !empty($User)) {
      $UserModel->SetField('RankID', $RankID);
        
      // Record some activity if it was a promotion
        
      $OldRank = $this->GetRank($User->RankID);
      
      if($OldRank->PointsRequired <= $NewRank->PointsRequired) {
        $ActivityModel = new ActivityModel();
        
        $Activity = array(
            'ActivityType' => 'RankPromotion',
            'ActivityUserID' => Gdn::Session()->UserID,
            'RegardingUserID' => $UserID,
            'Photo' => '/uploads/' . $Rank->Photo,
            'RecordType' => 'Rank',
            'RecordID' => $RankID,
            'Route' => '/ranks/detail/' . $Rank->RankID . '/' . Gdn_Format::Url($Rank->Name),
            'HeadlineFormat' => T('Yaga.HeadlineFormat.Promoted', '{RegardingUserID,You} have been promoted to {Data.Name,text}.'),
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
         $this->FireEvent('AfterRankPromotion');
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
}