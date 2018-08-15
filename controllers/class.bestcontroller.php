<?php if(!defined('APPLICATION')) exit();
/* Copyright 2014 Zachary Doll */

/**
 * This is all the frontend pages dealing with badges
 *
 * @since 1.0
 * @package Yaga
 */
class BestController extends Gdn_Controller {

  /**
   * The list of content the filters want to show
   * @var array
   */
  protected $_Content = array();
  
  /**
   * @var array These objects will be created on instantiation and available via
   * $this->ObjectName
   */
  public $Uses = array('ActedModel');

  /**
   * Initializes a frontend controller with the Best Filter, New Discussion, and
   * Discussion Filter modules.
   */
  public function Initialize() {
    parent::Initialize();
    $this->Application = 'Yaga';
    $this->Head = new HeadModule($this);
    $this->AddJsFile('jquery.js');
    $this->AddJsFile('jquery-ui.js');
    $this->AddJsFile('jquery.livequery.js');
    $this->AddJsFile('jquery.popup.js');
    $this->AddJsFile('global.js');
    $this->AddCssFile('style.css');
    $this->AddCssFile('reactions.css');
    $this->AddModule('BestFilterModule');
    $this->AddModule('NewDiscussionModule');
    $this->AddModule('DiscussionFilterModule');
  }

  /**
   * Default to showing the best of all time
   * 
   * @param int $Page What page of content should be shown
   */
  public function Index($Page = 0) {
    list($Offset, $Limit) = $this->_TranslatePage($Page);    
    $this->Title(T('Yaga.BestContent.Recent'));
    $this->_Content = $this->ActedModel->GetRecent($Limit, $Offset);
    $this->_BuildPager($Offset, $Limit, '/best/%1$s/');
    $this->SetData('ActiveFilter', 'Recent');
    $this->Render('index');
  }
  
  /**
   * Get the highest scoring content from all time
   *
   * @param int $Page What page of content should be shown
   */
  public function AllTime($Page = 0) {
    list($Offset, $Limit) = $this->_TranslatePage($Page); 
    $this->Title(T('Yaga.BestContent.AllTime'));
    $this->_Content = $this->ActedModel->GetBest(NULL, $Limit, $Offset);
    $this->_BuildPager($Offset, $Limit, '/best/alltime/%1$s/');
    $this->SetData('ActiveFilter', 'AllTime');
    $this->Render('index');
  }
  
  /**
   * Get the latest promoted content
   * 
   * @param int $ID Filter on a specific action ID
   * @param int $Page What page of content should be shown
   */
  public function Action($ID = NULL, $Page = 0) {
    if(is_null($ID) || !is_numeric($ID)) {
      $this->Index($Page);
      return;
    }
    $ActionModel = Yaga::ActionModel();
    $Action = $ActionModel->GetByID($ID);
    if(!$Action) {
      $this->Index($Page);
      return;
    }
    
    list($Offset, $Limit) = $this->_TranslatePage($Page);
    $this->Title(sprintf(T('Yaga.BestContent.Action'), $Action->Name));
    $this->_Content = $this->ActedModel->GetAction($ID, $Limit, $Offset);
    $this->_BuildPager($Offset, $Limit, '/best/action/' . $ID . '/%1$s/');
    $this->SetData('ActiveFilter', $ID);
    $this->Render('index');
  }
  
  /**
   * Converts a page number to an offset and limit useful for model queries.
   * 
   * @param int $Page What page of content should be shown
   * @return array An array containing the offset and limit
   */
  protected function _TranslatePage($Page) {
    list($Offset, $Limit) = OffsetLimit($Page, C('Yaga.BestContent.PerPage'));
    if(!is_numeric($Offset) || $Offset < 0) {
      $Offset = 0;
    }
    return array($Offset, $Limit);
  }
  
  /**
   * Builds a simple more/less pager to be rendered on the page
   * 
   * @param int $Offset
   * @param int $Limit
   * @param string $Link
   */
  protected function _BuildPager($Offset, $Limit, $Link) {
    $PagerFactory = new Gdn_PagerFactory();
    $this->Pager = $PagerFactory->GetPager('MorePager', $this);
    $this->Pager->MoreCode = 'More';
    $this->Pager->LessCode = 'Newer Content';
    $this->Pager->ClientID = 'Pager';
    $this->Pager->Configure(
       $Offset,
       $Limit,
       FALSE,
       $Link
    );
  }
}
