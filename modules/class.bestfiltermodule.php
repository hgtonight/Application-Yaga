<?php if (!defined('APPLICATION')) exit();
/* Copyright 2014 Zachary Doll */

/**
 * This shows the different filters you can apply to the entire forums scored content
 *
 * @package Yaga
 * @since 1.0
 */
class BestFilterModule extends Gdn_Module {
  
  /**
   * Load up the action list.
   * 
   * @param string $Sender
   */
  public function __construct($Sender = '') {
    parent::__construct($Sender);
    
    $ActionModel = Yaga::ActionModel();
    $actions = $ActionModel->Get();
    
    foreach($actions as $index => $action) {
        if($action->AwardValue < 0) {
            unset($actions[$index]);
        }
    }
    
    $this->Data = $actions;
  }
  
  /**
   * Specifies the asset this module should be rendered to.
   * 
   * @return string
   */
  public function AssetTarget() {
    return 'Content';
  }

  /**
   * Renders an action list.
   * 
   * @return string
   */
  public function ToString() {
    return parent::ToString();
  }

}
