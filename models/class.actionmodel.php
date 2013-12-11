<?php if (!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * Describe the available actions one can react with to other user content.
 *
 * Events:
 *
 * @package Yaga
 * @since 1.0
 */

class ActionModel extends Gdn_Model {
  
  /**
   * This is used as a cache.
   * @var object
   */
  private static $_Actions = NULL;
  
  /**
   * Defines the related database table name.
   */
  public function __construct() {
    parent::__construct('Action');
  }

  /**
   * Returns a list of all available actions
   */
  public function Get() {
    if(empty(self::$_Actions)) {
      self::$_Actions = $this->SQL
              ->Select()
              ->From('Action')
              ->OrderBy('Sort')
              ->Get()
              ->Result();
      //decho('Filling the action cache.');
    }
    return self::$_Actions;
  }

  /**
   * Returns data for a specific action
   * 
   * @param int $ActionID
   * @return dataset
   */
  public function GetByID($ActionID) {
    $Action = $this->SQL
                    ->Select()
                    ->From('Action')
                    ->Where('ActionID', $ActionID)
                    ->Get()
                    ->FirstRow();
    return $Action;
  }
  
  /**
   * Gets the last inserted Action
   * 
   * @return DataSet
   */
  public function GetNewestAction() {
    $Action = $this->SQL
                    ->Select()
                    ->From('Action')
                    ->OrderBy('ActionID', 'desc')
                    ->Get()
                    ->FirstRow();
    return $Action;
  }

  /**
   * Determine if a specified action exists
   * 
   * @param int $ActionID
   * @return bool
   */
  public function Exists($ActionID) {
    $temp = $this->GetByID($ActionID);
    return !empty($temp);
  }

  /**
   * Remove an action from the db
   * 
   * @param int $ActionID
   * @param int $ReplacementID what action ID existing reactions should report
   * to. Null will delete the associated reactions.
   */
  public function Delete($ActionID, $ReplacementID = NULL) {
    if($this->Exists($ActionID)) {
      $this->SQL->Delete('Action', array('ActionID' => $ActionID));

      // replace the reaction table to move reactions to a new action
      if($ReplacementID && $this->Exists($ReplacementID)) {
        $this->SQL->Update('Reaction')
                ->Set('ActionID', $ReplacementID)
                ->Where('ActionID', $ActionID);
      }
      else {
        $this->SQL->Delete('Reaction', array('ActionID' => $ActionID));
      }
      return TRUE;
    }
    return FALSE;
  }
  
  public function SaveSort($SortArray) {
    foreach($SortArray as $Index => $Action) {
      // remove the 'Action_' prefix
      $ActionID = substr($Action, 7);
      $this->SetField($ActionID, 'Sort', $Index);
    }
    return TRUE;
  }

}