<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * All is the base class for controllers throughout the gamification applicati0n.
 *
 * @since 1.0
 * @package Yaga
 */
class ActionsController extends DashboardController {

  /** @var array List of objects to prep. They will be available as $this->$Name. */
  public $Uses = array('Form', 'ActionModel');

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
    parent::Initialize();
    Gdn_Theme::Section('Dashboard');
    if($this->Menu) {
      $this->Menu->HighlightRoute('/actions');
    }
    $this->AddJsFile('actions.js');
    $this->AddCssFile('reactions.css');
  }

  public function Settings($Page = '') {
    $this->Permission('Yaga.Reactions.Manage');
    $this->AddSideMenu('actions/settings');

    $this->Title('Manage Reactions');

    // Get list of actions from the model and pass to the view
    $this->SetData('Actions', $this->ActionModel->GetActions());

    $this->Render();
  }

  public function Edit($ActionID = NULL) {
    $this->Permission('Yaga.Reactions.Manage');
    $this->AddSideMenu('actions/settings');
    $this->Form->SetModel($this->ActionModel);
    
    $Edit = FALSE;
    if($ActionID) {
      $this->Action = $this->ActionModel->GetAction($ActionID);
      $this->Form->AddHidden('ActionID', $ActionID);
      $Edit = TRUE;
    }
    
    if($this->Form->IsPostBack() == FALSE) {
      $this->Form->SetData($this->Action);
    }
    else {
      if($this->Form->Save()) {
        if($Edit) {
          $Action = $this->ActionModel->GetAction($this->Form->GetFormValue('ActionID'));
        }
        else {
          $Action = $this->ActionModel->GetNewestAction();
        }
        $NewActionRow = '<tr id="ActionID_' . $Action->ActionID . '" data-actionid="'. $Action->ActionID . '">';
        $NewActionRow .= "<td>$Action->Name</td>";
        $NewActionRow .= '<td><span class="ReactSprite ' . $Action->CssClass . '"> </span></td>';
        $NewActionRow .= "<td>$Action->Description</td>";
        $NewActionRow .= "<td>$Action->Tooltip</td>";
        $NewActionRow .= "<td>$Action->AwardValue</td>";
        $NewActionRow .= '<td>' . Anchor(T('Edit'), 'yaga/actions/edit/' . $Action->ActionID, array('class' => 'Popup SmallButton')) . Anchor(T('Delete'), 'yaga/actions/delete/' . $Action->ActionID, array('class' => 'Hijack SmallButton')) . '</td>';
        $NewActionRow .= '</tr>';
        if($Edit) {
          $this->JsonTarget('#ActionID_' . $this->Action->ActionID, $NewActionRow, 'ReplaceWith');
          $this->InformMessage('Action updated successfully!');
        }
        else {
          $this->JsonTarget('#Actions tbody', $NewActionRow, 'Append');
          $this->InformMessage('Action added successfully!');
        }
      }
    }

    $this->Render('add');
  }

  public function Add() {
    $this->Edit();
  }

  public function Delete($ActionID) {
    if(!$this->Request->IsPostBack()) {
      throw PermissionException('Javascript');
    }
    $this->Permission('Yaga.Reactions.Manage');
    $this->AddSideMenu('actions/settings');
    
    $this->ActionModel->DeleteAction($ActionID);
    
    $this->JsonTarget('#ActionID_' . $ActionID, null, 'SlideUp');
    $this->Render('Blank', 'Utility', 'Dashboard');
  }
}
