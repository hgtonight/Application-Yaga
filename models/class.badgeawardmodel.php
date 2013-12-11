<?php if (!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * Handles badge awards
 *
 * Events:
 *
 * @package Yaga
 * @since 1.0
 */

class BadgeAwardModel extends Gdn_Model {

  /**
   * Defines the related database table name.
   */
  public function __construct() {
    parent::__construct('BadgeAward');
  }

  public function GetCount($BadgeID) {
    $Wheres = array('BadgeID' => $BadgeID);
    return $this->SQL
            ->GetCount('BadgeAward', $Wheres);
  }

  public function GetRecent($BadgeID, $Limit = 15) {
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
   * Award a badge to a user and record some activity
   *
   * @param int $BadgeID
   * @param int $UserID This is the user that should get the award
   * @param int $InsertUserID This is the user that gave the award
   * @param string $Reason This is the reason the giver gave with the award
   */
  public function Award($BadgeID, $UserID, $InsertUserID = NULL, $Reason = '') {
    $Badge = Yaga::BadgeModel()->GetByID($BadgeID);
    if(!empty($Badge)) {
      if(!$this->Exists($UserID, $BadgeID)) {
        $this->SQL->Insert('BadgeAward', array(
            'BadgeID' => $BadgeID,
            'UserID' => $UserID,
            'InsertUserID' => $InsertUserID,
            'Reason' => $Reason,
            'DateInserted' => date(DATE_ISO8601)
        ));

        // Record the points for this badge
        UserModel::GivePoints($UserID, $Badge->AwardValue, 'Badge');

        // Increment the user's badge count
        $this->SQL->Update('User')
         ->Set('CountBadges', 'CountBadges + 1', FALSE)
         ->Where('UserID', $UserID)
         ->Put();

        // Record some activity
        $ActivityModel = new ActivityModel();

        $Activity = array(
            'ActivityType' => 'BadgeAward',
            'ActivityUserID' => Gdn::Session()->UserID,
            'RegardingUserID' => $UserID,
            'Photo' => '/uploads/' . $Badge->Photo,
            'RecordType' => 'Badge',
            'RecordID' => $BadgeID,
            'Route' => '/badges/detail/' . $Badge->BadgeID . '/' . Gdn_Format::Url($Badge->Name),
            'HeadlineFormat' => T('Yaga.HeadlineFormat.BadgeEarned'),
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

         $this->EventArguments['UserID'] = $UserID;
         $this->FireEvent('AfterBadgeAward');
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
  public function Exists($UserID, $BadgeID) {
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
  public function GetByUser($UserID, $DataType = DATASET_TYPE_ARRAY) {
    return $this->SQL
            ->Select()
            ->From('Badge b')
            ->Join('BadgeAward ba', 'ba.BadgeID = b.BadgeID', 'left')
            ->Where('ba.UserID', $UserID)
            ->Get()
            ->Result($DataType);
  }

  public function GetByUserCount($UserID) {
    return $this->SQL
            ->Select()
            ->From('Badge b')
            ->Join('BadgeAward ba', 'ba.BadgeID = b.BadgeID', 'left')
            ->Where('ba.UserID', $UserID)
            ->GetCount();
  }

  /**
   * Returns the full list of badges and the associated user awards if applicable
   *
   * @param int $UserID
   * @return DataSet
   */
  public function GetEarnedJoinAll($UserID) {
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
  
  public function GetEarned($UserID) {
    return $this->SQL
            ->Select('ba.*, ui.Name as InsertUserName')
            ->From('BadgeAward ba')
            ->Join('User ui', 'ba.InsertUserID = ui.UserID', 'left')
            ->Where('ba.UserID', $UserID)
            ->OrderBy('ba.DateInserted', 'Desc')
            ->Get();
  }

  /**
   * Returns the list of unobtained but enabled badges for a specific user
   *
   * @param int $UserID
   * @param bool $Enabled Description
   * @return DataSet
   */
  public function GetUnearned($UserID) {
    return $this->SQL
            ->Select()
            ->From('Badge b')
            ->Join('BadgeAward ba', 'b.BadgeID = ba.BadgeID', 'left')
            ->Where('ba.UserID', $UserID)
            ->Where('b.Enabled', 1)
            ->Get()
            ->Result();
  }
}