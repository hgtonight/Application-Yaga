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

  /**
   * Gets the number of badges that have been awarded with a specific ID
   * 
   * @param int $BadgeID
   * @return int
   */
  public function GetCount($BadgeID = FALSE) {
    if($BadgeID) {
      $Wheres = array('BadgeID' => $BadgeID);
    }
    else {
      $Wheres = array();
    }
    return $this->SQL->GetCount('BadgeAward', $Wheres);
  }

  /**
   * Gets recently awarded badges with a specific ID
   * 
   * @param int $BadgeID
   * @param int $Limit
   * @return dataset
   */
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
        Yaga::GivePoints($UserID, $Badge->AwardValue, 'Badge');

        // Increment the user's badge count
        $this->SQL->Update('User')
                ->Set('CountBadges', 'CountBadges + 1', FALSE)
                ->Where('UserID', $UserID)
                ->Put();

        if(is_null($InsertUserID)) {
          $InsertUserID = Gdn::Session()->UserID;
        }

        // Record some activity
        $ActivityModel = new ActivityModel();

        $Activity = array(
            'ActivityType' => 'BadgeAward',
            'ActivityUserID' => $UserID,
            'RegardingUserID' => $InsertUserID,
            'Photo' => Url($Badge->Photo, TRUE),
            'RecordType' => 'Badge',
            'RecordID' => $BadgeID,
            'Route' => '/yaga/badges/' . $Badge->BadgeID . '/' . Gdn_Format::Url($Badge->Name),
            'HeadlineFormat' => T('Yaga.Badge.EarnedHeadlineFormat'),
            'Data' => array(
                'Name' => $Badge->Name
            ),
            'Story' => $Badge->Description
        );

        // Create a public record
        $ActivityModel->Queue($Activity, FALSE); // TODO: enable the grouped notifications after issue #1776 is resolved , array('GroupBy' => 'Route'));
        // Notify the user of the award
        $Activity['NotifyUserID'] = $UserID;
        $ActivityModel->Queue($Activity, 'BadgeAward', array('Force' => TRUE));

        // Actually save the activity
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
                    ->Get()
                    ->FirstRow();
  }

  /**
   * Returns the badges a user has already received
   *
   * @param int $UserID
   * @param string $DataType
   * @return mixed
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

  /**
   * Returns the list of unobtained but enabled badges for a specific user
   *
   * @param int $UserID
   * @return DataSet
   */
  public function GetUnobtained($UserID) {
    $Px = $this->Database->DatabasePrefix;
    $Sql = 'select b.BadgeID, b.Enabled, b.RuleClass, b.RuleCriteria, '
            . 'ba.UserID '
            . "from {$Px}Badge as b "
            . "left join {$Px}BadgeAward as ba ON b.BadgeID = ba.BadgeID and ba.UserID = :UserID ";

    return $this->Database->Query($Sql, array(':UserID' => $UserID))->Result();
  }

  /**
   * Used by the DBA controller to update the denormalized badge count on the
   * user table via dba/counts
   * @param string $Column
   * @param int $UserID
   * @return boolean
   * @throws Gdn_UserException
   */
  public function Counts($Column, $UserID = NULL) {
    if($UserID) {
      $Where = array('UserID' => $UserID);
    }
    else {
      $Where = NULL;
    }
    
    $Result = array('Complete' => TRUE);
    switch($Column) {
      case 'CountBadges':
        Gdn::Database()->Query(DBAModel::GetCountSQL('count', 'User', 'BadgeAward', 'CountBadges', 'BadgeAwardID', 'UserID', 'UserID', $Where));
        break;
      default:
        throw new Gdn_UserException("Unknown column $Column");
    }
    return $Result;
  }

}
