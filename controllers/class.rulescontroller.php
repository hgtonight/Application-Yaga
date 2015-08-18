<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * Manages the building of a rules cache and is provides admin functions for
 * managing badges in the dashboard.
 *
 * @since 1.0
 * @package Yaga
 */
class RulesController extends Gdn_Controller {

  /**
   * May be used in the future.
   *
   * @since 1.0
   * @access public
   */
  public function Initialize() {
    parent::Initialize();
    $this->Application = 'Yaga';
  }

  /**
   * This checks the cache for current rule set and expires once a day.
   * It loads all php files in the rules folder and selects only those that
   * implement the 'YagaRule' interface.
   *
   * @return array Rules that are currently available to use. The class names
   * are keys and the friendly names are values.
   */
  public static function GetRules() {
    $Rules = Gdn::Cache()->Get('Yaga.Badges.Rules');
    if($Rules === Gdn_Cache::CACHEOP_FAILURE) {
      foreach(glob(PATH_APPLICATIONS . DS . 'yaga' . DS . 'library' . DS . 'rules' . DS . '*.php') as $filename) {
        include_once $filename;
      }

      $TempRules = array();
      foreach(get_declared_classes() as $className) {
        if(in_array('YagaRule', class_implements($className))) {
          $Rule = new $className();
          $TempRules[$className] = $Rule->Name();
        }
      }
      
      // TODO: Don't reuse badge model?
      $Model = Yaga::BadgeModel();
      $Model->EventArguments['Rules'] = &$TempRules;
      $Model->FireAs = 'Yaga';
      $Model->FireEvent('AfterGetRules');
      
      asort($TempRules);
      if(empty($TempRules)) {
        $Rules = serialize(FALSE);
      }
      else{
        $Rules = serialize($TempRules);
      }
      Gdn::Cache()->Store('Yaga.Badges.Rules', $Rules, array(Gdn_Cache::FEATURE_EXPIRY => C('Yaga.Rules.CacheExpire', 86400)));
    }

    return unserialize($Rules);
  }
  
  /**
   * This checks the cache for current rule set that can be triggered for a user
   * by another user. It loads all rules and selects only those that return true
   * on its `Interacts()` method.
   *
   * @return array Rules that are currently available to use that are interactive.
   */
  public static function GetInteractionRules() {
    $Rules = Gdn::Cache()->Get('Yaga.Badges.InteractionRules');
    if($Rules === Gdn_Cache::CACHEOP_FAILURE) {
      $AllRules = RulesController::GetRules();

      $TempRules = array();
      foreach($AllRules as $ClassName => $Name) {
        $Rule = new $ClassName();
        if($Rule->Interacts()) {
          $TempRules[$ClassName] = $Name;
        }
      }
      if(empty($TempRules)) {
        $Rules = serialize(FALSE);
      }
      else{
        $Rules = serialize($TempRules);
      }
      
      Gdn::Cache()->Store('Yaga.Badges.InteractionRules', $Rules, array(Gdn_Cache::FEATURE_EXPIRY => C('Yaga.Rules.CacheExpire', 86400)));
    }

    return unserialize($Rules);
  }

  /**
   * This creates a new rule object in a safe way and renders its criteria form.
   *
   * @param string $RuleClass
   */
  public function GetCriteriaForm($RuleClass) {
    if(class_exists($RuleClass) && in_array('YagaRule', class_implements($RuleClass))) {
      $Rule = new $RuleClass();
      $Form = Gdn::Factory('Form');
      $Form->InputPrefix = '_Rules';
      $FormString = $Rule->Form($Form);
      $Description = $Rule->Description();
      $Name = $Rule->Name();

      $Data = array('CriteriaForm' => $FormString, 'RuleClass' => $RuleClass, 'Name' => $Name, 'Description' => $Description);
      $this->RenderData($Data);
    }
    else {
      $this->RenderException(new Gdn_UserException(T('Yaga.Error.Rule404')));
    }
  }
}
