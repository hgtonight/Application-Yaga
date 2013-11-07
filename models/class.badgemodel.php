<?php if (!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * Badges
 *
 * Events:
 *
 * @package Yaga
 * @since 1.0
 */

class BadgeModel extends Gdn_Model {
  private static $_Badges = NULL;
  /**
   * Class constructor. Defines the related database table name.
   */
  public function __construct() {
    parent::__construct('Badge');
  }

  /**
   * Returns a list of all available badges
   */
  public function GetBadges() {
    if(empty(self::$_Badges)) {
      self::$_Badges = $this->SQL
              ->Select()
              ->From('Badge')
              ->OrderBy('BadgeID')
              ->Get()
              ->Result();
      //decho('Filling the badge cache.');
    }
    return self::$_Badges;
  }

  public function GetEnabledBadges() {
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
   * @param int $BadgeID
   * @return dataset
   */
  public function GetBadge($BadgeID) {
    $Badge = $this->SQL
                    ->Select()
                    ->From('Badge')
                    ->Where('BadgeID', $BadgeID)
                    ->Get()
                    ->FirstRow();
    return $Badge;
  }

  public function GetNewestBadge() {
    $Badge = $this->SQL
                    ->Select()
                    ->From('Badge')
                    ->OrderBy('BadgeID', 'desc')
                    ->Get()
                    ->FirstRow();
    return $Badge;
  }

  public function BadgeExists($BadgeID) {
    $temp = $this->GetBadge($BadgeID);
    return !empty($temp);
  }

  public function EnableBadge($BadgeID, $Enable) {
    $Enable = (!$Enable) ? FALSE : TRUE;
    $this->SQL
            ->Update('Badge')
            ->Set('Enabled', $Enable)
            ->Where('BadgeID', $BadgeID)
            ->Put();
  }

  public function DeleteBadge($BadgeID, $ReplacementID = NULL) {
    if($this->BadgeExists($BadgeID)) {
      $this->SQL->Delete('Badge', array('BadgeID' => $BadgeID));
      // TODO: Cleanup the related badge awards
//      if($ReplacementID && $this->BadgeExists($ReplacementID)) {
//        $this->SQL->Update('Reaction')
//                ->Set('BadgeID', $ReplacementID)
//                ->Where('BadgeID', $BadgeID);
//      }
//      else {
//        $this->SQL->Delete('Reaction', array('BadgeID' => $BadgeID));
//      }
    }
  }
  
  public function AwardBadge($BadgeID, $UserID) {
    if($this->BadgeExists($BadgeID)) {
      if(!$this->UserHasBadge($UserID, $BadgeID)) {
        $this->SQL->Insert('BadgeAward', array(
            'BadgeID' => $BadgeID,
            'UserID' => $UserID,
            'DateInserted' => date(DATE_ISO8601)
        ));
        
        // Record some activity
        $Badge = $this->GetBadge($BadgeID);
        $ActivityModel = new ActivityModel();
        
        $Activity = array(
            'ActivityType' => 'BadgeAward',
            'ActivityUserID' => $UserID,
            'Photo' => '/uploads/' . $Badge->Photo,
            'RecordType' => 'Badge',
            'RecordID' => $BadgeID,
            'Route' => '/badge/' . $Badge->BadgeID . '/' . Gdn_Format::Url($Badge->Name),
            'HeadlineFormat' => '{ActivityUserID,user} earned the <a href="{Url,html}">{Data.Name,text}</a> badge.',
            'Data' => array(
               'Name' => $Badge->Name
            ),
            'Story' => $Badge->Description
         );

        $ActivityModel->Queue($Activity);
        
        // TODO: Handle notifications
        
        $ActivityModel->SaveQueue();
      }
    } 
  }
  
  public function UserHasBadge($UserID, $BadgeID) {
    return $this->SQL
            ->Select()
            ->From('BadgeAward')
            ->Where('BadgeID', $BadgeID)
            ->Where('UserID', $UserID)
            ->GetCount();
  }
  
  public function GetUserBadgeAwards($UserID) {
    return $this->SQL
            ->Select()
            ->From('Badge b')
            ->Join('BadgeAward ba', 'ba.BadgeID = b.BadgeID', 'left')
            ->Where('ba.UserID', $UserID)
            ->Get()
            ->Result(DATASET_TYPE_ARRAY);
  }
  
}