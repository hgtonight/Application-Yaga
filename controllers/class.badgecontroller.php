<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * Contains management code for creating badges.
 *
 * @since 1.0
 * @package Yaga
 */
class BadgeController extends DashboardController {

  /**
   * @var array These objects will be created on instantiation and available via
   * $this->ObjectName
   */
  public $Uses = array('Form', 'BadgeModel', 'BadgeAwardModel');

  /**
   * Make this look like a dashboard page and add the resources
   *
   * @since 1.0
   * @access public
   */
  public function Initialize() {
    parent::Initialize();
    $this->Application = 'Yaga';
    Gdn_Theme::Section('Dashboard');
    if($this->Menu) {
      $this->Menu->HighlightRoute('/badge');
    }
    $this->AddJsFile('jquery-ui-1.10.0.custom.min.js');
    $this->AddJsFile('admin.badges.js');
    $this->AddCssFile('badges.css');
    $this->removeCssFile('magnific-popup.css');
  }

  /**
   * Manage the current badges and add new ones
   *
   * @param int $Page
   */
  public function Settings($Page = '') {
    $this->Permission('Yaga.Badges.Manage');
    $this->AddSideMenu('badge/settings');

    $this->Title(T('Yaga.Badges.Manage'));

    // Get list of badges from the model and pass to the view
    list($Offset, $Limit) = OffsetLimit($Page, PagerModule::$DefaultPageSize);
    $this->SetData('Badges', $this->BadgeModel->GetLimit($Limit, $Offset));
    $this->SetData('Rules', RulesController::GetRules());

    $this->Render();
  }

  /**
   * Edit an existing badge or add a new one
   *
   * @param int $BadgeID
   * @throws ForbiddenException if no proper rules are found
   */
  public function Edit($BadgeID = NULL) {
    $this->Permission('Yaga.Badges.Manage');
    $this->AddSideMenu('badge/settings');
    $this->Form->SetModel($this->BadgeModel);

    // Only allow editing if some rules exist
    if(!RulesController::GetRules()) {
      throw new Gdn_UserException(T('Yaga.Error.NoRules'));
    }

    $Edit = FALSE;
    if($BadgeID) {
      $this->Title(T('Yaga.Badge.Edit'));
      $this->Badge = $this->BadgeModel->GetByID($BadgeID);
      $this->Form->AddHidden('BadgeID', $BadgeID);
      $Edit = TRUE;
    }
    else {
      $this->Title(T('Yaga.Badge.Add'));
    }

    if($this->Form->IsPostBack() == FALSE) {
      if(property_exists($this, 'Badge')) {
        // Manually merge the criteria into the badge object
        $Criteria = (array) unserialize($this->Badge->RuleCriteria);
        $BadgeArray = (array) $this->Badge;

        $Data = array_merge($BadgeArray, $Criteria);
        $this->Form->SetData($Data);
      }
    }
    else {
      // Handle the photo upload
      $Upload = new Gdn_Upload();
      $TmpImage = $Upload->ValidateUpload('PhotoUpload', FALSE);

      if($TmpImage) {
        // Generate the target image name
        $TargetImage = $Upload->GenerateTargetName(PATH_UPLOADS, FALSE);
        $ImageBaseName = pathinfo($TargetImage, PATHINFO_BASENAME);

        // Save the uploaded image
        $Parts = $Upload->SaveAs($TmpImage, 'yaga' . DS . $ImageBaseName);
        $AssetRoot = Gdn::Request()->UrlDomain(TRUE).Gdn::Request()->AssetRoot();
        $RelativeUrl = StringBeginsWith($Parts['Url'], $AssetRoot, TRUE, TRUE);

        $this->Form->SetFormValue('Photo', $RelativeUrl);
      }
      else if(!$Edit) {
        // Use default photo from config if this is a new badge
        $this->Form->SetFormValue('Photo', C('Yaga.Badges.DefaultPhoto'));
      }

      // Find the rule criteria
      $FormValues = $this->Form->FormValues();     
      $Criteria = array();
      foreach($FormValues as $Key => $Value) {
        if(substr($Key, 0, 7) == '_Rules/') {
          $RealKey = substr($Key, 7);
          $Criteria[$RealKey] = $Value;
        }
      }

      // Validate the criteria
      $RuleClass = new $FormValues['RuleClass'];
      $Rule = new $RuleClass();

      $Rule->Validate($Criteria, $this->Form);

      $SerializedCriteria = serialize($Criteria);
      $this->Form->SetFormValue('RuleCriteria', $SerializedCriteria);
      if($this->Form->Save()) {
        if($Edit) {
          $this->InformMessage(T('Yaga.Badge.Updated'));
        }
        else {
          $this->InformMessage(T('Yaga.Badge.Added'));
        }
        Redirect('/badge/settings');
      }
    }

    $this->Render('edit');
  }

  /**
   * Convenience function for nice URLs
   */
  public function Add() {
    $this->Edit();
  }

