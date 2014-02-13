<?php if(!defined('APPLICATION')) exit();

/**
 * A special function that is automatically run upon enabling your application.
 *
 * Remember to rename this to FooHooks, where 'Foo' is you app's short name.
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
  }

  /**
   * Add the badge count into the user info module
   *
   * @param UserInfoModule $Sender
   */
  public function UserInfoModule_OnBasicInfo_Handler($Sender) {
    if(!C('Yaga.Badges.Enabled')) {
      return;
    }
    echo '<dt class="Badges">' . T('Yaga.Badges', 'Badges') . '</dt> ';
    echo '<dd class="Badges">' . $Sender->User->CountBadges . '</dd>';
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
      return;
    }

    list($Offset, $Limit) = OffsetLimit($Page, C('Yaga.ReactedContent.PerPage', 5));
    if(!is_numeric($Offset) || $Offset < 0) {
      $Offset = 0;
    }

    $Sender->EditMode(FALSE);

    // Tell the ProfileController what tab to load
    $Sender->GetUserInfo($UserReference, $Username);
    $Sender->_SetBreadcrumbs(T('Yaga.Reactions'), UserUrl($Sender->User, '', 'reactions'));
    $Sender->SetTabView(T('Yaga.Reactions'), 'reactions', 'profile', 'Yaga');

    $Sender->AddJsFile('jquery.expander.js');
    $Sender->AddJsFile('reactions.js', 'yaga');
    $Sender->AddDefinition('ExpandText', T('(more)'));
    $Sender->AddDefinition('CollapseText', T('(less)'));

    $Model = new ActedModel();
    $Data = $Model->Get($Sender->User->UserID, $ActionID, $Limit, $Offset);

    $Sender->SetData('Content', $Data);

    // Set the HandlerType back to normal on the profilecontroller so that it fetches it's own views
    $Sender->HandlerType = HANDLER_TYPE_NORMAL;

    // Do not show discussion options
    $Sender->ShowOptions = FALSE;

    if($Sender->Head) {
      $Sender->Head->AddTag('meta', array('name' => 'robots', 'content' => 'noindex,noarchive'));
    }

    $ReactionModel = Yaga::ReactionModel();
    
    // Build a pager
    $PagerFactory = new Gdn_PagerFactory();
    $Sender->Pager = $PagerFactory->GetPager('Pager', $Sender);
    $Sender->Pager->ClientID = 'Pager';
    $Sender->Pager->Configure(
            $Offset, $Limit, $ReactionModel->GetUserCount($Sender->User->UserID, $ActionID), 'profile/reactions/' . $Sender->User->UserID . '/' . Gdn_Format::Url($Sender->User->Name) . '/' . $ActionID . '/%1$s/'
    );
    
    // Render the ProfileController
    $Sender->Render();
  }

  /**
   * Check for promotions on received points.
   *
   * @param UserModel $Sender
   */
  public function UserModel_GivePoints_Handler($Sender) {
    // Don't check for promotions if we aren't using ranks
    if(!C('Yaga.Ranks.Enabled')) {
      return;
    }
    $UserID = $Sender->EventArguments['UserID'];
    $UserModel = Gdn::UserModel();
    $User = $UserModel->GetID($UserID);

    // Don't try to promote if they are frozen
    if(!$User->RankProgression) {
      return;
    }

    $Points = $Sender->EventArguments['Points'];
    $RankModel = Yaga::RankModel();
    $Rank = $RankModel->GetByPoints($Points);

    if($Rank && $Rank->RankID != $User->RankID) {
      // Only promote automatically
      $OldRank = $RankModel->GetByID($User->RankID);
      if($OldRank->Level <= $Rank->Level) {
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
      $Sender->Preferences['Notifications']['Email.BadgeAward'] = T('Yaga.Notifications.Badges');
      $Sender->Preferences['Notifications']['Popup.BadgeAward'] = T('Yaga.Notifications.Badges');
    }

    if(C('Yaga.Ranks.Enabled')) {
      $Sender->Preferences['Notifications']['Email.RankPromotion'] = T('Yaga.Notifications.Ranks');
      $Sender->Preferences['Notifications']['Popup.RankPromotion'] = T('Yaga.Notifications.Ranks');
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
            'Text' => Sprite('SpRibbon') . ' ' . T('Yaga.Badge.Award'),
            'Url' => '/badge/award/' . $Sender->User->UserID,
            'CssClass' => 'Popup'
        );
      }

      if(C('Yaga.Ranks.Enabled') && CheckPermission('Yaga.Ranks.Add')) {
        $Sender->EventArguments['ProfileOptions'][] = array(
            'Text' => Sprite('SpModeratorActivities') . ' ' . T('Yaga.Rank.Promote'),
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
    RenderReactionRecord($ID, $Type);
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
    RenderReactionRecord($ID, $Type);
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
      RenderReactionList($ID, $Type);
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
    if($Activity->AllowComments == 0) {
      return;
    }

    // check to see if allowed to add reactions
    if(!Gdn::Session()->CheckPermission('Yaga.Reactions.Add')) {
      return;
    }

    // Activities can be by multiple users
    if(is_array($Activity->ActivityUserID) && in_array($CurrentUserID, $Activity->ActivityUserID)) {
      // User is part of a multiple user activity
    }
    else if($CurrentUserID == $Activity->ActivityUserID) {
      // User is the author of this activity
    }
    else {
      echo Wrap(RenderReactionList($ID, $Type, FALSE), 'div', array('class' => 'Reactions'));
    }
  }

  /**
   * Insert JS and CSS files into the appropiate controllers
   */
  public function ProfileController_Render_Before($Sender) {
    $this->_AddResources($Sender);

    if(C('Yaga.Badges.Enabled')) {
      $Sender->AddModule('BadgesModule');
    }
  }

  public function DiscussionController_Render_Before($Sender) {
    $this->_AddResources($Sender);
  }

  public function CommentController_Render_Before($Sender) {
    $this->_AddResources($Sender);
  }

  public function ActivityController_Render_Before($Sender) {
    $this->_AddResources($Sender);

    if(C('Yaga.LeaderBoard.Enabled', FALSE)) {
      // add leaderboard modules to the activity page
      $Module = new LeaderBoardModule();
      $Module->GetData('w');
      $Sender->AddModule($Module);
      $Module = new LeaderBoardModule();
      $Sender->AddModule($Module);
    }
  }

  /**
   * Check for Badge Awards where appropriate
   */
  public function Gdn_Dispatcher_AppStartup_Handler($Sender) {
    $this->_AwardBadges($Sender, __FUNCTION__);
  }

  public function Base_AfterGetSession_Handler($Sender) {
    $this->_AwardBadges($Sender, __FUNCTION__);
  }
  
  public function CommentModel_AfterSaveComment_Handler($Sender) {
    $this->_AwardBadges($Sender, __FUNCTION__);
  }

  public function DiscussionModel_AfterSaveDiscussion_Handler($Sender) {
    $this->_AwardBadges($Sender, __FUNCTION__);
  }
  
  public function ActivityModel_BeforeSaveComment_Handler($Sender) {
    $this->_AwardBadges($Sender, __FUNCTION__);
  }

  public function CommentModel_BeforeNotification_Handler($Sender) {
    $this->_AwardBadges($Sender, __FUNCTION__);
  }

  public function DiscussionModel_BeforeNotification_Handler($Sender) {
    $this->_AwardBadges($Sender, __FUNCTION__);
  }

  public function Base_AfterSignIn_Handler($Sender) {
    $this->_AwardBadges($Sender, __FUNCTION__);
  }

  public function UserModel_AfterSave_Handler($Sender) {
    $this->_AwardBadges($Sender, __FUNCTION__);
  }

  public function ReactionModel_AfterReactionSave_Handler($Sender) {
    $this->_AwardBadges($Sender, __FUNCTION__);
  }

  public function BadgeAwardModel_AfterBadgeAward_Handler($Sender) {
    $this->_AwardBadges($Sender, __FUNCTION__);
  }

  public function Base_AfterConnection_Handler($Sender) {
    $this->_AwardBadges($Sender, __FUNCTION__);
  }

  /**
   * This is the dispatcher to check badge awards
   *
   * @param Object $Sender The sending object
   * @param string $Handler The event handler to check associated rules for awards
   * (e.g. BadgeAwardModel_AfterBadgeAward_Handler or Base_AfterConnection)
   */
  private function _AwardBadges($Sender, $Handler) {
    $Session = Gdn::Session();
    if(!C('Yaga.Badges.Enabled') || !$Session->IsValid()) {
      return;
    }
    
    // Let's us use __FUNCTION__ in the original hook
    $Hook = str_ireplace('_Handler', '', $Handler); 
    
    if(Debug()) {
      $Controller = Gdn::Controller();
      if($Controller) {
        $Controller->InformMessage("Checking for awards on $Hook");
      }
    }
    
    $UserID = $Session->UserID;
    $User = $Session->User;
    
    $BadgeAwardModel = Yaga::BadgeAwardModel();
    $Badges = $BadgeAwardModel->GetUnobtained($UserID);

    $Rules = array();
    foreach($Badges as $Badge) {
      if($Badge->Enabled && $Badge->UserID != $UserID) {
        // The user doesn't have this badge
        $Class = $Badge->RuleClass;
        $Criteria = (object) unserialize($Badge->RuleCriteria);

        // Create a rule object if needed
        if(!in_array($Class, $Rules)) {
          $Rule = new $Class();
          $Rules[$Class] = $Rule;
        }

        // execute the Calculated
        $Rule = $Rules[$Class];
        if(in_array($Hook, $Rule->Hooks())) {
          $Result = $Rule->Award($Sender, $User, $Criteria);
          if($Result) {
            if(is_numeric($Result)) {
              $AwardedUserID = $Result;
            }
            else {
              $AwardedUserID = $UserID;
            }
            $BadgeAwardModel->Award($Badge->BadgeID, $AwardedUserID, $UserID);
          }
        }
      }
    }
  }

  /**
   * Add the appropriate resources for each controller
   *
   * @param object $Sender
   */
  private function _AddResources($Sender) {
    $Sender->AddCssFile('reactions.css', 'yaga');
  }

  /**
   * Add global Yaga resources to all dashboard pages
   *
   * @param object $Sender
   */
  public function Base_Render_Before($Sender) {
    if($Sender->MasterView == 'admin') {
      $Sender->AddCssFile('yaga.css', 'yaga');
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
