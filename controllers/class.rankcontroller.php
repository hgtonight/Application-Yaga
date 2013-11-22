<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * Contains management code for designing ranks.
 *
 * @since 1.0
 * @package Yaga
 */
class RankController extends DashboardController {

  /**
   * @var array These objects will be created on instantiation and available via
   * $this->ObjectName
   */
  public $Uses = array('Form', 'RankModel');

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
      $this->Menu->HighlightRoute('/rank');
    }
    $this->AddJsFile('admin.ranks.js');
    $this->AddCssFile('ranks.css');
  }

  /**
   * Manage the current ranks and add new ones
   *
   * @param int $Page
   */
  public function Settings($Page = '') {
    $this->Permission('Yaga.Ranks.Manage');
    $this->AddSideMenu('rank/settings');

    $this->Title(T('Manage Ranks'));

    // Get list of ranks from the model and pass to the view
    $this->SetData('Ranks', $this->RankModel->GetRanks());

    $this->Render();
  }

  /**
   * Edit an existing rank or add a new one
   *
   * @param int $RankID
   * @throws ForbiddenException if no proper rules are found
   */
  public function Edit($RankID = NULL) {
    $this->Permission('Yaga.Ranks.Manage');
    $this->AddSideMenu('rank/settings');
    $this->Form->SetModel($this->RankModel);

    $this->Title(T('Add Rank'));
    $Edit = FALSE;
    if($RankID) {
      $this->Rank = $this->RankModel->GetRank($RankID);
      $this->Form->AddHidden('RankID', $RankID);
      $Edit = TRUE;
      $this->Title(T('Edit Rank'));
    }
    
     // Load up all permissions
    $PermissionModel = new PermissionModel();
    $Permissions = $PermissionModel->PermissionColumns();
    unset($Permissions['PermissionID']);
    $PermissionKeys = array_keys($Permissions);
    $PermissionList = array_combine($PermissionKeys, $PermissionKeys);
    $this->SetData('Permissions', $PermissionList);
    
    //$this->SetData('PermissionData', $Permissions, true);
    
    if($this->Form->IsPostBack() == FALSE) {
      if(property_exists($this, 'Rank')) {
        $this->Form->SetData($this->Rank);
      }
    }
    else {
      // Handle the photo upload
      $Upload = new Gdn_Upload();
      $TmpImage = $Upload->ValidateUpload('PhotoUpload', FALSE);

      if($TmpImage) {
        // Generate the target image name
        $TargetImage = $Upload->GenerateTargetName(PATH_UPLOADS);
        $ImageBaseName = pathinfo($TargetImage, PATHINFO_BASENAME);

        // Save the uploaded image
        $Parts = $Upload->SaveAs($TmpImage, 'ranks/' . $ImageBaseName);

        $this->Form->SetFormValue('Photo', $Parts['SaveName']);
      }

      if($this->Form->Save()) {
        if($Edit) {
          $this->InformMessage('Rank updated successfully!');
        }
        else {
          $this->InformMessage('Rank added successfully!');
        }
        Redirect('/yaga/rank/settings');
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
   * Remove the rank via model.
   *
   * @todo Consider adding a confirmation page when not using JS
   * @param int $RankID
   */
  public function Delete($RankID) {
    $this->Permission('Yaga.Ranks.Manage');
    $this->AddSideMenu('rank/settings');

    $this->RankModel->DeleteRank($RankID);

    redirect('rank/settings');
  }

  /**
   * Toggle the enabled state of a rank. Must be done via JS.
   *
   * @param int $RankID
   * @throws PermissionException
   */
  public function Toggle($RankID) {
    if(!$this->Request->IsPostBack()) {
      throw PermissionException('Javascript');
    }
    $this->Permission('Yaga.Ranks.Manage');
    $this->AddSideMenu('rank/settings');

    $Rank = $this->RankModel->GetRank($RankID);

    if($Rank->Enabled) {
      $Enable = FALSE;
      $ToggleText = T('Disabled');
      $ActiveClass = 'InActive';
    }
    else {
      $Enable = TRUE;
      $ToggleText = T('Enabled');
      $ActiveClass = 'Active';
    }

    $Slider = Wrap(Wrap(Anchor($ToggleText, 'yaga/rank/toggle/' . $Rank->RankID, 'Hijack SmallButton'), 'span', array('class' => "ActivateSlider ActivateSlider-{$ActiveClass}")), 'td');
    $this->RankModel->EnableRank($RankID, $Enable);
    $this->JsonTarget('#RankID_' . $RankID . ' td:nth-child(7)', $Slider, 'ReplaceWith');
    $this->Render('Blank', 'Utility', 'Dashboard');
  }

  /**
   * Remove the photo association of a rank. This does not remove the actual file
   *
   * @param int $RankID
   * @param string $TransientKey
   */
  public function DeletePhoto($RankID = FALSE, $TransientKey = '') {
      // Check permission
      $this->Permission('Garden.Ranks.Manage');

      $RedirectUrl = 'yaga/rank/edit/'.$RankID;

      if (Gdn::Session()->ValidateTransientKey($TransientKey)) {
         $this->RankModel->SetField($RankID, 'Photo', NULL);
         $this->InformMessage(T('Rank photo has been deleted.'));
      }

      if ($this->_DeliveryType == DELIVERY_TYPE_ALL) {
          Redirect($RedirectUrl);
      } else {
         $this->ControllerName = 'Home';
         $this->View = 'FileNotFound';
         $this->RedirectUrl = Url($RedirectUrl);
         $this->Render();
      }
   }

   /**
    * You can manually award ranks to users for special cases
    *
    * @param int $UserID
    */
   public function Award($UserID) {
    // Check permission
    $this->Permission('Garden.Ranks.Add');
    $this->AddSideMenu('rank/settings');

    // Only allow awarding if some ranks exist
    if(!$this->RankModel->GetRankCount()) {
      throw ForbiddenException('award ranks without any ranks defined');
    }

    $UserModel = Gdn::UserModel();
    $User = $UserModel->GetID($UserID);

    $this->SetData('Username', $User->Name);

    $Ranks = $this->RankModel->GetRanks();
    $Ranklist = array();
    foreach($Ranks as $Rank) {
      $Ranklist[$Rank->RankID] = $Rank->Name;
    }
    $this->SetData('Ranks', $Ranklist);

    if($this->Form->IsPostBack() == FALSE) {
      // Add the user id field
      $this->Form->AddHidden('UserID', $User->UserID);
    }
    else {
      $Validation = new Gdn_Validation();
      $Validation->ApplyRule('UserID', 'ValidateRequired');
      $Validation->ApplyRule('RankID', 'ValidateRequired');
      if($Validation->Validate($this->Request->Post())) {
        $FormValues = $this->Form->FormValues();
        if($this->RankModel->UserHasRank($FormValues['UserID'], $FormValues['RankID'])) {
          $this->Form->AddError($User->Name . T(' already has this rank!'), 'RankID');
          // Need to respecify the user id
          $this->Form->AddHidden('UserID', $User->UserID);
        }

        if($this->Form->ErrorCount() == 0) {
          $this->RankModel->AwardRank($FormValues['RankID'], $FormValues['UserID'], Gdn::Session()->UserID, $FormValues['Reason']);

          if($this->Request->Get('Target')) {
            $this->RedirectUrl = $this->Request->Get('Target');
          }
          elseif($this->DeliveryType() == DELIVERY_TYPE_ALL) {
            $this->RedirectUrl = Url(UserUrl($User));
          }
          else {
            $this->JsonTarget('', '', 'Refresh');
          }
        }
      }
      else {
        $this->Form->SetValidationResults($Validation->Results());
      }
    }

    $this->Render();
  }

}
