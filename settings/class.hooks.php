<?php if(!defined('APPLICATION')) exit();

/**
 * A special function that is automatically run upon enabling your application.
 *
 * Remember to rename this to FooHooks, where 'Foo' is you app's short name.
 */
class YagaHooks implements Gdn_IPlugin {

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
   * @param object $Sender
   */
  public function ProfileController_AfterUserInfo_Handler($Sender) {
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
    $Actions = $ReactionModel->GetActions();
    $String = '';
    foreach($Actions as $Action) {
      $Selected = ($ActionID == $Action->ActionID) ? ' Selected' : '';
      $Count = $ReactionModel->GetUserReactionCount($User->UserID, $Action->ActionID);
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
  public function ProfileController_Reactions_Create($Sender, $UserReference = '', $Username = '', $ActionID = '', $Page = '') {
    $Expiry = 60;

    $Sender->EditMode(FALSE);

    // Tell the ProfileController what tab to load
    $Sender->GetUserInfo($UserReference, $Username);
    $Sender->_SetBreadcrumbs(T('Yaga.Reactions'), UserUrl($Sender->User, '', 'reactions'));
    $Sender->SetTabView(T('Yaga.Reactions'), 'reactions', 'profile', 'Yaga');

    $Sender->AddJsFile('jquery.expander.js');
    $Sender->AddJsFile('reactions.js', 'yaga');
    $Sender->AddDefinition('ExpandText', T('(more)'));
    $Sender->AddDefinition('CollapseText', T('(less)'));

    $PageSize = Gdn::Config('Vanilla.Discussions.PerPage', 30);
    list($Offset, $Limit) = OffsetLimit($Page, $PageSize);

    // Check cache
    $CacheKey = "yaga.profile.reactions.{$ActionID}";
    $Content = Gdn::Cache()->Get($CacheKey);

    if($Content == Gdn_Cache::CACHEOP_FAILURE) {

      // Get matching Discussions
      $Type = 'discussion';
      $Discussions = Gdn::SQL()->Select('d.*')
                      ->From('Discussion d')
                      ->Join('Reaction r', 'd.DiscussionID = r.ParentID')
                      //->Where('r.ParentAuthorID', $UserReference)
                      ->Where('d.InsertUserID', $UserReference)
                      ->Where('r.ActionID', $ActionID)
                      //     ->Where('r.ParentType', $Type)
                      ->OrderBy('r.DateInserted', 'DESC')
                      ->Limit($Limit)
                      ->Get()->Result(DATASET_TYPE_ARRAY);

      // Get matching Comments
      $Comments = Gdn::SQL()->Select('c.*')
                      ->From('Comment c')
                      ->Join('Reaction r', 'c.CommentID = r.ParentID')
                      //->Where('r.ParentAuthorID', 'c.InsertUserID')
                      //     ->Where('r.ParentType', '\'comment\'')
                      ->Where('c.InsertUserID', $UserReference)
                      ->Where('r.ActionID', $ActionID)
                      //     ->Where('r.ParentType', $Type)
                      ->OrderBy('r.DateInserted', 'DESC')
                      ->Limit($Limit)
                      ->Get()->Result(DATASET_TYPE_ARRAY);

      $this->JoinCategory($Comments);

      // Interleave
      $Content = $this->Union('DateInserted', array(
          'Discussion' => $Discussions,
          'Comment' => $Comments
      ));
      $this->Prepare($Content);

      // Add result to cache
      Gdn::Cache()->Store($CacheKey, $Content, array(
          Gdn_Cache::FEATURE_EXPIRY => $Expiry
      ));
    }

    $this->Security($Content);
    $this->Condense($Content, $Limit);

    // Deliver JSON data if necessary
    if($Sender->DeliveryType() != DELIVERY_TYPE_ALL && $Offset > 0) {
      $Sender->SetJson('LessRow', $Sender->Pager->ToString('less'));
      $Sender->SetJson('MoreRow', $Sender->Pager->ToString('more'));
      $Sender->View = 'reactions';
    }

    $Sender->SetData('Content', $Content);

    // Set the HandlerType back to normal on the profilecontroller so that it fetches it's own views
    $Sender->HandlerType = HANDLER_TYPE_NORMAL;

    // Do not show discussion options
    $Sender->ShowOptions = FALSE;

    if($Sender->Head) {
      $Sender->Head->AddTag('meta', array('name' => 'robots', 'content' => 'noindex,noarchive'));
    }

    // Render the ProfileController
    $Sender->Render();
  }

  /**
   * Attach CategoryID to Comments
   * 
   * @param array $Comments
   */
  protected function JoinCategory(&$Comments) {
    $DiscussionIDs = array();

    foreach($Comments as &$Comment) {
      $DiscussionIDs[$Comment['DiscussionID']] = TRUE;
    }
    $DiscussionIDs = array_keys($DiscussionIDs);

    $Discussions = Gdn::SQL()->Select('d.*')
                    ->From('Discussion d')
                    ->WhereIn('DiscussionID', $DiscussionIDs)
                    ->Get()->Result(DATASET_TYPE_ARRAY);

    $DiscussionsByID = array();
    foreach($Discussions as $Discussion) {
      $DiscussionsByID[$Discussion['DiscussionID']] = $Discussion;
    }
    unset(${$Discussions});

    foreach($Comments as &$Comment) {
      $Comment['Discussion'] = $DiscussionsByID[$Comment['DiscussionID']];
      $Comment['CategoryID'] = GetValueR('Discussion.CategoryID', $Comment);
    }
  }

  /**
   * Interleave two or more result arrays by a common field
   * 
   * @param string $Field
   * @param array $Sections Array of result arrays
   * @return array
   */
  protected function Union($Field, $Sections) {
    if(!is_array($Sections))
      return;

    $Interleaved = array();
    foreach($Sections as $SectionType => $Section) {
      if(!is_array($Section))
        continue;

      foreach($Section as $Item) {
        $ItemField = GetValue($Field, $Item);
        $Interleaved[$ItemField] = array_merge($Item, array('ItemType' => $SectionType));

        ksort($Interleaved);
      }
    }

    $Interleaved = array_reverse($Interleaved);
    return $Interleaved;
  }

  /**
   * Pre-process content into a uniform format for output
   * 
   * @param Array $Content By reference
   */
  protected function Prepare(&$Content) {

    foreach($Content as &$ContentItem) {
      $ContentType = GetValue('ItemType', $ContentItem);

      $Replacement = array();
      $Fields = array('DiscussionID', 'CategoryID', 'DateInserted', 'DateUpdated', 'InsertUserID', 'Body', 'Format', 'ItemType');

      switch(strtolower($ContentType)) {
        case 'comment':
          $Fields = array_merge($Fields, array('CommentID'));

          // Comment specific
          $Replacement['Name'] = GetValueR('Discussion.Name', $ContentItem);
          break;

        case 'discussion':
          $Fields = array_merge($Fields, array('Name', 'Type'));
          break;
      }

      $Fields = array_fill_keys($Fields, TRUE);
      $Common = array_intersect_key($ContentItem, $Fields);
      $Replacement = array_merge($Replacement, $Common);
      $ContentItem = $Replacement;

      // Attach User
      $UserID = GetValue('InsertUserID', $ContentItem);
      $User = Gdn::UserModel()->GetID($UserID);
      $ContentItem['Author'] = $User;
    }
  }

  /**
   * Strip out content that this user is not allowed to see
   * 
   * @param array $Content Content array, by reference
   */
  protected function Security(&$Content) {
    if(!is_array($Content))
      return;
    $Content = array_filter($Content, array($this, 'SecurityFilter'));
  }

  protected function SecurityFilter($ContentItem) {
    $CategoryID = GetValue('CategoryID', $ContentItem, NULL);
    if(is_null($CategoryID) || $CategoryID === FALSE)
      return FALSE;

    $Category = CategoryModel::Categories($CategoryID);
    $CanView = GetValue('PermsDiscussionsView', $Category);
    if(!$CanView)
      return FALSE;

    return TRUE;
  }

  /**
   * Condense an interleaved content list down to the required size
   * 
   * @param array $Content
   * @param array $Limit
   */
  protected function Condense(&$Content, $Limit) {
    $Content = array_slice($Content, 0, $Limit);
  }

  /**
   * Check for promotions on received points.
   * 
   * @param type $Sender
   * @return type
   */
  public function UserModel_GivePoints_Handler($Sender) {
    // Don't check for promotions if we aren't using ranks
    if(!C('Yaga.Ranks.Enabled')) {
      return;
    }
    $UserID = $Sender->EventArguments['UserID'];
    $UserModel = Gdn::UserModel();
    $User = $UserModel->GetID($UserID);
    $Points = $Sender->EventArguments['Points'];

    $RankModel = Yaga::RankModel();

    $Rank = $RankModel->GetRankByPoints($Points);

    if($Rank && $Rank->RankID != $User->RankID) {
      // Only promote automatically
      $OldRank = $RankModel->GetID($User->RankID);
      if($OldRank->Level <= $Rank->Level) {
        // Throw up a promotion activity
        $ActivityModel = new ActivityModel();

        $Activity = array(
            'ActivityType' => 'RankPromotion',
            'ActivityUserID' => $UserID,
            'RegardingUserID' => $UserID,
            'Photo' => 'uploads' . DS . C('Yaga.Ranks.Photo'),
            'RecordType' => 'Rank',
            'RecordID' => $Rank->RankID,
            'HeadlineFormat' => T('Yaga.HeadlineFormat.Promoted'),
            'Data' => array(
                'Name' => $Rank->Name
            ),
            'Story' => $Rank->Description
        );

        $ActivityModel->Queue($Activity);

        // Notify the user of the award
        $Activity['NotifyUserID'] = $UserID;
        $Activity['Emailed'] = ActivityModel::SENT_PENDING;
        $ActivityModel->Queue($Activity, 'Ranks', array('Force' => TRUE));

        $ActivityModel->SaveQueue();

        // Update the rank id
        $UserModel->SetField($UserID, 'RankID', $Rank->RankID);

        // Get the user's roles
        $CurrentRoleData = $UserModel->GetRoles($UserID);
        $CurrentRoleIDs = ConsolidateArrayValuesByKey($CurrentRoleData->Result(), 'RoleID');
        
        // Remove the old roles
        $TempRoleIDs = array_diff($CurrentRoleIDs, array($OldRank->Role));

        // Add our selected roles
        $NewRoleIDs = array_unique(array_merge($TempRoleIDs, array($Rank->Role)));

        // Set the combined roles
        if($NewRoleIDs != $CurrentRoleIDs) {
          $UserModel->SaveRoles($UserID, $NewRoleIDs);
        }
      }
    }
  }

  /**
   * Transparently inject the rank permissions a user has earned
   *
   */

  /**
   * Add the badge and rank notification options
   *
   * @param object $Sender
   */
  public function ProfileController_AfterPreferencesDefined_Handler($Sender) {
    if(C('Yaga.Badges.Enabled')) {
      $Sender->Preferences['Notifications']['Email.Badges'] = T('Yaga.Notifications.Badges');
      $Sender->Preferences['Notifications']['Popup.Badges'] = T('Yaga.Notifications.Badges');
    }

    if(C('Yaga.Ranks.Enabled')) {
      $Sender->Preferences['Notifications']['Email.Ranks'] = T('Yaga.Notifications.Ranks');
      $Sender->Preferences['Notifications']['Popup.Ranks'] = T('Yaga.Notifications.Ranks');
    }
  }

  /**
   * Add the Award Badge and Promote options to the profile controller
   *
   * @param object $Sender
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
   * @param object $Sender
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
   * @param object $Sender
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
   * @param object $Sender
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
      RenderReactions($ID, $Type);
    }
  }

  /**
   * Add the action list to any activity items that can be commented on
   *
   * @param object $Sender
   */
  public function ActivityController_AfterActivityBody_Handler($Sender) {
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
      echo Wrap(RenderReactions($ID, $Type, FALSE), 'div', array('class' => 'Reactions'));
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
  public function CommentModel_AfterSaveComment_Handler($Sender) {
    $this->_AwardBadges($Sender, 'CommentModel_AfterSaveComment');
  }

  public function DiscussionModel_AfterSaveDiscussion_Handler($Sender) {
    $this->_AwardBadges($Sender, 'DiscussionModel_AfterSaveDiscussion');
  }

  public function CommentModel_BeforeNotification_Handler($Sender) {
    $this->_AwardBadges($Sender, 'CommentModel_BeforeNotification');
  }

  public function DiscussionModel_BeforeNotification_Handler($Sender) {
    $this->_AwardBadges($Sender, 'DiscussionModel_BeforeNotification');
  }

  public function Base_AfterSignIn_Handler($Sender) {
    $this->_AwardBadges($Sender, 'Base_AfterSignIn');
  }

  public function UserModel_AfterSave_Handler($Sender) {
    $this->_AwardBadges($Sender, 'UserModel_AfterSave');
  }

  public function ReactionModel_AfterReactionSave_Handler($Sender) {
    $this->_AwardBadges($Sender, 'ReactionModel_AfterReactionSave');
  }

  public function BadgeAwardModel_AfterBadgeAward_Handler($Sender) {
    $this->_AwardBadges($Sender, 'BadgeAwardModel_AfterBadgeAward');
  }

  public function Base_AfterConnection_Handler($Sender) {
    $this->_AwardBadges($Sender, 'Base_AfterConnection');
  }

  /**
   * This is the dispatcher to check badge awards
   *
   * @param string $Hook The rule hooks to check
   */
  private function _AwardBadges($Sender, $Hook) {
    if(!C('Yaga.Badges.Enabled')) {
      return;
    }

    $Session = Gdn::Session();
    if(!$Session->IsValid())
      return;

    $UserID = $Session->UserID;
    $UserModel = new UserModel();
    $User = $UserModel->GetID($UserID);

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
   * @param type $Sender
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
