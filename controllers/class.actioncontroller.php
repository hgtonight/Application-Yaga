<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * Manage actions that are available for reactions
 *
 * @since 1.0
 * @package Yaga
 */
class ActionController extends DashboardController {

  /**
   * @var array These objects will be created on instantiation and available via
   * $this->ObjectName
   */
  public $Uses = array('Form', 'ActionModel');

  /**
   * Make this look like a dashboard page and add the resources
   *
   * @since 1.0
   * @access public
   */
  public function Initialize() {
    parent::Initialize();
    Gdn_Theme::Section('Dashboard');
    if($this->Menu) {
      $this->Menu->HighlightRoute('/action');
    }
    $this->AddJsFile('jquery-ui-1.10.0.custom.min.js');
    $this->AddJsFile('admin.actions.js');
    $this->AddCssFile('reactions.css');
  }

  /**
   * Manage the available actions for reactions
   * 
   * @param int $Page
   */
  public function Settings($Page = '') {
    $this->Permission('Yaga.Reactions.Manage');
    $this->AddSideMenu('action/settings');

    $this->Title(T('Yaga.ManageReactions'));

    // Get list of actions from the model and pass to the view
    $this->SetData('Actions', $this->ActionModel->Get());

    $this->Render();
  }

  /**
   * Edit an existing action or add a new one
   * 
   * @param int $ActionID
   */
  public function Edit($ActionID = NULL) {
    $this->Permission('Yaga.Reactions.Manage');
    $this->AddSideMenu('action/settings');
    $this->Form->SetModel($this->ActionModel);
    
    $Edit = FALSE;
    $this->Title(T('Yaga.AddAction'));
    if($ActionID) {
      $this->Action = $this->ActionModel->GetByID($ActionID);
      $this->Form->AddHidden('ActionID', $ActionID);
      $Edit = TRUE;
      $this->Title(T('Yaga.EditAction'));
    }
    
    // This is just a list of all the images in the action icons folder
    $this->SetData('Icons', array('Happy', 'Happy2', 'Smiley', 'Smiley2', 'Tongue', 'Tongue2', 'Sad', 'Sad2', 'Wink', 'Wink2', 'Grin', 'Shocked', 'Confused', 'Confused2', 'Neutral', 'Neutral2', 'Wondering', 'Wondering2', 'PointUp', 'PointRight', 'PointDown', 'PointLeft', 'ThumbsUp', 'ThumbsUp2', 'Shocked2', 'Evil', 'Evil2', 'Angry', 'Angry2', 'Heart', 'Heart2', 'HeartBroken', 'Star', 'Star2', 'Grin2', 'Cool', 'Cool2', 'Question', 'Notification', 'Warning', 'Spam', 'Blocked', 'Eye', 'Eye2', 'EyeBlocked', 'Flag', 'BrightnessMedium', 'QuotesLeft', 'Music', 'Pacman', 'Bullhorn', 'Rocket', 'Fire', 'Hammer', 'Target', 'Lightning', 'Shield', 'CheckmarkCircle', 'Lab', 'Leaf', 'Dashboard', 'Droplet', 'Feed', 'Support', 'Hammer2', 'Wand', 'Cog', 'Gift', 'Trophy', 'Magnet', 'Switch', 'Globe', 'Bookmark', 'Bookmarks', 'Star3', 'Info', 'Info2', 'CancelCircle', 'Checkmark', 'Close'));
    
    // Load up all permissions
    $PermissionModel = new PermissionModel();
    $Permissions = $PermissionModel->PermissionColumns();
    unset($Permissions['PermissionID']);
    $PermissionKeys = array_keys($Permissions);
    $PermissionList = array_combine($PermissionKeys, $PermissionKeys);
    $this->SetData('Permissions', $PermissionList);
    
    if($this->Form->IsPostBack() == FALSE) {
      if(property_exists($this, 'Action')) {
        $this->Form->SetData($this->Action);
      }
      else {
        $this->Form->SetData(array('Permission' => 'Yaga.Reactions.Add'));
      }
    }
    else {
      if($this->Form->Save()) {
        if($Edit) {
          $Action = $this->ActionModel->GetByID($this->Form->GetFormValue('ActionID'));
        }
        else {
          $Action = $this->ActionModel->GetNewestAction();
        }
        
        $NewActionRow = ActionRow($Action);
        
        if($Edit) {
          $this->JsonTarget('#Action_' . $this->Action->ActionID, $NewActionRow, 'ReplaceWith');
          $this->InformMessage(T('Yaga.ActionUpdated'));
        }
        else {
          $this->JsonTarget('#Actions', $NewActionRow, 'Append');
          $this->InformMessage(T('Yaga.ActionAdded'));
        }
      }
    }

    $this->Render('add');
  }

  /**
   * Convenience function for nice URLs
   */
  public function Add() {
    $this->Edit();
  }

  /**
   * Remove the action via model.
   * 
   * @param int $ActionID
   */
  public function Delete($ActionID) {
    $Action = $this->ActionModel->GetID($ActionID);

    if(!$Action) {
      throw NotFoundException(T('Yaga.Action'));
    }

    $this->Permission('Yaga.Reactions.Manage');

    if($this->Form->IsPostBack()) {
      if(!$this->ActionModel->Delete($ActionID)) {
        $this->Form->AddError(sprintf(T('Yaga.Error.DeleteFailed'), T('Yaga.Action')));
      }

      if($this->Form->ErrorCount() == 0) {
        if($this->_DeliveryType === DELIVERY_TYPE_ALL) {
          Redirect('action/settings');
        }

        $this->JsonTarget('#ActionID_' . $ActionID, NULL, 'SlideUp');
      }
    }

    $this->AddSideMenu('badge/settings');
    $this->SetData('Title', T('Delete Reaction'));
    $this->Render();
  }
  
  public function Sort() {
      // Check permission
      $this->Permission('Garden.Reactions.Manage');
      
      // Set delivery type to true/false
      $TransientKey = GetIncomingValue('TransientKey');
      if (Gdn::Request()->IsPostBack()) {
         $SortArray = GetValue('SortArray', $_POST);
         $Saves = $this->ActionModel->SaveSort($SortArray);
         $this->SetData('Result', TRUE);
         $this->SetData('Saves', $Saves);
      }
         
      // Renders true/false rather than template  
      $this->Render();
   }
}
