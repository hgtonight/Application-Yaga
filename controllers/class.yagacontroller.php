<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * This is the base class for controllers throughout the gamification applicati0n.
 * 
 * @todo Determine if I need this
 * @since 1.0
 * @package Yaga
 */
class YagaController extends Gdn_Controller {

  /**
   * May need this in the future
   *
   * @access public
   */
  public function __construct() {
    parent::__construct();
  }

  /**
   * May use in the future
   *
   * @since 1.0
   * @access public
   */
  public function Initialize() {
    // Call Gdn_Controller's Initialize() as well.
    parent::Initialize();
  }
}
