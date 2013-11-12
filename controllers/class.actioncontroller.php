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

    $this->Title('Manage Reactions');

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
    if($ActionID) {
      $this->Action = $this->ActionModel->GetAction($ActionID);
      $this->Form->AddHidden('ActionID', $ActionID);
      $Edit = TRUE;
    }
    
    if($this->Form->IsPostBack() == FALSE) {
      if(property_exists($this, 'Action')) {
        $this->Form->SetData($this->Action);
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
        $NewActionRow = '<tr id="ActionID_' . $Action->ActionID . '" data-actionid="'. $Action->ActionID . '">';
        $NewActionRow .= "<td>$Action->Name</td>";
        $NewActionRow .= '<td><span class="ReactSprite ' . $Action->CssClass . '"> </span></td>';
        $NewActionRow .= "<td>$Action->Description</td>";
        $NewActionRow .= "<td>$Action->Tooltip</td>";
        $NewActionRow .= "<td>$Action->AwardValue</td>";
        $NewActionRow .= '<td>' . Anchor(T('Edit'), 'yaga/action/edit/' . $Action->ActionID, array('class' => 'Popup SmallButton')) . Anchor(T('Delete'), 'yaga/action/delete/' . $Action->ActionID, array('class' => 'Hijack SmallButton')) . '</td>';
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
}
