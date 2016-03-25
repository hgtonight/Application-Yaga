<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013-2014 Zachary Doll */

/**
 * A collection of hooks that are enabled when Yaga is.
 * 
 * @package Yaga
 * @since 1.0
 */
class YagaHooks implements Gdn_IPlugin {

  /**
   * Redirect any old links to proper settings page permanently
   * @param SettingsController $Sender
   */
  public function SettingsController_Yaga_Create($Sender) {
    Redirect('yaga/settings', 301);
  }

  /**
   * Add Simple stats page to dashboard index
   * @param SettingsController $Sender
   */
  public function SettingsController_AfterRenderAsset_Handler($Sender) {
    $EventArguments = $Sender->EventArguments;
    if($EventArguments['AssetName'] == 'Content' && $Sender->OriginalRequestMethod == 'index') {
      //echo 'Sweet sweet stats!';
      $BadgeAwardModel = Yaga::BadgeAwardModel();
      $ReactionModel = Yaga::ReactionModel();

      $BadgeCount = $BadgeAwardModel->GetCount();
      $ReactionCount = $ReactionModel->GetCount();
      echo Wrap('Yaga Statistics', 'h1');
      echo Wrap(
              Wrap(
                      Wrap(
                              'Badges' . Wrap($BadgeCount, 'strong'),
                              'div'), 'li', array('class' => 'BadgeCount')) .
              Wrap(
                      Wrap(
                              'Reactions' . Wrap($ReactionCount, 'strong'),
                              'div'), 'li', array('class' => 'ReactionCount')),
            'ul',
            array('class' => 'StatsOverview'));
    }
  }

  /**
   * Add the settings page links
   *
   * @param Object $Sender
   */
  public function Base_GetAppSettingsMenuItems_Handler($Sender) {
    $Menu = $Sender->EventArguments['SideMenu'];
    $Section = 'Gamification';
    $Attrs = array('class' => $Section);
    $Menu->AddItem($Section, $Section, FALSE, $Attrs);
    $Menu->AddLink($Section, T('Settings'), 'yaga/settings', 'Garden.Settings.Manage');
    if(C('Yaga.Reactions.Enabled')) {
      $Menu->AddLink($Section, T('Yaga.Reactions'), 'action/settings', 'Yaga.Reactions.Manage');
    }
    if(C('Yaga.Badges.Enabled')) {
      $Menu->AddLink($Section, T('Yaga.Badges'), 'badge/settings', 'Yaga.Badges.Manage');
    }
    if(C('Yaga.Ranks.Enabled')) {
      $Menu->AddLink($Section, T('Yaga.Ranks'), 'rank/settings', 'Yaga.Ranks.Manage');
    }
  }

  /**
   * Add a Best Content item to the discussion filters module
   * 
   * @param mixed $Sender
   * @return boolean
   */
  public function Base_AfterDiscussionFilters_Handler($Sender) {
    if(!C('Yaga.Reactions.Enabled')) {
      return FALSE;
    }

    echo Wrap(Anchor(Sprite('SpBestOf', 'SpMod Sprite') . ' ' . T('Yaga.BestContent'), '/best'), 'li', array('class' => $Sender->ControllerName == 'bestcontroller' ? 'Best Active' : 'Best'));
  }

  /**
   * Display the reaction counts on the profile page
   * @param ProfileController $Sender
   */
  public function ProfileController_AfterUserInfo_Handler($Sender) {
    if(!C('Yaga.Reactions.Enabled')) {
      return;
    }
    $User = $Sender->User;
    $Method = $Sender->RequestMethod;
    if($Method == 'reactions') {
      $ActionID = $Sender->RequestArgs[2];
    }
    else {
      $ActionID = -1;
    }
    echo '<div class="Yaga ReactionsWrap">';
    echo Wrap(T('Yaga.Reactions', 'Reactions'), 'h2', array('class' => 'H'));

    // insert the reaction totals in the profile
    $ReactionModel = Yaga::ReactionModel();
    $Actions = Yaga::ActionModel()->Get();
    $String = '';
    foreach($Actions as $Action) {
      $Selected = ($ActionID == $Action->ActionID) ? ' Selected' : '';
      $Count = $ReactionModel->GetUserCount($User->UserID, $Action->ActionID);
      $TempString = Wrap(Wrap(Gdn_Format::BigNumber($Count), 'span', array('title' => $Count)), 'span', array('class' => 'Yaga_ReactionCount CountTotal'));
      $TempString .= Wrap($Action->Name, 'span', array('class' => 'Yaga_ReactionName CountLabel'));

      $String .= Wrap(Wrap(Anchor($TempString, '/profile/reactions/' . $User->UserID . '/' . Gdn_Format::Url($User->Name) . '/' . $Action->ActionID, array('class' => 'Yaga_Reaction TextColor', 'title' => $Action->Description)), 'span', array('class' => 'CountItem' . $Selected)), 'span', array('class' => 'CountItemWrap'));
    }

    echo Wrap($String, 'div', array('class' => 'DataCounts'));
    echo '</div>';
  }

