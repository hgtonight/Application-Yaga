<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * Contains management code for creating badges.
 *
 * @since 1.0
 * @package Yaga
 */
class RulesController extends YagaController {

  /** @var array List of objects to prep. They will be available as $this->$Name. */
  public $Uses = array();

  /**
   * If you use a constructor, always call parent.
   * Delete this if you don't need it.
   *
   * @access public
   */
  public function __construct() {
    parent::__construct();
  }

  /**
   * This is a good place to include JS, CSS, and modules used by all methods of this controller.
   *
   * Always called by dispatcher before controller's requested method.
   *
   * @since 1.0
   * @access public
   */
  public function Initialize() {
    parent::Initialize();
  }
  
  public static function GetRules() {
    $Filecache = new Gdn_Filecache();
    $Filecache->AddContainer(array(Gdn_Cache::CONTAINER_LOCATION=>'./cache/'));
    
    $Rules = $Filecache->Get('Yaga.Badges.Rules');
    if($Rules == Gdn_Cache::CACHEOP_FAILURE) {
      foreach(glob(PATH_APPLICATIONS . DS . 'yaga' . DS . 'rules' . DS . '*.php') as $filename) {
        include_once $filename;
      }
      
      $TempRules = array();
      foreach(get_declared_classes() as $className) {
        if(in_array('YagaRule', class_implements($className))) {
          $Rule = new $className();
          $TempRules[$className] = $Rule->FriendlyName();
        }
      }
      if(empty($TempRules)) {
        $Rules = serialize(FALSE);
      }
      else{
        $Rules = serialize($TempRules);
      }
      $Filecache->Store('Yaga.Badges.Rules', $Rules, array(Gdn_Cache::FEATURE_EXPIRY => C('Yaga.Rules.CacheExpire', 86400)));
    }
    
    return unserialize($Rules);
  }
  
  public function GetCriteriaForm($RuleClass) {
    try {
      $Rule = new $RuleClass();
    }
    catch (Exception $ex) {
      $this->ErrorMessage('Unknown class: ' . $ex->getMessage());
      return;
    }
    $Form = Gdn::Factory('Form');
    $Form->InputPrefix = '_Rules';
    $FormString = $Rule->RenderCriteriaInterface($Form, FALSE);
    $Description = $Rule->Description();
    
    $Data = array( 'CriteriaForm' => $FormString, 'RuleClass' => $RuleClass, 'Description' => $Description);
    $this->RenderData($Data);
  }
}
