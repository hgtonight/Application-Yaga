<?php if (!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * Reactions are the actions a user takes against another user's content
 *
 * Events: AfterReactionSave
 *
 * @package Yaga
 * @since 1.0
 */

class ReactionModel extends Gdn_Model {

  /**
   * Used to cache the reactions
   * @var array
   */
  private static $_Reactions = array();

  /**
   * Defines the related database table name.
   */
  public function __construct() {
    parent::__construct('Reaction');
  }

  /**
   * Returns all available actions along with the current count specified by
   * the $ID and $Type of content.
   * 
   * @param int $ID
   * @param string $Type
   * @return DataSet
   */
  public function GetList($ID, $Type) {
    $Px = $this->Database->DatabasePrefix;

    // try getting the record count from the cache
    if (array_key_exists($Type . $ID, self::$_Reactions)) {
      $Reactions = self::$_Reactions[$Type . $ID];
      $Actions = Yaga::ActionModel()->Get();
      // add the count
      foreach ($Actions as &$Action) {
        $Action->Count = 0;
        foreach ($Reactions as $Reaction) {
          if ($Reaction->ActionID == $Action->ActionID) {
            $Action->Count++;
          }
        }
      }
      return $Actions;
    }

    $Sql = "select a.*, "
            . "(select count(r.ReactionID) "
            . "from {$Px}Reaction as r "
            . "where r.ParentID = :ParentID and r.ParentType = :ParentType "
            . "and r.ActionID = a.ActionID) as Count "
            . "from {$Px}Action AS a "
            . "order by a.Sort";

    return $this->Database->Query($Sql, array(':ParentID' => $ID, ':ParentType' => $Type))->Result();
  }

  /**
   * Returns the reaction records associated with the specified user content.
   *
   * @param int $ID
   * @param string $Type is the kind of ID. Valid: comment, discussion, activity
   * @return mixed DataSet if it exists, NULL otherwise
   */
  public function GetRecord($ID, $Type) {
    // try getting the record from the cache
    if (array_key_exists($Type . $ID, self::$_Reactions)) {
      return self::$_Reactions[$Type . $ID];
    }
    else {
      $Result = $this->SQL
              ->Select('a.*, r.InsertUserID as UserID, r.DateInserted')
              ->From('Action a')
              ->Join('Reaction r', 'a.ActionID = r.ActionID')
              ->Where('r.ParentID', $ID)
              ->Where('r.ParentType', $Type)
              ->OrderBy('r.DateInserted')
              ->Get()
              ->Result();
      self::$_Reactions[$Type . $ID] = $Result;
      return $Result;
    }
  }

  /**
   * Return a list of reactions a user has received
   *
   * @param int $ID
   * @param string $Type activity, comment, discussion
   * @param int $UserID
   * @return DataSet
   */
  public function GetByUser($ID, $Type, $UserID) {
    return $this->SQL
            ->Select()
            ->From('Reaction')
            ->Where('ParentID', $ID)
            ->Where('ParentType', $Type)
            ->Where('InsertUserID', $UserID)
            ->Get()
            ->FirstRow();
  }

  /**
   * Return the count of reactions received by a user
   *
   * @param int $UserID
   * @param int $ActionID
   * @return DataSet
   */
  public function GetUserCount($UserID, $ActionID) {
    return $this->SQL
            ->Select()
            ->From('Reaction')
            ->Where('ActionID', $ActionID)
            ->Where('ParentAuthorID', $UserID)
            ->GetCount();
  }
  
  /**
   * Return the count of actions taken by a user
   *
   * @param int $UserID
   * @param int $ActionID
   * @return DataSet
   */
  public function GetUserTakenCount($UserID, $ActionID) {
    return $this->SQL
            ->Select()
            ->From('Reaction')
            ->Where('ActionID', $ActionID)
            ->Where('InsertUserID', $UserID)
            ->GetCount();
  }

  /**
   * Sets a users reaction against another user's content. A user can only react
   * in one way to each unique piece of content. This function makes sure to
   * enforce this rule
   *
   * Events: AfterReactionSave
   *
   * @param int $ID
   * @param string $Type activity, comment, discussion
   * @param int $AuthorID
   * @param int $UserID
   * @param int $ActionID
   * @return DataSet
   */
  public function Set($ID, $Type, $AuthorID, $UserID, $ActionID) {
    // clear the cache
    unset(self::$_Reactions[$Type . $ID]);

    $EventArgs = array('ParentID' => $ID, 'ParentType' => $Type, 'ParentUserID' => $AuthorID, 'InsertUserID' => $UserID, 'ActionID' => $ActionID);
    $ActionModel = Yaga::ActionModel();
    $NewAction = $ActionModel->GetByID($ActionID);
    $Points = $Score = $NewAction->AwardValue;
    $CurrentReaction = $this->GetByUser($ID, $Type, $UserID);
    $EventArgs['CurrentReaction'] = $CurrentReaction;
    $this->FireEvent('BeforeReactionSave', $EventArgs);

    if($CurrentReaction) {
      $OldAction = $ActionModel->GetByID($CurrentReaction->ActionID);

      if($ActionID == $CurrentReaction->ActionID) {
        // remove the record
        $Reaction = $this->SQL->Delete('Reaction', array('ParentID' => $ID,
                    'ParentType' => $Type,
                    'InsertUserID' => $UserID,
                    'ActionID' => $ActionID));
        $EventArgs['Exists'] = FALSE;
        $Score = 0;
        $Points = -1 * $OldAction->AwardValue;
      }
      else {
        // update the record
        $Reaction = $this->SQL
              ->Update('Reaction')
              ->Set('ActionID', $ActionID)
              ->Set('DateInserted', date(DATE_ISO8601))
              ->Where('ParentID', $ID)
              ->Where('ParentType', $Type)
              ->Where('InsertUserID', $UserID)
              ->Put();
        $EventArgs['Exists'] = TRUE;
        $Points = -1 * ($OldAction->AwardValue - $Points);
      }
    }
    else {
      // insert a record
      $Reaction = $this->SQL
              ->Insert('Reaction',
                      array('ActionID' => $ActionID,
                      'ParentID' =>  $ID,
                      'ParentType' => $Type,
                      'ParentAuthorID' => $AuthorID,
                      'InsertUserID' => $UserID,
                      'DateInserted' => date(DATE_ISO8601)));
      $EventArgs['Exists'] = TRUE;
    }

    // Update the parent item score
    $this->SetUserScore($ID, $Type, $UserID, $Score);
    // Give the user points commesurate with reaction activity
    Yaga::GivePoints($AuthorID, $Points, 'Reaction');
    $EventArgs['Points'] = $Points;
    $this->FireEvent('AfterReactionSave', $EventArgs);
    return $Reaction;
  }

  /**
   * Fills the memory cache with the specified reaction records
   *
   * @since 1.1
   * @param string $Type
   * @param array $IDs
   */
  public function Prefetch($Type, $IDs) {
    if (!is_array($IDs)) {
        $IDs = (array)$IDs;
    }
    
    if (in_array($Type, array('discussion', 'comment', 'activity')) && !empty($IDs)) {
      $Result = $this->SQL
        ->Select('a.*, r.InsertUserID as UserID, r.DateInserted, r.ParentID')
        ->From('Action a')
        ->Join('Reaction r', 'a.ActionID = r.ActionID')
        ->WhereIn('r.ParentID', $IDs)
        ->Where('r.ParentType', $Type)
        ->OrderBy('r.DateInserted')
        ->Get()
        ->Result();
      
      foreach ($IDs as $ID) {
        self::$_Reactions[$Type . $ID] = array();
      }
      
      $UserIDs = array();
      // fill the cache
      foreach ($Result as $Reaction) {
        $UserIDs[] = $Reaction->UserID;
        self::$_Reactions[$Type . $Reaction->ParentID][] = $Reaction;
      }

      // Prime the user cache
      Gdn::UserModel()->GetIDs($UserIDs);
    }
  }

  /**
   * This updates the items score for future use in ranking and a best of controller
   *
   * @param int $ID The items ID
   * @param string $Type The type of the item (only supports 'discussion' and 'comment'
   * @param int $UserID The user that is scoring the item
   * @param int $Score What they give it
   * @return boolean Whether or not the the request was successful
   */
  private function SetUserScore($ID, $Type, $UserID, $Score) {
    $Model = FALSE;
    switch($Type) {
      default:
        return FALSE;
      case 'discussion':
        $Model = new DiscussionModel();
        break;
      case 'comment':
        $Model = new CommentModel();
        break;
    }

    if($Model) {
      $Model->SetUserScore($ID, $UserID, $Score);
      return TRUE;
    }
    else {
      return FALSE;
    }
  }
}