  /**
   * Add the badge count into the user info module
   *
   * @param UserInfoModule $Sender
   */
  public function UserInfoModule_OnBasicInfo_Handler($Sender) {
    if(C('Yaga.Badges.Enabled')) {
      echo '<dd class="Badges">' . $Sender->User->CountBadges . '</dd>';
      echo '<dt class="Badges">' . T('Yaga.Badges', 'Badges') . '</dt> ';
    }
  }

  /**
   * This method shows the latest discussions/comments a user has posted that
   * received the specified action
   *
   * @param ProfileController $Sender
   * @param int $UserReference
   * @param string $Username
   * @param int $ActionID
   * @param int $Page
   */
  public function ProfileController_Reactions_Create($Sender, $UserReference = '', $Username = '', $ActionID = '', $Page = 0) {
    if(!C('Yaga.Reactions.Enabled')) {
      throw notFoundException();
    }

    list($Offset, $Limit) = OffsetLimit($Page, C('Yaga.ReactedContent.PerPage', 5));
    if(!is_numeric($Offset) || $Offset < 0) {
      $Offset = 0;
    }

    $Sender->EditMode(FALSE);

    // Tell the ProfileController what tab to load
    $Sender->GetUserInfo($UserReference, $Username);
    $Sender->SetTabView(T('Yaga.Reactions'), 'reactions', 'profile', 'Yaga');

    $Sender->AddJsFile('jquery.expander.js');
    $Sender->AddJsFile('reactions.js', 'yaga');
    $Sender->AddDefinition('ExpandText', T('(more)'));
    $Sender->AddDefinition('CollapseText', T('(less)'));

    $Model = Yaga::ActedModel();
    $Data = $Model->Get($Sender->User->UserID, $ActionID, $Limit, $Offset);

    $Sender->SetData('Content', $Data->Content);

    // Set the HandlerType back to normal on the profilecontroller so that it fetches it's own views
    $Sender->HandlerType = HANDLER_TYPE_NORMAL;

    // Do not show discussion options
    $Sender->ShowOptions = FALSE;

    if($Sender->Head) {
      $Sender->Head->AddTag('meta', array('name' => 'robots', 'content' => 'noindex,noarchive'));
    }

    // Build a pager
    $BaseUrl = 'profile/reactions/' . $Sender->User->UserID . '/' . Gdn_Format::Url($Sender->User->Name) . '/' . $ActionID; 
    $PagerFactory = new Gdn_PagerFactory();
    $Sender->Pager = $PagerFactory->GetPager('Pager', $Sender);
    $Sender->Pager->ClientID = 'Pager';
    $Sender->Pager->Configure(
            $Offset, $Limit, $Data->TotalRecords, $BaseUrl . '/%1$s/'
    );

    // Add the specific action to the breadcrumbs
    $action = Yaga::ActionModel()->GetID($ActionID);
    if($action) {
      $Sender->_SetBreadcrumbs($action->Name, $BaseUrl);
    }
    
    // Render the ProfileController
    $Sender->Render();
  }

