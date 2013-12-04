<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * This is all the frontend pages dealing with badges
 *
 * @since 1.0
 * @package Yaga
 */
class BestOfController extends Gdn_Controller {

  /**
   * @var array These objects will be created on instantiation and available via
   * $this->ObjectName
   */
  public $Uses = array('BadgeModel');

  public function Initialize() {
    parent::Initialize();
    $this->Head = new HeadModule($this);
    $this->AddJsFile('jquery.js');
    $this->AddJsFile('jquery-ui.js');
    $this->AddJsFile('jquery.livequery.js');
    $this->AddJsFile('jquery.popup.js');
    $this->AddJsFile('global.js');
    $this->AddCssFile('style.css');
  }

  /**
   * Render a blank page if no methods were specified in dispatch
   */
  public function Index() {
    $this->All();
  }
  
  /**
   * This renders out the full list of badges
   */
  public function All() {
    $this->Title(T('Best of Everything'));
    $Module = new PromotedContentModule();
    $Module->Selector = 'Score';
    
    $this->SetData('Module', $Module);
    $this->Title(T('Best Of...'));
    $this->Render('all');
  }
}
