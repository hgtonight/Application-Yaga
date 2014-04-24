<?php if (!defined('APPLICATION')) exit();
/* Copyright 2014 Zachary Doll */

/**
 * This shows the different filters you can apply to the entire forums scored content
 *
 * @since 1.0
 * @package Yaga
 */
class BestFilterModule extends Gdn_Module {
  
  public function __construct($Sender = '') {
    parent::__construct($Sender);
    
    $ActionModel = Yaga::ActionModel();
    $this->Data = $ActionModel->Get();
  }
  public function AssetTarget() {
    return 'Content';
  }

  public function ToString() {
    return parent::ToString();
  }

}