  /**
   * This method shows the highest scoring discussions/comments a user has ever posted
   *
   * @param ProfileController $Sender
   * @param int $UserReference
   * @param string $Username
   * @param int $Page
   */
  public function ProfileController_Best_Create($Sender, $UserReference = '', $Username = '', $Page = 0) {
    if(!C('Yaga.Reactions.Enabled')) {
      return;
    }

    list($Offset, $Limit) = OffsetLimit($Page, C('Yaga.BestContent.PerPage', 10));
    if(!is_numeric($Offset) || $Offset < 0) {
      $Offset = 0;
    }

    $Sender->EditMode(FALSE);

    // Tell the ProfileController what tab to load
    $Sender->GetUserInfo($UserReference, $Username);
    $Sender->_SetBreadcrumbs(T('Yaga.BestContent'), UserUrl($Sender->User, '', 'best'));
    $Sender->SetTabView(T('Yaga.BestContent'), 'best', 'profile', 'Yaga');

    $Sender->AddJsFile('jquery.expander.js');
    $Sender->AddJsFile('reactions.js', 'yaga');
    $Sender->AddDefinition('ExpandText', T('(more)'));
    $Sender->AddDefinition('CollapseText', T('(less)'));

    $Model = Yaga::ActedModel();
    $Data = $Model->GetBest($Sender->User->UserID, $Limit, $Offset);

    $Sender->SetData('Content', $Data->Content);

    // Set the HandlerType back to normal on the profilecontroller so that it fetches it's own views
    $Sender->HandlerType = HANDLER_TYPE_NORMAL;

    // Do not show discussion options
    $Sender->ShowOptions = FALSE;

    if($Sender->Head) {
      $Sender->Head->AddTag('meta', array('name' => 'robots', 'content' => 'noindex,noarchive'));
    }

    // Build a pager
    $PagerFactory = new Gdn_PagerFactory();
    $Sender->Pager = $PagerFactory->GetPager('Pager', $Sender);
    $Sender->Pager->ClientID = 'Pager';
    $Sender->Pager->Configure(
            $Offset, $Limit, FALSE, 'profile/best/' . $Sender->User->UserID . '/' . Gdn_Format::Url($Sender->User->Name) . '/%1$s/'
    );

    // Render the ProfileController
    $Sender->Render();
  }

  /**
   * Add a best content tab on a user's profile
   * @param ProfileController $Sender
   */
  public function ProfileController_AddProfileTabs_Handler($Sender) {
    if(is_object($Sender->User) && $Sender->User->UserID > 0) {
      $Sender->AddProfileTab(Sprite('SpBestOf', 'SpMod Sprite') . ' ' . T('Yaga.BestContent'), 'profile/best/' . $Sender->User->UserID . '/' . urlencode($Sender->User->Name), 'Best');
    }
  }

  /**
   * Check for rank progress when the user model gets updated
   *
   * @param UserModel $Sender
   */
  public function UserModel_AfterSetField_Handler($Sender) {
    // Don't check for promotions if we aren't using ranks
    if(!C('Yaga.Ranks.Enabled')) {
      return;
    }
    
    $Fields = $Sender->EventArguments['Fields'];
    $FieldHooks = array('Points', 'CountDiscussions', 'CountComments');
    
    foreach($FieldHooks as $FieldHook) {
      if(array_key_exists($FieldHook, $Fields)) {
        $UserID = $Sender->EventArguments['UserID'];
        $this->RankProgression($UserID);
        break; // Only need to fire once per event
      }
    }
  }
  
  /**
   * Update a user's rank id if they qualify
   * 
   * @param int $UserID
   */
  protected function RankProgression($UserID) {
    $UserModel = Gdn::UserModel();
    $User = $UserModel->GetID($UserID);

    // Don't try to promote if they are frozen
    if(!$User->RankProgression) {
      return;
    }

    $RankModel = Yaga::RankModel();
    $Rank = $RankModel->GetHighestQualifyingRank($User);

    if($Rank && $Rank->RankID != $User->RankID) {
      // Only promote automatically
      $OldRank = $RankModel->GetByID($User->RankID);
      if($OldRank->Sort < $Rank->Sort) {
        $RankModel->Set($Rank->RankID, $UserID, TRUE);
      }
    }
  }

  /**
   * Add the badge and rank notification options
   *
   * @param ProfileController $Sender
   */
  public function ProfileController_AfterPreferencesDefined_Handler($Sender) {
    if(C('Yaga.Badges.Enabled')) {
      $Sender->Preferences['Notifications']['Email.BadgeAward'] = T('Yaga.Badges.Notify');
      $Sender->Preferences['Notifications']['Popup.BadgeAward'] = T('Yaga.Badges.Notify');
    }

    if(C('Yaga.Ranks.Enabled')) {
      $Sender->Preferences['Notifications']['Email.RankPromotion'] = T('Yaga.Ranks.Notify');
      $Sender->Preferences['Notifications']['Popup.RankPromotion'] = T('Yaga.Ranks.Notify');
    }
  }

