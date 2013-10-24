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

class ActionModel extends Gdn_Model {
  private static $_Actions = NULL;
  /**
   * Class constructor. Defines the related database table name.
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
   * @param int $ActionID
   * @return dataset
   */
  public function GetAction($ActionID) {
    return $this->SQL
                    ->Select()
                    ->From('Action')
                    ->Where('ActionID', $ActionID)
                    ->Get()
                    ->FirstRow();
  }
  
}