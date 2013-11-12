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
   * 
   * @param int $ActionID
   * @return dataset
   */
  public function GetAction($ActionID) {
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
  public function ActionExists($ActionID) {
    $temp = $this->GetAction($ActionID);
    return !empty($temp);
  }

  /**
   * Remove an action from the db
   * 
   * @param int $ActionID
   * @param int $ReplacementID what action ID existing reactions should report
   * to. Null will delete the associated reactions.
   */
  public function DeleteAction($ActionID, $ReplacementID = NULL) {
    if($this->ActionExists($ActionID)) {
      $this->SQL->Delete('Action', array('ActionID' => $ActionID));
      // TODO: Ask the user if they want to delete reactions or lump them in
      // with another action
      if($ReplacementID && $this->ActionExists($ReplacementID)) {
        $this->SQL->Update('Reaction')
                ->Set('ActionID', $ReplacementID)
                ->Where('ActionID', $ActionID);
      }
      else {
        $this->SQL->Delete('Reaction', array('ActionID' => $ActionID));
      }
    }
  }

}