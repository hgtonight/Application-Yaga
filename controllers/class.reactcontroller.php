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
   * This determines if the current user can react on this item with this action
   * 
   * @param string $Type valid options are 'discussion', 'comment', and 'activity'
   * @param int $ID
   * @param int $ActionID
   * @throws Gdn_UserException
   */
  public function Index($Type, $ID, $ActionID) {
    $Type = strtolower($Type);
    $Action = $this->ActionModel->GetAction($ActionID);
    
    // Make sure the action exists and the user is allowed to react    
    if(!$Action) {
      throw new Gdn_UserException(T('Yaga.InvalidAction'));
    }
    
    if(!Gdn::Session()->CheckPermission($Action->Permission)) {
      throw PermissionException();
    }
    
    switch($Type) {
      case 'discussion':
        $Model = new DiscussionModel();
        $AnchorID = '#Discussion_';
        $Key = 'InsertUserID';
        break;
      case 'comment':
        $Model = new CommentModel();
        $AnchorID = '#Comment_';
        $Key = 'InsertUserID';
        break;
      case 'activity':
        $Model = new ActivityModel();
        $AnchorID = '#Activity_';
        $Key = 'ActivityUserID';
        break;
      default:
        throw new Gdn_UserException(T('Yaga.InvalidReactType'));
        break;
    }
    
    $Item = $Model->GetID($ID);

    if($Item) {
      $Anchor = $AnchorID . $ID . ' .ReactMenu';
    }
    else {
      throw new Gdn_UserException(T('Yaga.InvalidID'));
    }
    
    $UserID = Gdn::Session()->UserID;
    
    switch($Type) {
      case 'comment':
      case 'discussion':
        $ItemOwnerID = $Item->InsertUserID;
        break;
      case 'activity':
        $ItemOwnerID = $Item['ActivityUserID'];
        break;
      default:
        throw new Gdn_UserException(T('Yaga.InvalidReactType'));
        break;
    }
    
    if($ItemOwnerID == $UserID) {
      throw new Gdn_UserException(T('Yaga.Error.ReactToOwn'));
    }

    // It has passed through the gauntlet
    $this->ReactionModel->SetReaction($ID, $Type, $ItemOwnerID, $UserID, $ActionID);
    
    $this->JsonTarget($Anchor, RenderReactions($ID, $Type, FALSE), 'ReplaceWith');
    
    // Don't render anything
    $this->Render('Blank', 'Utility', 'Dashboard');
  }
  
}
