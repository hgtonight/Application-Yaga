<?php if (!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * This contains static functions to get models and objects related to Yaga
 */
class Yaga {

  protected static $_ActionModel = NULL;
  protected static $_ReactionModel = NULL;
  protected static $_BadgeModel = NULL;
  protected static $_RankModel = NULL;
  protected static $_BadgeAwardModel = NULL;
  
  /**
   * Get a reference to the action model
   * @return ActionModel
   */
  public static function ActionModel() {
      if (is_null(self::$_ActionModel)) {
         self::$_ActionModel = new ActionModel();
      }
      return self::$_ActionModel;
   }
  
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
   * @return BadgeModel
   */
  public static function BadgeModel() {
      if (is_null(self::$_BadgeModel)) {
         self::$_BadgeModel = new BadgeModel();
      }
      return self::$_BadgeModel;
   }
   
   /**
   * Get a reference to the badge award model
   * @return BadgeAwardModel
   */
  public static function BadgeAwardModel() {
      if (is_null(self::$_BadgeAwardModel)) {
         self::$_BadgeAwardModel = new BadgeAwardModel();
      }
      return self::$_BadgeAwardModel;
   }
   
  /**
   * Get a reference to the rank model
   * @return RankModel
   */
  public static function RankModel() {
      if (is_null(self::$_RankModel)) {
         self::$_RankModel = new RankModel();
      }
      return self::$_RankModel;
   }
}
