<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * This handles all the AJAX requests to actually react to user generated content.
 *
 * @since 1.0
 * @package Yaga
 */
class ReactController extends Gdn_Controller {

  /**
   * @var array These objects will be created on instantiation and available via
   * $this->ObjectName
   */
  public $Uses = array('ActionModel', 'ReactionModel');

  /**
   * All requests to this controller must be made via JS.
   * 
   * @throws PermissionException
   */
  public function Initialize() {
    parent::Initialize();
    if(!$this->Request->IsPostBack()) {
      throw PermissionException('Javascript');
    }
  }

  /**
   * Render a blank page if no methods were specified in dispatch
   */
  public function Index() {
    $this->Render('Blank', 'Utility', 'Dashboard');
  }
  
  /**
   * Makes the current user react to a specific discussion
   * 
   * @todo Consider merging with comment and activity methods
   * @param int $DiscussionID
   * @param int $ActionID The action to use
   * @throws Gdn_UserException Any funny business and your inform messages will
   * tell you!
   */
  public function Discussion($DiscussionID, $ActionID) {
    // check to see if allowed to react
    $this->Permission('Plugins.Reactions.Add');
    
    if(!$this->ActionModel->ActionExists($ActionID)) {
      throw new Gdn_UserException('Invalid Action');
    }
    
    $DiscussionModel = new DiscussionModel();
    $Discussion = $DiscussionModel->GetID($DiscussionID);
    
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
    $this->ReactionModel->SetReaction($DiscussionID, 'discussion', $Discussion->InsertUserID, $UserID, $ActionID);
    
    $this->JsonTarget($Anchor, $this->_RenderActions($DiscussionID, 'discussion', FALSE), 'ReplaceWith');
    
    $this->Render('Blank', 'Utility', 'Dashboard');
  }
  
  /**
   * Makes the current user react to a specific comment
   * 
   * @todo Consider merging with discussion and activity methods 
   * @param int $CommentID
   * @param int $ActionID The action to use
   * @throws Gdn_UserException Any funny business and your inform messages will
   * tell you!
   */
  public function Comment($CommentID, $ActionID) {
    // check to see if allowed to react
    $this->Permission('Plugins.Reactions.Add');
    
    if(!$this->ActionModel->ActionExists($ActionID)) {
      throw new Gdn_UserException('Invalid Action');
    }
    
    $CommentModel = new CommentModel();
    $Comment = $CommentModel->GetID($CommentID);
    
    if($Comment) {
      $Anchor = '#Comment_' . $CommentID . ' .ReactMenu';
    }
    else {
      throw new Gdn_UserException('Invalid ID');
    }
    
    $UserID = Gdn::Session()->UserID;
    
    if($Comment->InsertUserID == $UserID) {
      throw new Gdn_UserException('You cannot react to your own content.');
    }

    // It has passed through the gauntlet
    $this->ReactionModel->SetReaction($CommentID, 'comment', $Comment->InsertUserID, $UserID, $ActionID);
    
    $this->JsonTarget($Anchor, $this->_RenderActions($CommentID, 'comment', FALSE), 'ReplaceWith');
    
    $this->Render('Blank', 'Utility', 'Dashboard');
  }
  
  /**
   * Makes the current user react to a specific activity
   * 
   * @todo Consider merging with discussion and comment methods 
   * @param int $ActivityID
   * @param int $ActionID The action to use
   * @throws Gdn_UserException Any funny business and your inform messages will
   * tell you!
   */
  public function Activity($ActivityID, $ActionID) {
    // check to see if allowed to react
    $this->Permission('Plugins.Reactions.Add');
    
    if(!$this->ActionModel->ActionExists($ActionID)) {
      throw new Gdn_UserException('Invalid Action');
    }
    
    $ActivityModel = new ActivityModel();
    $Activity = $ActivityModel->GetID($ActivityID);
    
    if($Activity) {
      $Anchor = '#Activity_' . $ActivityID . ' .ReactMenu';
    }
    else {
      throw new Gdn_UserException('Invalid ID');
    }
    
    $UserID = Gdn::Session()->UserID;
    
    if($Activity->InsertUserID == $UserID) {
      throw new Gdn_UserException('You cannot react to your own content.');
    }

    // It has passed through the gauntlet
    $this->ReactionModel->SetReaction($ActivityID, 'activity', $Activity->InsertUserID, $UserID, $ActionID);
    
    $this->JsonTarget($Anchor, $this->_RenderActions($ActivityID, 'activity', FALSE), 'ReplaceWith');
    
    $this->Render('Blank', 'Utility', 'Dashboard');
  }

  private function _RenderActions($ID, $Type, $Echo = TRUE) {
    $Reactions = $this->ReactionModel->GetReactions($ID, $Type);
    $ActionsString = '';
    foreach($Reactions as $Action) {
      $ActionsString .= Anchor(
              Wrap('&nbsp;', 'span', array('class' => 'ReactSprite React-' . $Action->ActionID . ' ' . $Action->CssClass)) .
              WrapIf(count($Action->UserIDs), 'span', array('class' => 'Count')) .
              Wrap($Action->Name, 'span', array('class' => 'ReactLabel')), 'react/' . $Type . '/' . $ID . '/' . $Action->ActionID,
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
