<?php if(!defined('APPLICATION')) exit();

/**
 * A special function that is automatically run upon enabling your application.
 *
 * Remember to rename this to FooHooks, where 'Foo' is you app's short name.
 */
class YagaHooks implements Gdn_IPlugin {

  static $_ReactionModel = NULL;

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
    $Menu->AddLink($Section, 'Settings', 'configure', 'Garden.Settings.Manage');
    if(C('Yaga.Reactions.Enabled')) {
      $Menu->AddLink($Section, 'Reactions', 'action/settings', 'Yaga.Reactions.Manage');
    }
    if(C('Yaga.Badges.Enabled')) {
      $Menu->AddLink($Section, 'Badges', 'badge/settings', 'Yaga.Badges.Manage');
    }
    if(C('Yaga.Ranks.Enabled')) {
      $Menu->AddLink($Section, 'Ranks', 'rank/settings', 'Yaga.Ranks.Manage');
    }
  }

  /**
   * Display the reaction counts on the profile page
   * @param object $Sender
   */
  public function ProfileController_AfterUserInfo_Handler($Sender) {
    $User = $Sender->User;
    echo '<div class="Yaga ReactionsWrap">';
    echo Wrap(T('Yaga.Reactions', 'Reactions'), 'h2', array('class' => 'H'));

    // insert the reaction totals in the profile
    $Actions = $this->_ReactionModel->GetActions();
    $String = '';
    foreach($Actions as $Action) {
      $Count = $this->_ReactionModel->GetUserReactionCount($User->UserID, $Action->ActionID);
      $TempString = Wrap(Wrap(Gdn_Format::BigNumber($Count), 'span', array('title' => $Count)), 'span', array('class' => 'Yaga_ReactionCount CountTotal'));
      $TempString .= Wrap($Action->Name, 'span', array('class' => 'Yaga_ReactionName CountLabel'));

      $String .= Wrap(Wrap(Anchor($TempString, '/profile/reactions/' . $User->UserID . '/' . Gdn_Format::Url($User->Name) . '/' . $Action->ActionID, array('class' => 'Yaga_Reaction TextColor', 'title' => $Action->Description)), 'span', array('class' => 'CountItem')), 'span', array('class' => 'CountItemWrap'));
    }

    echo Wrap($String, 'div', array('class' => 'DataCounts'));
  }

  /**
   * @todo document
   * @todo create pager
   * @param type $Sender
   * @param type $UserReference
   * @param type $Username
   * @param type $ActionID
   * @param type $Page
   * @param type $UserID
   */
  public function ProfileController_Reactions_Create($Sender, $UserReference = '', $Username = '', $ActionID = '', $Page = '', $UserID = '') {
    $Expiry = 60;
    
    $Sender->EditMode(FALSE);

    // Tell the ProfileController what tab to load
	$Sender->GetUserInfo($UserReference, $Username, $UserID);
    $Sender->_SetBreadcrumbs(T('Reactions'), UserUrl($Sender->User, '', 'reactions'));
    $Sender->SetTabView('Reactions', 'reactions', 'profile', 'Yaga');
    
    $Sender->AddJsFile('jquery.expander.js');
    $Sender->AddJsFile('reactions.js', 'yaga');
    $Sender->AddDefinition('ExpandText', T('(more)'));
    $Sender->AddDefinition('CollapseText', T('(less)'));
      
    $PageSize = Gdn::Config('Vanilla.Discussions.PerPage', 30);
    list($Offset, $Limit) = OffsetLimit($Page, $PageSize);

    // Check cache
    $CacheKey = "yaga.profile.reactions.{$ActionID}";
    $Content = Gdn::Cache()->Get($CacheKey);
      
      if ($Content == Gdn_Cache::CACHEOP_FAILURE) {
         
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
            'Discussion'   => $Discussions, 
            'Comment'      => $Comments
         ));
         $this->Prepare($Content);
         
         // Add result to cache
         Gdn::Cache()->Store($CacheKey, $Content, array(
            Gdn_Cache::FEATURE_EXPIRY  => $Expiry
         ));
      }
      
      $this->Security($Content);
      $this->Condense($Content, $Limit);

    // Deliver JSON data if necessary
    if ($Sender->DeliveryType() != DELIVERY_TYPE_ALL && $Offset > 0) {
       $Sender->SetJson('LessRow', $Sender->Pager->ToString('less'));
       $Sender->SetJson('MoreRow', $Sender->Pager->ToString('more'));
       $Sender->View = 'reactions';
    }

    $Sender->SetData('Content', $Content);
    
    // Set the HandlerType back to normal on the profilecontroller so that it fetches it's own views
    $Sender->HandlerType = HANDLER_TYPE_NORMAL;

    // Do not show discussion options
    $Sender->ShowOptions = FALSE;

    if ($Sender->Head) {
       $Sender->Head->AddTag('meta', array('name' => 'robots', 'content' => 'noindex,noarchive'));
    }

    // Render the ProfileController
    $Sender->Render();
  }
   
   /**
    * Attach CategoryID to Comments
    * @todo move somewhere else
    * @param array $Comments
    */
   protected function JoinCategory(&$Comments) {
      $DiscussionIDs = array();
      
      foreach ($Comments as &$Comment) {
         $DiscussionIDs[$Comment['DiscussionID']] = TRUE;
      }
      $DiscussionIDs = array_keys($DiscussionIDs);
      
      $Discussions = Gdn::SQL()->Select('d.*')
         ->From('Discussion d')
         ->WhereIn('DiscussionID', $DiscussionIDs)
         ->Get()->Result(DATASET_TYPE_ARRAY);
      
      $DiscussionsByID = array();
      foreach ($Discussions as $Discussion) {
         $DiscussionsByID[$Discussion['DiscussionID']] = $Discussion;
      }
      unset(${$Discussions});
      
      foreach ($Comments as &$Comment) {
         $Comment['Discussion'] = $DiscussionsByID[$Comment['DiscussionID']];
         $Comment['CategoryID'] = GetValueR('Discussion.CategoryID', $Comment);
      }
   }
   
   /**
    * Interleave two or more result arrays by a common field
    * @todo move somewhere else
    * @param string $Field
    * @param array $Sections Array of result arrays
    * @return array
    */
   protected function Union($Field, $Sections) {
      if (!is_array($Sections)) return;
      
      $Interleaved = array();
      foreach ($Sections as $SectionType => $Section) {
         if (!is_array($Section)) continue;
         
         foreach ($Section as $Item) {
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
    * @todo move somewhere else
    * @param Array $Content By reference
    */
   protected function Prepare(&$Content) {
      
      foreach ($Content as &$ContentItem) {
         $ContentType = GetValue('ItemType', $ContentItem);
         
         $Replacement = array();
         $Fields = array('DiscussionID', 'CategoryID', 'DateInserted', 'DateUpdated', 'InsertUserID', 'Body', 'Format', 'ItemType');
         
         switch (strtolower($ContentType)) {
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
    * @todo move somewhere else
    * @param array $Content Content array, by reference
    */
   protected function Security(&$Content) {
      if (!is_array($Content)) return;
      $Content = array_filter($Content, array($this, 'SecurityFilter'));
   }
   
   protected function SecurityFilter($ContentItem) {
      $CategoryID = GetValue('CategoryID', $ContentItem, NULL);
      if (is_null($CategoryID) || $CategoryID === FALSE)
         return FALSE;

      $Category = CategoryModel::Categories($CategoryID);
      $CanView = GetValue('PermsDiscussionsView', $Category);
      if (!$CanView)
         return FALSE;
      
      return TRUE;
   }
   
   /**
    * Condense an interleaved content list down to the required size
    * @todo move somewhere else
    * @param array $Content
    * @param array $Limit
    */
   protected function Condense(&$Content, $Limit) {
      $Content = array_slice($Content, 0, $Limit);
   }
  
  /**
   * Add the badge and rank notification options
   * 
   * @param object $Sender
   */
  public function ProfileController_AfterPreferencesDefined_Handler($Sender) {
    if(C('Yaga.Badges.Enabled')) {
      $Sender->Preferences['Notifications']['Email.Badges'] = T('Notify me when I earn a badge.');
      $Sender->Preferences['Notifications']['Popup.Badges'] = T('Notify me when I earn a badge.');
    }

    if(C('Yaga.Ranks.Enabled')) {
      $Sender->Preferences['Notifications']['Email.Ranks'] = T('Notify me when I am promoted in rank.');
      $Sender->Preferences['Notifications']['Popup.Ranks'] = T('Notify me when I am promoted in rank.');
    }
  }

  /**
   * Add the Award Badge option to the profile controller
   *
   * @param object $Sender
   */
  public function ProfileController_BeforeProfileOptions_Handler($Sender) {
    if(Gdn::Session()->IsValid() && CheckPermission('Yaga.Badges.Add')) {
      //decho($Sender->EventArguments);
      $Sender->EventArguments['ProfileOptions'][] = array(
          'Text' => Sprite('SpModeratorActivities') . ' ' . T('Give Badge'),
          'Url' => '/badge/award/' . $Sender->User->UserID,
          'CssClass' => 'Popup'
      );
    }
  }

  /**
   * Display a record of reactions after the first post
   *
   * @param object $Sender
   */
  public function DiscussionController_AfterDiscussionBody_Handler($Sender) {
    $Type = 'discussion';
    $ID = $Sender->DiscussionID;
    $this->_RenderCurrentReactions($ID, $Type);
  }

  /**
   * Display a record of reactions after comments
   * @param object $Sender
   */
  public function DiscussionController_AfterCommentBody_Handler($Sender) {
    $Type = 'comment';
    $ID = $Sender->EventArguments['Comment']->CommentID;
    $this->_RenderCurrentReactions($ID, $Type);
  }

  /**
   * Renders the reaction record for a specific item
   * @param int $ID
   * @param enum $Type 'discussion', 'activity', or 'comment'
   */
  protected function _RenderCurrentReactions($ID, $Type) {
    // check to see if allowed to view reactions
    if(!Gdn::Session()->CheckPermission('Yaga.Reactions.View')) {
      return;
    }

    if(empty($this->_ReactionModel)) {
      $this->_ReactionModel = new ReactionModel();
    }

    $Reactions = $this->_ReactionModel->GetAllReactions($ID, $Type);
    foreach($Reactions as $Reaction) {
      if($Reaction->UserIDs) {
        foreach($Reaction->UserIDs as $Index => $UserID) {
          $User = Gdn::UserModel()->GetID($UserID);
          $String = UserPhoto($User, array('Size' => 'Small'));
          $String .= '<span class="ReactSprite Reaction-' . $Reaction->ActionID . ' ' . $Reaction->CssClass . '"></span>';
          $Wrapttributes = array(
              'class' => 'UserReactionWrap',
              'data-userid' => $User->UserID,
              'title' => $User->Name . ' - ' . $Reaction->Name . ' on ' . Gdn_Format::Date($Reaction->Dates[$Index], '%B %e, %Y')
          );
          echo Wrap($String, 'span', $Wrapttributes);
        }
      }
    }
  }

  /**
   * Add action list to discussion items
   * @param object $Sender
   */
  public function DiscussionController_AfterReactions_Handler($Sender) {
    if(C('Yaga.Reactions.Enabled') == FALSE) {
      return;
    }

    $Sender->AddJsFile('reactions.js', 'yaga');
    $Sender->AddCssFile('reactions.css', 'yaga');
    // check to see if allowed to add reactions
    if(!Gdn::Session()->CheckPermission('Yaga.Reactions.Add')) {
      return;
    }

    // Users shouldn't be able to react to their own content
    $Type = $Sender->EventArguments['RecordType'];
    $ID = $Sender->EventArguments['RecordID'];

    // Users shouldn't be able to react to their own content
    if(Gdn::Session()->UserID != $Sender->EventArguments['Author']->UserID) {
      $this->_RenderActions($ID, $Type);
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
      echo Wrap($this->_RenderActions($ID, $Type, FALSE), 'div', array('class' => 'Reactions'));
    }
  }

  /**
   * Renders an action list that also contains the current count of reactions
   * an item has received
   *
   * @param int $ID
   * @param enum $Type 'discussion', 'activity', or 'comment'
   * @param bool $Echo Should it be echoed?
   * @return mixed String if $Echo is false, TRUE otherwise
   */
  public function _RenderActions($ID, $Type, $Echo = TRUE) {
    if(empty($this->_ReactionModel)) {
      $this->_ReactionModel = new ReactionModel();
    }

    $Reactions = $this->_ReactionModel->GetAllReactions($ID, $Type);
    $ActionsString = '';
    foreach($Reactions as $Action) {
      if(CheckPermission($Action->Permission)) {
        $ActionsString .= Anchor(
                Wrap('&nbsp;', 'span', array('class' => 'ReactSprite React-' . $Action->ActionID . ' ' . $Action->CssClass)) .
                WrapIf(count($Action->UserIDs), 'span', array('class' => 'Count')) .
                Wrap($Action->Name, 'span', array('class' => 'ReactLabel')), 'react/' . $Type . '/' . $ID . '/' . $Action->ActionID, 'Hijack ReactButton'
        );
      }
    }

    $AllActionsString = Wrap($ActionsString, 'span', array('class' => 'ReactMenu'));

    if($Echo) {
      echo $AllActionsString;
      return true;
    }
    else {
      return $AllActionsString;
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

  public function BadgeModel_AfterBadgeAward_Handler($Sender) {
    $this->_AwardBadges($Sender, 'BadgeModel_AfterBadgeAward');
  }
  
  public function Base_AfterConnection_Handler($Sender) {
    $this->_AwardBadges($Sender, 'Base_AfterConnection');
  }

  /**
   * This is the dispatcher to check badge awards
   * @todo Optimize this by caching the rules... or something
   *
   * @param string $Hook The rule hooks to check
   */
  private function _AwardBadges($Sender, $Hook) {
    if(!C('Yaga.Badges.Enabled', FALSE)) {
      return;
    }

    $Session = Gdn::Session();
    if(!$Session->IsValid())
      return;

    $UserID = $Session->UserID;
    $UserModel = new UserModel();
    $User = $UserModel->GetID($UserID);

    $BadgeModel = new BadgeModel();
    $Badges = $BadgeModel->GetAllBadgesUserAwards($UserID);

    $Rules = array();
    foreach($Badges as $Badge) {
      if($Badge->Enabled
              && $Badge->UserID != $UserID) {
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
            $BadgeModel->AwardBadge($Badge->BadgeID, $AwardedUserID, $UserID);
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
    if(empty($this->_ReactionModel)) {
      $this->_ReactionModel = new ReactionModel();
    }

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

  /**
   * Special function automatically run upon clicking 'Disable' on your application.
   * @todo Determine if I need to do anything on disable.
   */
  public function OnDisable() {

  }

  /**
   * Special function automatically run upon clicking 'Remove' on your application.
   * @todo Determine if I need to do anything on removal.
   */
  public function CleanUp() {

  }

}