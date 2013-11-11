<?php if (!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * Reactions
 * 
 * Events:
 * 
 * @package Yaga
 * @since 1.0
 */

class ReactionModel extends Gdn_Model {
  private static $_Reactions = array();
  private static $_Actions = NULL;
  /**
   * Class constructor. Defines the related database table name.
   */
  public function __construct() {
    parent::__construct('Reaction');
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
          if(empty($ReactionSet[$Index]->UserIDs)) {
            $ReactionSet[$Index]->UserIDs = array();
          }
            
        }

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
        $Reaction = $this->SQL->Delete('Reaction', array('ParentID' => $ID,
                    'ParentType' => $Type,
                    'InsertUserID' => $UserID,
                    'ActionID' => $ActionID));
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
    }
    $EventArgs['Reaction'] = $Reaction;
    $this->FireEvent('AfterReaction', $EventArgs);
    
    return $Reaction;
  }
}