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
   * A single copy of ActedModel available to plugins and hooks files.
   * 
   * @since 1.1
   * @var ActedModel
   */
  protected static $_ActedModel = NULL;
  
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
   * Get a reference to the acted model
   * @since 1.1
   * @return ActedModel
   */
  public static function ActedModel() {
      if (is_null(self::$_ActedModel)) {
         self::$_ActedModel = new ActedModel();
      }
      return self::$_ActedModel;
   }
  
  /**
   * Get a reference to the action model
   * @since 1.0
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
   * @since 1.0
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
   * @since 1.0
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
   * @since 1.0
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
   * @since 1.0
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
    * @since 1.1
    * @param int $UserID
    * @param int $Value
    * @param string $Source
    * @param int $Timestamp
    */
   public static function GivePoints($UserID, $Value, $Source = 'Other', $Timestamp = FALSE) {
     if($UserID == Gdn::userModel()->getSystemUserID()) {
         return;
     }
     UserModel::GivePoints($UserID, $Value, $Source, $Timestamp);
   }
   
   /**
   * This is the dispatcher to check badge awards
   *
   * @param mixed $Sender The sending object
   * @param string $Handler The event handler to check associated rules for awards
   * (e.g. BadgeAwardModel_AfterBadgeAward_Handler or Base_AfterConnection)
   * @since 1.1
   */
   public static function ExecuteBadgeHooks($Sender, $Handler) {
    $Session = Gdn::Session();
    if(!C('Yaga.Badges.Enabled') || !$Session->IsValid()) {
      return;
    }

    // Let's us use __FUNCTION__ in the original hook
    $Hook = strtolower(str_ireplace('_Handler', '', $Handler));

    $UserID = $Session->UserID;
    $User = $Session->User;

    $BadgeAwardModel = Yaga::BadgeAwardModel();
    $Badges = $BadgeAwardModel->GetUnobtained($UserID);

    $InteractionRules = RulesController::GetInteractionRules();

    $Rules = array();
    foreach($Badges as $Badge) {
      // The badge award needs to be processed
      if(($Badge->Enabled && $Badge->UserID != $UserID)
         || array_key_exists($Badge->RuleClass, $InteractionRules)) {
        // Create a rule object if needed
        $Class = $Badge->RuleClass;
        if(!in_array($Class, $Rules) && class_exists($Class)) {
          $Rule = new $Class();
          $Rules[$Class] = $Rule;
        }
        else {
          if(!array_key_exists('UnknownRule', $Rules)) {
            $Rules['UnkownRule'] = new UnknownRule();
          }
          $Rules[$Class] = $Rules['UnkownRule'];
        }

        $Rule = $Rules[$Class];
        
        // Only check awards for rules that use this hook
        $Hooks = array_map('strtolower',$Rule->Hooks());
        if(in_array($Hook, $Hooks)) {
          $Criteria = (object) unserialize($Badge->RuleCriteria);
          $Result = $Rule->Award($Sender, $User, $Criteria);
          if($Result) {
            $AwardedUserIDs = array();
            if(is_array($Result)) {
              $AwardedUserIDs = $Result;
            }
            else if(is_numeric($Result)) {
              $AwardedUserIDs[] = $Result;
            }
            else {
              $AwardedUserIDs[] = $UserID;
            }
            
            $systemUserID = Gdn::userModel()->getSystemUserID();
            foreach($AwardedUserIDs as $AwardedUserID) {
              if($AwardedUserID == $systemUserID) {
                  continue;
              }
              $BadgeAwardModel->Award($Badge->BadgeID, $AwardedUserID, $UserID);
            }
          }
        }
      }
    }
  }
}
