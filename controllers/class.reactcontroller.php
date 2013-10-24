<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * All is the base class for controllers throughout the gamification applicati0n.
 *
 * @since 1.0
 * @package Yaga
 */
class ReactController extends YagaController {

  /** @var array List of objects to prep. They will be available as $this->$Name. */
  public $Uses = array('DiscussionModel', 'ActivityModel', 'CommentModel', 'ActionModel', 'ReactionModel');

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
    if(!$this->Request->IsPostBack()) {
      //throw PermissionException('Javascript');
    }

    $this->AddJsFile('yaga.js');
    $this->AddCssFile('yaga.css');
  }

  public function Index() {
    decho($this);
    $this->Render('Blank', 'Utility', 'Dashboard');
  }
  
  public function Discussion($DiscussionID, $ActionID) {
    // check to see if allowed to react
    $this->Permission('Plugins.Reactions.Add');
    
    if($this->ActionModel->GetAction($ActionID)) {
      throw new Gdn_UserException('Invalid Action');
    }
    
    $Discussion = $this->DiscussionModel->GetID($DiscussionID);
    
    if($Discussion) {
      $Anchor = '#Discussion_' . $DiscussionID . ' .ReactMenu';
    }
    else {
      throw new Gdn_UserException('Invalid ID');
    }
    
    $UserID = Gdn::Session()->UserID;
    
    if($Discussion->InsertUserID == $UserID) {
      throw new Gdn_UserException('You cannot react to your own content.');
    }

    // It has passed through the gauntlet
    $this->ReactionModel->SetReaction($DiscussionID, 'discussion', $UserID, $ActionID);
    
    $this->JsonTarget($Anchor, $this->_RenderActions($DiscussionID, 'discussion', FALSE), 'ReplaceWith');
    
    $this->Render('Blank', 'Utility', 'Dashboard');
  }
  
  public function Comment($DiscussionID, $ActionID) {
    // check to see if allowed to react
    $this->Permission('Plugins.Reactions.Add');
    
    if($this->ActionModel->GetAction($ActionID)) {
      throw new Gdn_UserException('Invalid Action');
    }
    
    $Discussion = $this->DiscussionModel->GetID($DiscussionID);
    
    if($Discussion) {
      $Anchor = '#Discussion_' . $DiscussionID . ' .ReactMenu';
    }
    else {
      throw new Gdn_UserException('Invalid ID');
    }
    
    $UserID = Gdn::Session()->UserID;
    
    if($Discussion->InsertUserID == $UserID) {
      throw new Gdn_UserException('You cannot react to your own content.');
    }

    // It has passed through the gauntlet
    $this->ReactionModel->SetReaction($DiscussionID, 'discussion', $UserID, $ActionID);
    
    $this->JsonTarget($Anchor, $this->_RenderActions($DiscussionID, 'discussion', FALSE), 'ReplaceWith');
    
    $this->Render('Blank', 'Utility', 'Dashboard');
  }
  
  public function Activity($DiscussionID, $ActionID) {
    // check to see if allowed to react
    $this->Permission('Plugins.Reactions.Add');
    
    if($this->ActionModel->GetAction($ActionID)) {
      throw new Gdn_UserException('Invalid Action');
    }
    
    $Discussion = $this->DiscussionModel->GetID($DiscussionID);
    
    if($Discussion) {
      $Anchor = '#Discussion_' . $DiscussionID . ' .ReactMenu';
    }
    else {
      throw new Gdn_UserException('Invalid ID');
    }
    
    $UserID = Gdn::Session()->UserID;
    
    if($Discussion->InsertUserID == $UserID) {
      throw new Gdn_UserException('You cannot react to your own content.');
    }

    // It has passed through the gauntlet
    $this->ReactionModel->SetReaction($DiscussionID, 'discussion', $UserID, $ActionID);
    
    $this->JsonTarget($Anchor, $this->_RenderActions($DiscussionID, 'discussion', FALSE), 'ReplaceWith');
    
    $this->Render('Blank', 'Utility', 'Dashboard');
  }

  private function _RenderActions($ID, $Type, $Echo = TRUE) {
    $Reactions = $this->ReactionModel->GetReactions($ID, $Type);
    //decho($Reactions);
    $ActionsString = '';
    foreach($Reactions as $Action) {
      $ActionsString .= Anchor(
              Wrap('&nbsp;', 'span', array('class' => 'ReactSprite React-' . $Action->ActionID)) .
              WrapIf(count($Action->UserIDs), 'span', array('class' => 'Count')) .
              Wrap($Action->Name, 'span', array('class' => 'ReactLabel')), 'discussion/react/' . $Type . '/' . $ID . '/' . $Action->ActionID,
              'Hijack ReactButton'
      );
    }
    
    $AllActionsString = Wrap($ActionsString, 'span', array('class' => 'ReactMenu'));
    
    if($Echo) {
      echo $AllActionsString;
    }
    else {
      return $AllActionsString;
    }
    
  }
}
