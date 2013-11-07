<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * Contains management code for creating badges.
 *
 * @since 1.0
 * @package Yaga
 */
class BadgesController extends DashboardController {

  /** @var array List of objects to prep. They will be available as $this->$Name. */
  public $Uses = array('Form', 'BadgeModel', 'Gdn_Filecache');

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
    $this->AddJsFile('admin.badges.js');
    $this->AddCssFile('badges.css');
    $this->Filecache->AddContainer(array(Gdn_Cache::CONTAINER_LOCATION=>'./cache/'));
  }

  public function Settings($Page = '') {
    $this->Permission('Yaga.Badges.Manage');
    $this->AddSideMenu('badges/settings');

    $this->Title('Manage Badges');

    // Get list of badges from the model and pass to the view
    $this->SetData('Badges', $this->BadgeModel->GetBadges());

    $this->Render();
  }
  
  public function GetRules() {
    //$Rules = $this->Filecache->Get('Yaga.Badges.Rules');
    //if($Rules == Gdn_Cache::CACHEOP_FAILURE) {
      foreach(glob(PATH_APPLICATIONS . DS . 'yaga' . DS . 'rules' . DS . '*.php') as $filename) {
        include_once $filename;
      }
      
      $TempRules = array();
      foreach(get_declared_classes() as $className) {
        if(in_array('YagaRule', class_implements($className))) {
          $Rule = new $className();
          $TempRules[$className] = $Rule->FriendlyName();
        }
      }
      if(empty($TempRules)) {
        $Rules = serialize(FALSE);
      }
      else{
        $Rules = serialize($TempRules);
      }
      //$this->Filecache->Store('Yaga.Badges.Rules', $Rules, array(Gdn_Cache::FEATURE_EXPIRY => C('Yaga.Rules.CacheExpire', 86400)));
    //}
    
    return unserialize($Rules);
  }

  public function test() {
    $Session = Gdn::Session();
    if(!$Session->IsValid())
      return;

    $UserID = $Session->UserID;

    $BadgeModel = new BadgeModel();
    $Badges = $BadgeModel->GetEnabledBadges();
    $UserBadges = $BadgeModel->GetUserBadgeAwards($UserID);
    
    $Rules = array();
    foreach($Badges as $Badge) {
      if(!InSubArray($Badge->BadgeID, $UserBadges)) {
        // The user doesn't have this badge
        
        $Class = $Badge->RuleClass;
        // Create a rule object if needed
        if(!in_array($Class, $Rules)) {
          $Rule = new $Class();
          $Rules[$Class] = $Rule;
        }
        
        // execute the Calculated
        $Rule = $Rules[$Class];
        if($Rule->CalculateAward($UserID, $Badge->RuleCriteria)) {
          $BadgeModel->AwardBadge($Badge->BadgeID, $UserID);
          
          // TODO: Record to Activity Table
          $this->InformMessage('Badge "' . $Badge->Name . '" was awarded to you!');
          // Notify user of badge award
        }
      }
    }
    $this->Render('add');
  }
  
  public function Edit($BadgeID = NULL) {
    $this->Permission('Yaga.Badges.Manage');
    $this->AddSideMenu('badges/settings');
    $this->Form->SetModel($this->BadgeModel);
    
    // Only allow editing if some rules exist
    

    $Edit = FALSE;
    if($BadgeID) {
      $this->Badge = $this->BadgeModel->GetBadge($BadgeID);
      $this->Form->AddHidden('BadgeID', $BadgeID);
      $Edit = TRUE;
    }

    if($this->Form->IsPostBack() == FALSE) {
      if(property_exists($this, 'Badge')) {
        // Manually merge the criteria into the badge object
        $Criteria = unserialize($this->Badge->RuleCriteria);
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
        $TargetImage = $Upload->GenerateTargetName(PATH_UPLOADS);
        $ImageBaseName = pathinfo($TargetImage, PATHINFO_BASENAME);

        // Save the uploaded image
        $Parts = $Upload->SaveAs($TmpImage, $ImageBaseName);

        $this->Form->SetFormValue('Photo', $Parts['SaveName']);
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
      $SerializedCriteria = serialize($Criteria);
      $this->Form->SetFormValue('RuleCriteria', $SerializedCriteria);
      if($this->Form->Save()) {
        if($Edit) {
          $this->InformMessage('Badge updated successfully!');
        }
        else {
          $this->InformMessage('Badge added successfully!');
        }
        Redirect('/yaga/badges/settings');
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
      throw PermissionException('Javascript');
    }
    $this->Permission('Yaga.Reactions.Manage');
    $this->AddSideMenu('badges/settings');

    $Badge = $this->BadgeModel->GetBadge($BadgeID);
    
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
    
    $Slider = Wrap(Wrap(Anchor($ToggleText, 'yaga/badges/toggle/' . $Badge->BadgeID, 'Hijack SmallButton'), 'span', array('class' => "ActivateSlider ActivateSlider-{$ActiveClass}")), 'td');
    $this->BadgeModel->EnableBadge($BadgeID, $Enable);
    $this->JsonTarget('#BadgeID_' . $BadgeID . ' td:nth-child(7)', $Slider, 'ReplaceWith');
    $this->Render('Blank', 'Utility', 'Dashboard');
  }

  public function DeletePhoto($BadgeID = FALSE, $TransientKey = '') {
      // Check permission
      $this->Permission('Garden.Badges.Manage');
      
      $RedirectUrl = 'yaga/badges/edit/'.$BadgeID;
      
      if (Gdn::Session()->ValidateTransientKey($TransientKey)) {
         // Do removal, set message, redirect
         $BadgeModel = new BadgeModel();
         $BadgeModel->SetField($BadgeID, 'Photo', NULL); 
         $this->InformMessage(T('Badge photo has been deleted.'));
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
}