  /**
   * Add the Award Badge and Promote options to the profile controller
   *
   * @param ProfileController $Sender
   */
  public function ProfileController_BeforeProfileOptions_Handler($Sender) {
    if(Gdn::Session()->IsValid()) {
      if(C('Yaga.Badges.Enabled') && CheckPermission('Yaga.Badges.Add')) {
        $Sender->EventArguments['ProfileOptions'][] = array(
            'Text' => Sprite('SpBadge', 'SpMod Sprite') . ' ' . T('Yaga.Badge.Award'),
            'Url' => '/badge/award/' . $Sender->User->UserID,
            'CssClass' => 'Popup'
        );
      }

      if(C('Yaga.Ranks.Enabled') && CheckPermission('Yaga.Ranks.Add')) {
        $Sender->EventArguments['ProfileOptions'][] = array(
            'Text' => Sprite('SpMod') . ' ' . T('Yaga.Rank.Promote'),
            'Url' => '/rank/promote/' . $Sender->User->UserID,
            'CssClass' => 'Popup'
        );
      }
    }
  }

  /**
   * Display a record of reactions after the first post
   *
   * @param DiscussionController $Sender
   */
  public function DiscussionController_AfterDiscussionBody_Handler($Sender) {
    if(!Gdn::Session()->CheckPermission('Yaga.Reactions.View') || !C('Yaga.Reactions.Enabled')) {
      return;
    }
    $Type = 'discussion';
    $ID = $Sender->DiscussionID;
    echo RenderReactionRecord($ID, $Type);
  }

  /**
   * Display a record of reactions after comments
   * @param DiscussionController $Sender
   */
  public function DiscussionController_AfterCommentBody_Handler($Sender) {
    if(!Gdn::Session()->CheckPermission('Yaga.Reactions.View') || !C('Yaga.Reactions.Enabled')) {
      return;
    }
    $Type = 'comment';
    $ID = $Sender->EventArguments['Comment']->CommentID;
    echo RenderReactionRecord($ID, $Type);
  }

  /**
   * Add action list to discussion items
   * @param DiscussionController $Sender
   */
  public function DiscussionController_AfterReactions_Handler($Sender) {
    if(C('Yaga.Reactions.Enabled') == FALSE) {
      return;
    }

    // check to see if allowed to add reactions
    if(!Gdn::Session()->CheckPermission('Yaga.Reactions.Add')) {
      return;
    }

    // Users shouldn't be able to react to their own content
    $Type = $Sender->EventArguments['RecordType'];
    $ID = $Sender->EventArguments['RecordID'];

    if(array_key_exists('Author', $Sender->EventArguments)) {
      $Author = $Sender->EventArguments['Author'];
      $AuthorID = $Author->UserID;
    }
    else {
      $Discussion = $Sender->EventArguments['Discussion'];
      $AuthorID = $Discussion->InsertUserID;
    }

    // Users shouldn't be able to react to their own content
    if(Gdn::Session()->UserID != $AuthorID) {
      echo RenderReactionList($ID, $Type);
    }
  }

  /**
   * Add the action list to any activity items that can be commented on
   *
   * @param ActivityController $Sender
   */
  public function ActivityController_AfterActivityBody_Handler($Sender) {
    if(!C('Yaga.Reactions.Enabled')) {
      return;
    }
    $Activity = $Sender->EventArguments['Activity'];
    $CurrentUserID = Gdn::Session()->UserID;
    $Type = 'activity';
    $ID = $Activity->ActivityID;

    // Only allow reactions on activities that allow comments
    if(!property_exists($Activity, 'AllowComments') || $Activity->AllowComments == 0) {
      return;
    }

    // check to see if allowed to add reactions
    if(!Gdn::Session()->CheckPermission('Yaga.Reactions.Add')) {
      return;
    }
    
    if($CurrentUserID == $Activity->RegardingUserID) {
      // The current user made this activity item happen
    }
    else {
      echo Wrap(RenderReactionList($ID, $Type), 'div', array('class' => 'Reactions'));
    }
  }

