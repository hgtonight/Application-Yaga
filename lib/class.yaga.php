<?php if (!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * This contains static functions to get models and objects related to Yaga
 */
class Yaga {

  protected static $_ReactionModel = NULL;
  protected static $_BadgeModel = NULL;
  
  /**
   * Get a reference to the reaction model
   * @return ReactionModel
   */
  public static function ReactionModel() {
      if (is_null(self::$_ReactionModel)) {
         self::$_ReactionModel = new ReactionModel();
      }
      return self::$_ReactionModel;
   }
   
   /**
   * Get a reference to the badge model
   * @return ReactionModel
   */
  public static function BadgeModel() {
      if (is_null(self::$_BadgeModel)) {
         self::$_BadgeModel = new BadgeModel();
      }
      return self::$_BadgeModel;
   }
}