  /**
   * Remove the badge via model.
   *
   * @param int $BadgeID
   * @throws NotFoundException
   */
  public function Delete($BadgeID) {
    $Badge = $this->BadgeModel->GetByID($BadgeID);

    if(!$Badge) {
      throw NotFoundException(T('Yaga.Badge'));
    }

    $this->Permission('Yaga.Badges.Manage');

    if($this->Form->IsPostBack()) {
      if(!$this->BadgeModel->Delete($BadgeID)) {
        $this->Form->AddError(sprintf(T('Yaga.Error.DeleteFailed'), T('Yaga.Badge')));
      }

      if($this->Form->ErrorCount() == 0) {
        if($this->_DeliveryType === DELIVERY_TYPE_ALL) {
          Redirect('badge/settings');
        }

        $this->JsonTarget('#BadgeID_' . $BadgeID, NULL, 'SlideUp');
      }
    }

    $this->AddSideMenu('badge/settings');
    $this->SetData('Title', T('Yaga.Badge.Delete'));
    $this->Render();
  }

  /**
   * Toggle the enabled state of a badge. Must be done via JS.
   *
   * @param int $BadgeID
   * @throws PermissionException
   */
  public function Toggle($BadgeID) {
    if(!$this->Request->IsPostBack()) {
      throw new Gdn_UserException(T('Yaga.Error.NeedJS'));
    }
    $this->Permission('Yaga.Badges.Manage');
    $this->AddSideMenu('badge/settings');

    $Badge = $this->BadgeModel->GetByID($BadgeID);

    if($Badge->Enabled) {
      $Enable = FALSE;
      $ToggleText = T('Disabled');
      $ActiveClass = 'InActive';
    }
    else {
      $Enable = TRUE;
      $ToggleText = T('Enabled');
      $ActiveClass = 'Active';
    }

    $Slider = Wrap(Wrap(Anchor($ToggleText, 'badge/toggle/' . $Badge->BadgeID, 'Hijack SmallButton'), 'span', array('class' => "ActivateSlider ActivateSlider-{$ActiveClass}")), 'td');
    $this->BadgeModel->Enable($BadgeID, $Enable);
    $this->JsonTarget('#BadgeID_' . $BadgeID . ' td:nth-child(6)', $Slider, 'ReplaceWith');
    $this->Render('Blank', 'Utility', 'Dashboard');
  }

  /**
   * Remove the photo association of a badge. This does not remove the actual file
   *
   * @param int $BadgeID
   * @param string $TransientKey
   */
  public function DeletePhoto($BadgeID = FALSE, $TransientKey = '') {
      // Check permission
      $this->Permission('Yaga.Badges.Manage');

      $RedirectUrl = 'badge/edit/'.$BadgeID;

      if (Gdn::Session()->ValidateTransientKey($TransientKey)) {
         $this->BadgeModel->SetField($BadgeID, 'Photo', C('Yaga.Badges.DefaultPhoto'));
         $this->InformMessage(T('Yaga.Badge.PhotoDeleted'));
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
    * You can manually award badges to users for special cases
    *
    * @param int $UserID
    * @throws Gdn_UserException
    */
   public function Award($UserID) {
    // Check permission
    $this->Permission('Yaga.Badges.Add');
    $this->AddSideMenu('badge/settings');

    // Only allow awarding if some badges exist
    if(!$this->BadgeModel->GetCount()) {
      throw new Gdn_UserException(T('Yaga.Error.NoBadges'));
    }

    $UserModel = Gdn::UserModel();
    $User = $UserModel->GetID($UserID);

    $this->SetData('Username', $User->Name);

    $Badges = $this->BadgeModel->Get();
    $Badgelist = array();
    foreach($Badges as $Badge) {
      $Badgelist[$Badge->BadgeID] = $Badge->Name;
    }
    $this->SetData('Badges', $Badgelist);

    if($this->Form->IsPostBack() == FALSE) {
      // Add the user id field
      $this->Form->AddHidden('UserID', $User->UserID);
    }
    else {
      $Validation = new Gdn_Validation();
      $Validation->ApplyRule('UserID', 'ValidateRequired');
      $Validation->ApplyRule('BadgeID', 'ValidateRequired');
      if($Validation->Validate($this->Request->Post())) {
        $FormValues = $this->Form->FormValues();
        if($this->BadgeAwardModel->Exists($FormValues['UserID'], $FormValues['BadgeID'])) {
          $this->Form->AddError(sprintf(T('Yaga.Badge.AlreadyAwarded'), $User->Name), 'BadgeID');
          // Need to respecify the user id
          $this->Form->AddHidden('UserID', $User->UserID);
        }

        if($this->Form->ErrorCount() == 0) {
          $this->BadgeAwardModel->Award($FormValues['BadgeID'], $FormValues['UserID'], Gdn::Session()->UserID, $FormValues['Reason']);

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

  /**
   * This takes in a sort array and updates the badge sort order.
   * 
   * Renders the Save tree and/or the Result of the sort update.
   * 
   * @since 1.1
   */
  public function Sort() {
      // Check permission
      $this->Permission('Yaga.Badges.Manage');

      $Request = Gdn::Request();
      if($Request->IsPostBack()) {
         $SortArray = $Request->GetValue('SortArray', NULL);
         $Saves = $this->BadgeModel->SaveSort($SortArray);
         $this->SetData('Result', TRUE);
         $this->SetData('Saves', $Saves);
      }
      else {
        $this->SetData('Result', FALSE);
      }

      $this->RenderData();
   }
}