  /**
   * Apply any applicable rank perks when the session first starts.
   * @param UserModel $Sender
   */
  public function UserModel_AfterGetSession_Handler($Sender) {
    if(!C('Yaga.Ranks.Enabled')) {
      return;
    }

    $User = &$Sender->EventArguments['User'];
    $RankID = $User->RankID;
    if(is_null($RankID)) {
      return;
    }

    $RankModel = Yaga::RankModel();
    $Perks = $RankModel->GetPerks($RankID);
    
    // Apply all the perks
    foreach($Perks as $Perk => $PerkValue) {
      $PerkType = substr($Perk, 0, 4);
      $PerkKey = substr($Perk, 4);
      
      if($PerkType === 'Conf') {
        $this->ApplyCustomConfigs($PerkKey, $PerkValue);
      }
      else if($PerkType === 'Perm' && $PerkValue === 'grant') {
        $this->GrantPermission($User, $PerkKey);
      }
      else if($PerkType === 'Perm' && $PerkValue === 'revoke') {
        $this->RevokePermission($User, $PerkKey);
      }
      else {
        // Do nothing
        // TODO: look into firing a custom event
      }
    }
  }

  /**
   * Gives the specified permission to a user, regardless of current role.
   * @param type $User
   * @param string $Permission
   */
  private function GrantPermission($User, $Permission = '') {
    if($Permission === '') {
      return;
    }
    
    if(!is_array($User->Permissions)) {
      $TempPerms = unserialize($User->Permissions);
      if(!in_array($Permission, $TempPerms)) {
        $TempPerms[] = $Permission;
        $User->Permissions = serialize($TempPerms);
      }
    }
    else {
      $TempPerms =& $User->Permissions;
      $TempPerms[] = $Permission;
    }
  }

  /**
   * Removes the specified permission from a user, regardless of current role.
   *
   * Cannot be used to override $User->Admin = 1 permissions
   *
   * @param type $User
   * @param string $Permission
   */
  private function RevokePermission($User, $Permission = '') {
    if($Permission === '') {
      return;
    }
    
    if(!is_array($User->Permissions)) {
      $TempPerms = unserialize($User->Permissions);
      $Key = array_search($Permission, $TempPerms);
      if($Key) {
        unset($TempPerms[$Key]);
        $User->Permissions = serialize($TempPerms);
      }
    }
    else {
      $TempPerms =& $User->Permissions;
      $Key = array_search($Permission, $TempPerms);
      if($Key) {
        unset($TempPerms[$Key]);
      }
    }
  }

  /**
   * Apply custom configuration from rank perks in memory only.
   * @param string $Name
   * @param mixed $Value
   */
  private function ApplyCustomConfigs($Name = NULL, $Value = NULL) {
    SaveToConfig($Name, $Value, array('Save' => FALSE));
  }

  /**
   * Insert JS and CSS files into the appropiate controllers
   * 
   * @param ProfileController $Sender
   */
  public function ProfileController_Render_Before($Sender) {
    $this->AddResources($Sender);

    if(C('Yaga.Badges.Enabled')) {
      $Sender->AddModule('BadgesModule');
    }
  }

  /**
   * Insert JS and CSS files into the appropiate controllers and fill the reaction cache
   * 
   * @param DiscussionController $Sender
   */
  public function DiscussionController_Render_Before($Sender) {
    $this->AddResources($Sender);
    if (C('Yaga.Reactions.Enabled')) {
      if ($Sender->Data('Discussion')) {
        Yaga::ReactionModel()->Prefetch('discussion', $Sender->Data['Discussion']->DiscussionID);
      }
      if (isset($Sender->Data['Comments'])) {
        $CommentIDs = ConsolidateArrayValuesByKey($Sender->Data['Comments']->ResultArray(), 'CommentID');
        // set the DataSet type back to "object"
        $Sender->Data['Comments']->DataSetType(DATASET_TYPE_OBJECT);
        Yaga::ReactionModel()->Prefetch('comment', $CommentIDs);
      }
    }
  }
  
  /**
   * Insert JS and CSS files into the appropiate controllers
   * 
   * @since 1.1
   * @param DiscussionsController $Sender
   */
  public function DiscussionsController_Render_Before($Sender) {
    $this->AddResources($Sender);
  }

  /**
   * Insert JS and CSS files into the appropiate controllers
   * 
   * @param CommentController $Sender
   */
  public function CommentController_Render_Before($Sender) {
    $this->AddResources($Sender);
  }

