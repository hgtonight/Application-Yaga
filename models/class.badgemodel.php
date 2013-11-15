<?php if (!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * Describes badges and the associated rule criteria
 *
 * @todo Consider splitting into two models
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
  public function GetBadges() {
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
  
  public function GetBadgeCount() {
    return count($this->GetBadges());
  }

  /**
   * Returns a list of currently enabled badges
   * 
   * @return DataSet
   */
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
   * 
   * @param int $BadgeID
   * @return DataSet
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

  /**
   * Returns the last inserted badge
   * 
   * @return DataSet
   */
  public function GetNewestBadge() {
    $Badge = $this->SQL
                    ->Select()
                    ->From('Badge')
                    ->OrderBy('BadgeID', 'desc')
                    ->Get()
                    ->FirstRow();
    return $Badge;
  }
  
  public function GetBadgeAwardCount($BadgeID) {
    $Wheres = array('BadgeID' => $BadgeID);
    return $this->SQL
            ->GetCount('BadgeAward', $Wheres);
  }
  
  public function GetRecentBadgeAwards($BadgeID, $Limit = 15) {
    return $this->SQL
            ->Select('ba.UserID, ba.DateInserted, u.Name, u.Photo, u.Gender, u.Email')
            ->From('BadgeAward ba')
            ->Join('User u', 'ba.UserID = u.UserID')
            ->Where('BadgeID', $BadgeID)
            ->OrderBy('DateInserted', 'Desc')
            ->Limit($Limit)
            ->Get()
            ->Result();
  }

  /**
   * Convenience function to determin if a badge id currently exists
   * 
   * @param int $BadgeID
   * @return bool
   */
  public function BadgeExists($BadgeID) {
    $temp = $this->GetBadge($BadgeID);
    return !empty($temp);
  }

  /**
   * Enable or disable a badge
   * 
   * @param int $BadgeID
   * @param bool $Enable
   */
  public function EnableBadge($BadgeID, $Enable) {
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
  public function DeleteBadge($BadgeID) {
    if($this->BadgeExists($BadgeID)) {
      $this->SQL->Delete('Badge', array('BadgeID' => $BadgeID));
      $this->SQL->Delete('BadgeAward', array('BadgeID' => $BadgeID));
    }
  }
  
  /**
   * Award a badge to a user and record some activity
   * 
   * @param int $BadgeID
   * @param int $UserID This is the user that should get the award
   * @param int $InsertUserID This is the user that gave the award
   * @param string $Reason This is the reason the giver gave with the award
   */
  public function AwardBadge($BadgeID, $UserID, $InsertUserID = NULL, $Reason = '') {
    if($this->BadgeExists($BadgeID)) {
      if(!$this->UserHasBadge($UserID, $BadgeID)) {
        $this->SQL->Insert('BadgeAward', array(
            'BadgeID' => $BadgeID,
            'UserID' => $UserID,
            'InsertUserID' => $InsertUserID,
            'Reason' => $Reason,
            'DateInserted' => date(DATE_ISO8601)
        ));
        
        // Record some activity
        $Badge = $this->GetBadge($BadgeID);
        $ActivityModel = new ActivityModel();
        
        $Activity = array(
            'ActivityType' => 'BadgeAward',
            'ActivityUserID' => Gdn::Session()->UserID,
            'RegardingUserID' => $UserID,
            'Photo' => '/uploads/' . $Badge->Photo,
            'RecordType' => 'Badge',
            'RecordID' => $BadgeID,
            'Route' => '/badges/detail/' . $Badge->BadgeID . '/' . Gdn_Format::Url($Badge->Name),
            'HeadlineFormat' => '{RegardingUserID,You} earned the <a href="{Url,html}">{Data.Name,text}</a> badge.',
            'Data' => array(
               'Name' => $Badge->Name
            ),
            'Story' => $Badge->Description
         );
         
         $ActivityModel->Queue($Activity);
         
         // Notify the user of the award
         $Activity['NotifyUserID'] = $UserID;
         $Activity['Emailed'] = ActivityModel::SENT_PENDING;
         $ActivityModel->Queue($Activity, 'Badges', array('Force' => TRUE));
         
         $ActivityModel->SaveQueue();
      }
    } 
  }
  
  /**
   * Returns how many badges the user has of this particular id. It should only 
   * ever be 1 or zero.
   * 
   * @param int $UserID
   * @param int $BadgeID
   * @return int
   */
  public function UserHasBadge($UserID, $BadgeID) {
    return $this->SQL
            ->Select()
            ->From('BadgeAward')
            ->Where('BadgeID', $BadgeID)
            ->Where('UserID', $UserID)
            ->GetCount();
  }
  
  /**
   * Returns the badges a user already has
   * 
   * @param int $UserID
   * @return array
   */
  public function GetUserBadgeAward($UserID, $BadgeID) {
    return $this->SQL
            ->Select()
            ->From('Badge b')
            ->Join('BadgeAward ba', 'ba.BadgeID = b.BadgeID', 'left')
            ->Where('b.BadgeID', $BadgeID)
            ->Where('ba.UserID', $UserID)
            ->Get()
            ->FirstRow();
  }
  
  /**
   * Returns the badges a user already has
   * 
   * @param int $UserID
   * @return array
   */
  public function GetUserBadgeAwards($UserID) {
    return $this->SQL
            ->Select()
            ->From('Badge b')
            ->Join('BadgeAward ba', 'ba.BadgeID = b.BadgeID', 'left')
            ->Where('ba.UserID', $UserID)
            ->Get()
            ->Result(DATASET_TYPE_ARRAY);
  }
  
  /**
   * Returns the full list of badges and the associated user awards if applicable
   * 
   * @todo Refactor controllers that massage the data together to use this instead
   * @param int $UserID
   * @return DataSet
   */
  public function GetAllBadgesUserAwards($UserID) {
    return $this->SQL
            ->Select('b.*, ba.UserID, ba.InsertUserID, ba.Reason, ba.DateInserted, ui.Name as InsertUserName')
            ->From('Badge b')
            ->Join('BadgeAward ba', 'ba.BadgeID = b.BadgeID', 'left')
            ->Join('User ui', 'ba.InsertUserID = ui.UserID', 'left')
            ->Where('ba.UserID', $UserID)
            ->OrWhere('b.BadgeID is not null') // needed to get the full set of badges
            ->GroupBy('b.BadgeID')
            ->OrderBy('b.BadgeID', 'Desc')
            ->Get();
  }
  
  /**
   * Returns the list of unobtained but enabled badges for a specific user
   * 
   * @param int $UserID
   * @param bool $Enabled Description
   * @return DataSet
   */
  public function GetBadgesToCheckForUser($UserID) {
    return $this->SQL
            ->Select()
            ->From('Badge b')
            ->Join('BadgeAward ba', 'b.BadgeID = ba.BadgeID', 'left')
            ->Where('ba.UserID', $UserID)
            ->Where('b.Enabled', 1)
            //->OrWhere('b.BadgeID is not null') // needed to get the full set of badges
            ->Get()
            ->Result();
  }
}