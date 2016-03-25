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
    $this->Application = 'Yaga';
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
    $Action = $this->ActionModel->GetByID($ActionID);

    // Make sure the action exists and the user is allowed to react
    if(!$Action) {
      throw new Gdn_UserException(T('Yaga.Action.Invalid'));
    }

    if(!Gdn::Session()->CheckPermission($Action->Permission)) {
      throw PermissionException();
    }

    $Item = null;
    $AnchorID = '#' . ucfirst($Type) . '_';
    $ItemOwnerID = 0;

    if(in_array($Type, array('discussion', 'comment'))) {
      $Item = GetRecord($Type, $ID);
    }
    else if($Type == 'activity') {
      $Model = new ActivityModel();
      $Item = $Model->GetID($ID, DATASET_TYPE_ARRAY);
    }
    else {
      $this->EventArguments = array(
        'TypeFound' => FALSE,
        'TargetType' => $Type,
        'TargetID' => $ID,
        'Item' => &$Item,
        'AnchorID' => &$AnchorID,
        'ItemOwnerID' => &$ItemOwnerID
      );
      $this->FireEvent('CustomType');
      
      if(!$this->EventArguments['TypeFound']) {
        throw new Gdn_UserException(T('Yaga.Action.InvalidTargetType'));
      }
    }

    if($Item) {
      $Anchor = $AnchorID . $ID;
    }
    else {
      throw new Gdn_UserException(T('Yaga.Action.InvalidTargetID'));
    }

    $UserID = Gdn::Session()->UserID;

    switch($Type) {
      case 'comment':
      case 'discussion':
        $ItemOwnerID = $Item['InsertUserID'];
        break;
      case 'activity':
        $ItemOwnerID = $Item['RegardingUserID'];
        break;
      default:
        break;
    }

    if($ItemOwnerID == $UserID) {
      throw new Gdn_UserException(T('Yaga.Error.ReactToOwn'));
    }

    // It has passed through the gauntlet
    $this->ReactionModel->Set($ID, $Type, $ItemOwnerID, $UserID, $ActionID);

    $this->JsonTarget($Anchor . ' .ReactMenu', RenderReactionList($ID, $Type), 'ReplaceWith');
    $this->JsonTarget($Anchor . ' .ReactionRecord', RenderReactionRecord($ID, $Type), 'ReplaceWith');

    // Don't render anything
    $this->Render('Blank', 'Utility', 'Dashboard');
  }

}