  /**
   * Insert JS and CSS files into the appropiate controllers
   * 
   * @param ActivityController $Sender
   */
  public function ActivityController_Render_Before($Sender) {
    $this->AddResources($Sender);

    if(C('Yaga.LeaderBoard.Enabled', FALSE)) {
      // add leaderboard modules to the activity page
      $Module = new LeaderBoardModule();
      $Module->SlotType = 'w';
      $Sender->AddModule($Module);
      $Module = new LeaderBoardModule();
      $Sender->AddModule($Module);
    }
  }

  /**
   * Check for Badge Awards
   * 
   * @param Gdn_Dispatcher $Sender
   */
  public function Gdn_Dispatcher_AppStartup_Handler($Sender) {
    Yaga::ExecuteBadgeHooks($Sender, __FUNCTION__);
  }

  /**
   * Check for Badge Awards
   * 
   * @param mixed $Sender
   */
  public function Base_AfterGetSession_Handler($Sender) {
    Yaga::ExecuteBadgeHooks($Sender, __FUNCTION__);
  }

  /**
   * Check for Badge Awards
   * 
   * @param CommentModel $Sender
   */
  public function CommentModel_AfterSaveComment_Handler($Sender) {
    Yaga::ExecuteBadgeHooks($Sender, __FUNCTION__);
  }

  /**
   * Check for Badge Awards
   * 
   * @param DiscussionModel $Sender
   */
  public function DiscussionModel_AfterSaveDiscussion_Handler($Sender) {
    Yaga::ExecuteBadgeHooks($Sender, __FUNCTION__);
  }

  /**
   * Check for Badge Awards
   * 
   * @param ActivityModel $Sender
   */
  public function ActivityModel_BeforeSaveComment_Handler($Sender) {
    Yaga::ExecuteBadgeHooks($Sender, __FUNCTION__);
  }

  /**
   * Check for Badge Awards
   * 
   * @param CommentModel $Sender
   */
  public function CommentModel_BeforeNotification_Handler($Sender) {
    Yaga::ExecuteBadgeHooks($Sender, __FUNCTION__);
  }

  /**
   * Check for Badge Awards
   * 
   * @param DiscussionModel $Sender
   */
  public function DiscussionModel_BeforeNotification_Handler($Sender) {
    Yaga::ExecuteBadgeHooks($Sender, __FUNCTION__);
  }

  /**
   * Check for Badge Awards
   * 
   * @param mixed $Sender
   */
  public function Base_AfterSignIn_Handler($Sender) {
    Yaga::ExecuteBadgeHooks($Sender, __FUNCTION__);
  }

  /**
   * Check for Badge Awards
   * 
   * @param UserModel $Sender
   */
  public function UserModel_AfterSave_Handler($Sender) {
    Yaga::ExecuteBadgeHooks($Sender, __FUNCTION__);
  }

  /**
   * Check for Badge Awards
   * 
   * @param ReactionModel $Sender
   */
  public function ReactionModel_AfterReactionSave_Handler($Sender) {
    Yaga::ExecuteBadgeHooks($Sender, __FUNCTION__);
  }

  /**
   * Check for Badge Awards
   * 
   * @param BadgeAwardModel $Sender
   */
  public function BadgeAwardModel_AfterBadgeAward_Handler($Sender) {
    Yaga::ExecuteBadgeHooks($Sender, __FUNCTION__);
  }

  /**
   * Check for Badge Awards
   * 
   * @param mixed $Sender
   */
  public function Base_AfterConnection_Handler($Sender) {
    Yaga::ExecuteBadgeHooks($Sender, __FUNCTION__);
  }

  /**
   * Add the appropriate resources for each controller
   *
   * @param Gdn_Controller $Sender
   */
  private function AddResources($Sender) {
    $Sender->AddCssFile('reactions.css', 'yaga');
  }

  /**
   * Add global Yaga resources to all dashboard pages
   *
   * @param Gdn_Controller $Sender
   */
  public function Base_Render_Before($Sender) {
    if($Sender->MasterView == 'admin') {
      $Sender->AddCssFile('yaga.css', 'yaga');
    }
    else {
      if(Gdn::Session()->IsValid() && is_object($Sender->Menu) && C('Yaga.MenuLinks.Show')) {
        $this->AddMenuLinks($Sender->Menu);
      }
    }
  }
  
