<?php if (!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * This contains static functions to get models and objects related to Yaga
 * 
 * @package Yaga
 * @since 1.0
 */
class Yaga {

  /**
   * A single copy of ActionModel available to plugins and hooks files.
   * 
   * @var ActionModel
   */
  protected static $_ActionModel = NULL;
  
  /**
   * A single copy of ReactionModel available to plugins and hooks files.
   * 
   * @var ReactionModel
   */
  protected static $_ReactionModel = NULL;
  
  /**
   * A single copy of BadgeModel available to plugins and hooks files.
   * 
   * @var BadgeModel
   */
  protected static $_BadgeModel = NULL;
  
  /**
   * A single copy of RankModel available to plugins and hooks files.
   * 
   * @var RankModel
   */
  protected static $_RankModel = NULL;
  
  /**
   * A single copy of BadgeAwardModel available to plugins and hooks files.
   * 
   * @var BadgeAwardModel
   */
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
   
   /**
    * Alias for UserModel::GivePoints()
    * 
    * May be expanded in future versions.
    * 
    * @param int $UserID
    * @param int $Value
    * @param string $Source
    * @param int $Timestamp
    */
   public static function GivePoints($UserID, $Value, $Source = 'Other', $Timestamp = FALSE) {
     UserModel::GivePoints($UserID, $Value, $Source, $Timestamp);
   }
}
