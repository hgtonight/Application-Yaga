<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * All is the base class for controllers throughout the gamification applicati0n.
 *
 * @since 1.0
 * @package Yaga
 */
class BadgesController extends DashboardController {

  /** @var array List of objects to prep. They will be available as $this->$Name. */
  public $Uses = array('Form', 'BadgeModel');

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
      $this->Menu->HighlightRoute('/badges');
    }
    $this->AddJsFile('badges.js');
    $this->AddCssFile('badges.css');
  }

  public function Settings($Page = '') {
    $this->Permission('Yaga.Badges.Manage');
    $this->AddSideMenu('badges/settings');

    $this->Title('Manage Badges');

    // Get list of badges from the model and pass to the view
    $this->SetData('Badges', $this->BadgeModel->GetBadges());

    $this->Render();
  }

  public function Edit($BadgeID = NULL) {
    $this->Permission('Yaga.Badges.Manage');
    $this->AddSideMenu('badges/settings');
    $this->Form->SetModel($this->BadgeModel);

    $Edit = FALSE;
    if($BadgeID) {
      $this->Badge = $this->BadgeModel->GetBadge($BadgeID);
      $this->Form->AddHidden('BadgeID', $BadgeID);
      $Edit = TRUE;
    }

    if($this->Form->IsPostBack() == FALSE) {
      $this->Form->SetData($this->Badge);
    }
    else {
      if($this->Form->Save()) {
        $Upload = new Gdn_Upload();
        $TmpImage = $Upload->ValidateUpload('PhotoUpload_New', FALSE);

        if($TmpImage) {
          // Generate the target image name
          $TargetImage = $Upload->GenerateTargetName(PATH_UPLOADS);
          $ImageBaseName = pathinfo($TargetImage, PATHINFO_BASENAME);

          // Save the uploaded image
          $Parts = $Upload->SaveAs(
                  $TmpImage, $ImageBaseName
          );
          $this->Form->SetFormValue('Photo', $Parts['SaveName']);
        }

        if($Edit) {
          $Badge = $this->BadgeModel->GetBadge($this->Form->GetFormValue('BadgeID'));
        }
        else {
          $Badge = $this->BadgeModel->GetNewestBadge();
        }
        $NewBadgeRow = '<tr id="BadgeID_' . $Badge->BadgeID . '" data-badgeid="' . $Badge->BadgeID . '"' . ($Alt ? ' class="Alt"' : '') . '>';
        $NewBadgeRow .= '<td>' . Img($Badge->Photo) . '</td>';
        $NewBadgeRow .= "<td>$Badge->Name</td>";
        $NewBadgeRow .= "<td>$Badge->Description</td>";
        $NewBadgeRow .= "<td>$Badge->RuleClass</td>";
        $NewBadgeRow .= "<td>$Badge->RuleCriteria</td>";
        $NewBadgeRow .= "<td>$Badge->AwardValue</td>";
        $ToggleText = ($Badge->Enabled) ? T('Yes') : T('No');
        $NewBadgeRow .= '<td>' . Anchor($ToggleText, 'yaga/badges/toggle/' . $Badge->BadgeID, array('class' => 'Hijack')) . '</td>';
        $NewBadgeRow .= '<td>' . Anchor(T('Edit'), 'yaga/badges/edit/' . $Badge->BadgeID, array('class' => 'SmallButton')) . Anchor(T('Delete'), 'yaga/badges/delete/' . $Badge->BadgeID, array('class' => 'Danger PopConfirm SmallButton')) . '</td>';
        $NewBadgeRow .= '</tr>';
        if($Edit) {
          $this->JsonTarget('#BadgeID_' . $this->Badge->BadgeID, $NewBadgeRow, 'ReplaceWith');
          $this->InformMessage('Badge updated successfully!');
        }
        else {
          $this->JsonTarget('#Badges tbody', $NewBadgeRow, 'Append');
          $this->InformMessage('Badge added successfully!');
        }
      }
    }

    $this->Render('add');
  }

  public function Add() {
    $this->Edit();
  }

  public function Delete($BadgeID) {
    $this->Permission('Yaga.Badges.Manage');
    $this->AddSideMenu('badges/settings');

    $this->BadgeModel->DeleteBadge($BadgeID);

    redirect('badges/settings');
  }

  public function Toggle($BadgeID) {
    if(!$this->Request->IsPostBack()) {
      //throw PermissionException('Javascript');
    }
    $this->Permission('Yaga.Reactions.Manage');
    $this->AddSideMenu('badges/settings');
    
    $Badge = $this->BadgeModel->GetBadge($BadgeID);
    $Enable = (!$Badge->Enabled) ? TRUE : FALSE;
    $EnableText = ($Enable) ? 'Yes' : 'No';
    $this->BadgeModel->EnableBadge($BadgeID, $Enable);
    $this->JsonTarget('#BadgeID_' . $BadgeID . ' td:nth-child(7)', Wrap(Anchor($EnableText, 'yaga/badges/toggle/' . $BadgeID, array('class' => 'Hijack')), 'td'), 'ReplaceWith');
    $this->Render('Blank', 'Utility', 'Dashboard');
  }
}