  /**
   * Adds links to the frontend
   * 
   * @since 1.1
   * @param MenuModule $Menu
   */
  protected function AddMenuLinks($Menu) {
    if(C('Yaga.Badges.Enabled')) {
      $Menu->AddLink('Yaga', T('Badges'), 'yaga/badges');
    }
    if(C('Yaga.Ranks.Enabled')) {
      $Menu->AddLink('Yaga', T('Ranks'), 'yaga/ranks');
    }
  }

  /**
   * Delete all of the Yaga related information for a specific user.
   * 
   * @param int $UserID The ID of the user to delete.
   * @param array $Options An array of options:
   *  - DeleteMethod: One of delete, wipe, or NULL
   * @param array $Data
   * 
   * @since 1.0
   */
   protected function DeleteUserData($UserID, $Options = array(), &$Data = NULL) {
    $SQL = Gdn::SQL();

    $DeleteMethod = GetValue('DeleteMethod', $Options, 'delete');
    if($DeleteMethod == 'delete') {
      // Remove neutral/negative reactions
      $Actions = Yaga::ActionModel()->GetWhere(array('AwardValue <' => 1))->Result();
      foreach($Actions as $Negative) {
        Gdn::UserModel()->GetDelete('Reaction', array('InsertUserID' => $UserID, 'ActionID' => $Negative->ActionID), $Data);
      }
    }
    else if($DeleteMethod == 'wipe') {
      // Completely remove reactions
      Gdn::UserModel()->GetDelete('Reaction', array('InsertUserID' => $UserID), $Data);
    }
    else {
      // Leave reactions
    }

    // Remove the reactions they have received
    Gdn::UserModel()->GetDelete('Reaction', array('ParentAuthorID' => $UserID), $Data);

    // Remove their badges
    Gdn::UserModel()->GetDelete('BadgeAward', array('UserID' => $UserID), $Data);

    // Blank the user's yaga information
    $SQL->Update('User')
            ->Set(array(
                'CountBadges' => 0,
                'RankID' => NULL,
                'RankProgression' => 0
            ))
            ->Where('UserID', $UserID)
            ->Put();

    // Trigger a system wide point recount
    // TODO: Look into point re-calculation
  }

  /**
	 * Remove Yaga data when deleting a user.
    *
    * @since 1.0
    * @package Yaga
    *
    * @param UserModel $Sender UserModel.
    */
   public function UserModel_BeforeDeleteUser_Handler($Sender) {
      $UserID = GetValue('UserID', $Sender->EventArguments);
      $Options = GetValue('Options', $Sender->EventArguments, array());
      $Options = is_array($Options) ? $Options : array();
      $Content =& $Sender->EventArguments['Content'];

      $this->DeleteUserData($UserID, $Options, $Content);
   }

  /**
   * Add update routines to the DBA controller
   *
   * @param DbaController $Sender
   */
  public function DbaController_CountJobs_Handler($Sender) {
    $Counts = array(
        'BadgeAward' => array('CountBadges')
    );

    foreach($Counts as $Table => $Columns) {
      foreach($Columns as $Column) {
        $Name = "Recalculate $Table.$Column";
        $Url = "/dba/counts.json?" . http_build_query(array('table' => $Table, 'column' => $Column));

        $Sender->Data['Jobs'][$Name] = $Url;
      }
    }
  }

  /**
   * Run the structure and stub scripts if necessary when the application is
   * enabled.
   */
  public function Setup() {
    $Config = Gdn::Factory(Gdn::AliasConfig);
    $Drop = C('Yaga.Version') === FALSE ? TRUE : FALSE;
    $Explicit = TRUE;
    include(PATH_APPLICATIONS . DS . 'yaga' . DS . 'settings' . DS . 'structure.php');
    include(PATH_APPLICATIONS . DS . 'yaga' . DS . 'settings' . DS . 'stub.php');

    $ApplicationInfo = array();
    include(CombinePaths(array(PATH_APPLICATIONS . DS . 'yaga' . DS . 'settings' . DS . 'about.php')));
    $Version = ArrayValue('Version', ArrayValue('Yaga', $ApplicationInfo, array()), 'Undefined');
    SaveToConfig('Yaga.Version', $Version);
  }
}
