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

    $this->Title(T('Manage Reactions'));

    // Get list of actions from the model and pass to the view
    $this->SetData('Actions', $this->ActionModel->GetActions());

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
    $this->Title(T('Add Action'));
    if($ActionID) {
      $this->Action = $this->ActionModel->GetAction($ActionID);
      $this->Form->AddHidden('ActionID', $ActionID);
      $Edit = TRUE;
      $this->Title(T('Edit Action'));
    }
    
    // TODO: Autoload these, or something
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
          $Action = $this->ActionModel->GetAction($this->Form->GetFormValue('ActionID'));
        }
        else {
          $Action = $this->ActionModel->GetNewestAction();
        }
        $NewActionRow = Wrap(
            Wrap(
                    Anchor(T('Edit'), 'yaga/action/edit/' . $Action->ActionID, array('class' => 'Popup SmallButton')) . Anchor(T('Delete'), 'yaga/action/delete/' . $Action->ActionID, array('class' => 'Hijack SmallButton')), 'div', array('class' => 'Tools')) .
            Wrap(
                    Wrap($Action->Name, 'h4') .
                    Wrap(
                            Wrap($Action->Description, 'span') . ' ' .
                            Wrap(Plural($Action->AwardValue, '%s Point', '%s Points'), 'span'), 'div', array('class' => 'Meta')) .
                    Wrap(
                            Wrap('&nbsp;', 'span', array('class' => 'ReactSprite React-' . $Action->ActionID . ' ' . $Action->CssClass)) .
                            WrapIf(rand(0, 18), 'span', array('class' => 'Count')) .
                            Wrap($Action->Name, 'span', array('class' => 'ReactLabel')), 'div', array('class' => 'Preview Reactions')), 'div', array('class' => 'Action')), 'li', array('id' => 'Action_' . $Action->ActionID));
        if($Edit) {
          $this->JsonTarget('#Action_' . $this->Action->ActionID, $NewActionRow, 'ReplaceWith');
          $this->InformMessage(T('Action updated successfully!'));
        }
        else {
          $this->JsonTarget('#Actions', $NewActionRow, 'Append');
          $this->InformMessage(T('Action added successfully!'));
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
   * @todo Consider adding a confirmation page when not using JS
   * @param int $ActionID
   */
  public function Delete($ActionID) {
    $this->Permission('Yaga.Reactions.Manage');
    
    $this->ActionModel->DeleteAction($ActionID);

    redirect('action/settings');
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
