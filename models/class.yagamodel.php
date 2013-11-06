<?php if (!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * Cross table functions
 * 
 * Events:
 * 
 * @package Yaga
 * @since 1.0
 */

class YagaModel extends Gdn_Model {
  private static $_Reactions = array();
  private static $_Actions = NULL;
  /**
   * Class constructor. Defines the related database table name.
   */
  public function __construct() {
    parent::__construct();
  }

  public function GetUserPoints($UserID) {
    // TODO: Move this to a calculated user column
    $Points = 0;
    
    // Reaction Points
    $ActionPoints = $this->SQL
            ->Select('a.ActionID, a.Name, a.AwardValue, COUNT(*) Count')
            ->From('Action a')
            ->Join('Reaction r', 'a.ActionID = r.ActionID')
            ->Where('ParentAuthorID', $UserID)
            ->GroupBy('a.ActionID')
            ->Get()
            ->Result();
    
    foreach($ActionPoints as $Action) {
      $Points += $Action->AwardValue * $Action->Count;
    }
    
    // Badge Award Points
    
    $BadgePoints = $this->SQL
            ->Select('b.AwardValue')
            ->From('BadgeAward ba')
            ->Join('Badge b', 'ba.BadgeID = b.BadgeID')
            ->Where('ba.UserID', $UserID)
            ->Get()
            ->Result();
    
    foreach($BadgePoints as $Badge) {
      $Points += $Badge->AwardValue;
    }
    
    return $Points;
  }
  /**
   * Returns a list of all available actions
   */
  public function GetActions() {
    if(empty(self::$_Actions)) {
      self::$_Actions = $this->SQL
              ->Select()
              ->From('Action')
              ->OrderBy('ActionID')
              ->Get()
              ->Result();
      //decho('Filling the action cache.');
    }
    return self::$_Actions;
  }
  
  /**
   * Returns data for a specific action
   * @param int $ActionID
   * @return dataset
   */
  public function GetActionID($ActionID) {
    return $this->SQL
                    ->Select()
                    ->From('Action')
                    ->Where('ActionID', $ActionID)
                    ->Get()
                    ->FirstRow();
  }

  /**
   * Returns the reactions associated a specified ID.
   * @param int $ID
   * @param enum $Type is the kind of ID. Valid values are comment and discussion
   */
  public function GetReactions($ID, $Type) {
    if(in_array($Type, array('discussion', 'comment', 'activity')) && $ID > 0) {
      $ReactionSet = array();
      if(empty(self::$_Reactions[$Type . $ID])) {
        foreach($this->GetActions() as $Index => $Action) {
          $ReactionSet[$Index]->ActionID = $Action->ActionID;
          $ReactionSet[$Index]->Name = $Action->Name;
          $ReactionSet[$Index]->Description = $Action->Description;
          $ReactionSet[$Index]->Tooltip = $Action->Tooltip;
          $ReactionSet[$Index]->CssClass = $Action->CssClass;
          $ReactionSet[$Index]->AwardValue = $Action->AwardValue;
          
          $Reactions = $this->SQL
                  ->Select('InsertUserID as UserID, DateInserted')
                  ->From('Reaction')
                  ->Where('ActionID', $Action->ActionID)
                  ->Where('ParentID', $ID)
                  ->Where('ParentType', $Type)
                  ->Get()
                  ->Result();               
          //decho($Reaction);
          foreach($Reactions as $Reaction) {
            $ReactionSet[$Index]->UserIDs[] = $Reaction->UserID;
            $ReactionSet[$Index]->Dates[] = $Reaction->DateInserted;
          }
        }

        //decho('Filling in the reaction cache for ' . $Type . $ID);
        self::$_Reactions[$Type . $ID] = $ReactionSet;
      }
      return self::$_Reactions[$Type . $ID];
    }
    else {
      return NULL;
    }
  }

  public function GetUserReaction($ID, $Type, $UserID) {
    return $this->SQL
            ->Select()
            ->From('Reaction')
            ->Where('ParentID', $ID)
            ->Where('ParentType', $Type)
            ->Where('InsertUserID', $UserID)
            ->Get()
            ->FirstRow();
  }
  
  public function GetUserReactionCount($UserID, $ActionID) {
    return $this->SQL
            ->Select()
            ->From('Reaction')
            ->Where('ActionID', $ActionID)
            ->Where('ParentAuthorID', $UserID)
            ->GetCount();
  }
  
  public function SetReaction($ID, $Type, $AuthorID, $UserID, $ActionID) {
    // clear the cache
    unset(self::$_Reactions[$Type . $ID]);

    $CurrentReaction = $this->GetUserReaction($ID, $Type, $UserID);
    if($CurrentReaction) {
      if($ActionID == $CurrentReaction->ActionID) {
        // remove the record
        return $this->SQL->Delete('Reaction', array('ParentID' => $ID,
                    'ParentType' => $Type,
                    'InsertUserID' => $UserID,
                    'ActionID' => $ActionID));
      }
      else {
        // update the record
        return $this->SQL
              ->Update('Reaction')
              ->Set('ActionID', $ActionID)
              ->Set('DateInserted', date(DATE_ISO8601))
              ->Where('ParentID', $ID)
              ->Where('ParentType', $Type)
              ->Where('InsertUserID', $UserID)
              ->Put();
      }
    }
    else {
      // insert a record
      return $this->SQL
              ->Insert('Reaction',
                      array('ActionID' => $ActionID,
                      'ParentID' =>  $ID,
                      'ParentType' => $Type,
                      'ParentAuthorID' => $AuthorID,
                      'InsertUserID' => $UserID,
                      'DateInserted' => date(DATE_ISO8601)));
    }
  }
}