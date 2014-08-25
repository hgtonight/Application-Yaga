<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013-2014 Zachary Doll */

/**
 * Describe the user content that has been acted upon
 *
 * Events:
 *
 * @package Yaga
 * @since 1.0
 */
class ActedModel extends Gdn_Model {

  /**
   * How long in seconds this table should be cached. Defaults to 10 minutes
   * @var int
   */
  protected $_Expiry = 600;

  /**
   * Convenience function to save some typing. Gets the basic 'best' query set
   * up in an SQL driver and returns it
   * @param string $Table Discussion or Comment
   * @return Gdn_SQLDriver
   */
  private function _BaseSQL($Table = 'Discussion') {
    switch($Table) {
      case 'Comment':
        $SQL = Gdn::SQL()->Select('c.*')
                ->From('Comment c')
                ->Where('c.Score is not null')
                ->OrderBy('c.Score', 'DESC');
        break;
      default:
      case 'Discussion':
        $SQL = Gdn::SQL()->Select('d.*')
                ->From('Discussion d')
                ->Where('d.Score is not null')
                ->OrderBy('d.Score', 'DESC');
        break;
    }
    return $SQL;
  }

  /**
   * Returns a list of all posts by a specific user that has received at least
   * one of the specified actions.
   * 
   * @param int $UserID
   * @param int $ActionID
   * @param int $Limit
   * @param int $Offset
   * @return array
   */
  public function Get($UserID, $ActionID, $Limit = NULL, $Offset = 0) {
    $CacheKey = "yaga.profile.reactions.{$UserID}.{$ActionID}";
    $Content = Gdn::Cache()->Get($CacheKey);

    if($Content == Gdn_Cache::CACHEOP_FAILURE) {

      // Get matching Discussions
      $Discussions = $this->_BaseSQL('Discussion')
                      ->Join('Reaction r', 'd.DiscussionID = r.ParentID')
                      ->Where('d.InsertUserID', $UserID)
                      ->Where('r.ActionID', $ActionID)
                      ->Where('r.ParentType', 'discussion')
                      ->OrderBy('r.DateInserted', 'DESC')
                      ->Get()->Result(DATASET_TYPE_ARRAY);

      // Get matching Comments
      $Comments = $this->_BaseSQL('Comment')
                      ->Join('Reaction r', 'c.CommentID = r.ParentID')
                      ->Where('c.InsertUserID', $UserID)
                      ->Where('r.ActionID', $ActionID)
                      ->Where('r.ParentType', 'comment')
                      ->OrderBy('r.DateInserted', 'DESC')
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
          Gdn_Cache::FEATURE_EXPIRY => $this->_Expiry
      ));
    }

    $this->Security($Content);
    $this->Condense($Content, $Limit, $Offset);

    return $Content;
  }
  
  /**
   * Returns a list of all posts of which a specific user has taken the
   * specified action.
   *
   * @param int $UserID
   * @param int $ActionID
   * @param int $Limit
   * @param int $Offset
   * @return array
   */
  public function GetTaken($UserID, $ActionID, $Limit = NULL, $Offset = 0) {
    $CacheKey = "yaga.profile.actions.{$UserID}.{$ActionID}";
    $Content = Gdn::Cache()->Get($CacheKey);

    if($Content == Gdn_Cache::CACHEOP_FAILURE) {

      // Get matching Discussions
      $Discussions = $this->_BaseSQL('Discussion')
                      ->Join('Reaction r', 'd.DiscussionID = r.ParentID')
                      ->Where('r.InsertUserID', $UserID)
                      ->Where('r.ActionID', $ActionID)
                      ->Where('r.ParentType', 'discussion')
                      ->OrderBy('r.DateInserted', 'DESC')
                      ->Get()->Result(DATASET_TYPE_ARRAY);

      // Get matching Comments
      $Comments = $this->_BaseSQL('Comment')
                      ->Join('Reaction r', 'c.CommentID = r.ParentID')
                      ->Where('r.InsertUserID', $UserID)
                      ->Where('r.ActionID', $ActionID)
                      ->Where('r.ParentType', 'comment')
                      ->OrderBy('r.DateInserted', 'DESC')
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
          Gdn_Cache::FEATURE_EXPIRY => $this->_Expiry
      ));
    }

    $this->Security($Content);
    $this->Condense($Content, $Limit, $Offset);

    return $Content;
  }

  /**
   * Returns a list of all posts that has received at least one of the
   * specified actions.
   * 
   * @param int $ActionID
   * @param int $Limit
   * @param int $Offset
   * @return array
   */
  public function GetAction($ActionID, $Limit = NULL, $Offset = 0) {
    $CacheKey = "yaga.best.actions.{$ActionID}";
    $Content = Gdn::Cache()->Get($CacheKey);

    if($Content == Gdn_Cache::CACHEOP_FAILURE) {

      // Get matching Discussions
      $Discussions = $this->_BaseSQL('Discussion')
                      ->Join('Reaction r', 'd.DiscussionID = r.ParentID')
                      ->Where('r.ActionID', $ActionID)
                      ->Where('r.ParentType', 'discussion')
                      ->OrderBy('r.DateInserted', 'DESC')
                      ->Get()->Result(DATASET_TYPE_ARRAY);

      // Get matching Comments
      $Comments = $this->_BaseSQL('Comment')
                      ->Join('Reaction r', 'c.CommentID = r.ParentID')
                      ->Where('r.ActionID', $ActionID)
                      ->Where('r.ParentType', 'comment')
                      ->OrderBy('r.DateInserted', 'DESC')
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
          Gdn_Cache::FEATURE_EXPIRY => $this->_Expiry
      ));
    }

    $this->Security($Content);
    $this->Condense($Content, $Limit, $Offset);

    return $Content;
  }

  /**
   * Returns a list of all posts by a specific user ordered by highest score
   * 
   * @param int $UserID
   * @param int $Limit
   * @param int $Offset
   * @return array
   */
  public function GetBest($UserID = NULL, $Limit = NULL, $Offset = 0) {
    $CacheKey = "yaga.profile.best.{$UserID}";
    $Content = Gdn::Cache()->Get($CacheKey);

    if($Content == Gdn_Cache::CACHEOP_FAILURE) {
      $SQL = $this->_BaseSQL('Discussion');
      if(!is_null($UserID)) {
        $SQL = $SQL->Where('d.InsertUserID', $UserID);
      }
      $Discussions = $SQL->Get()->Result(DATASET_TYPE_ARRAY);

      $SQL = $this->_BaseSQL('Comment');
      if(!is_null($UserID)) {
        $SQL = $SQL->Where('c.InsertUserID', $UserID);
      }
      $Comments = $SQL->Get()->Result(DATASET_TYPE_ARRAY);

      $this->JoinCategory($Comments);

      // Interleave
      $Content = $this->Union('Score', array(
          'Discussion' => $Discussions,
          'Comment' => $Comments
      ));
      $this->Prepare($Content);

      Gdn::Cache()->Store($CacheKey, $Content, array(
          Gdn_Cache::FEATURE_EXPIRY => $this->_Expiry
      ));
    }

    $this->Security($Content);
    $this->Condense($Content, $Limit, $Offset);

    return $Content;
  }

  /**
   * Returns a list of all recent scored posts ordered by highest score
   * 
   * @param string $Timespan strtotime compatible time
   * @param int $Limit
   * @param int $Offset
   * @return array
   */
  public function GetRecent($Timespan = 'week', $Limit = NULL, $Offset = 0) {
    $CacheKey = "yaga.best.last.{$Timespan}";
    $Content = Gdn::Cache()->Get($CacheKey);

    if($Content == Gdn_Cache::CACHEOP_FAILURE) {
      $TargetDate = date('Y-m-d H:i:s', strtotime("1 {$Timespan} ago"));

      $SQL = $this->_BaseSQL('Discussion');
      $Discussions = $SQL->Where('d.DateUpdated >', $TargetDate)->Get()->Result(DATASET_TYPE_ARRAY);

      $SQL = $this->_BaseSQL('Comment');
      $Comments = $SQL->Where('c.DateUpdated >', $TargetDate)->Get()->Result(DATASET_TYPE_ARRAY);

      $this->JoinCategory($Comments);

      // Interleave
      $Content = $this->Union('Score', array(
          'Discussion' => $Discussions,
          'Comment' => $Comments
      ));
      $this->Prepare($Content);

      Gdn::Cache()->Store($CacheKey, $Content, array(
          Gdn_Cache::FEATURE_EXPIRY => $this->_Expiry
      ));
    }

    $this->Security($Content);
    $this->Condense($Content, $Limit, $Offset);

    return $Content;
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
    unset($Discussions);

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

  /**
   * Checks the view permission on an item
   * 
   * @param array $ContentItem
   * @return boolean Whether or not the user can see the content item
   */
  protected function SecurityFilter($ContentItem) {
    $CategoryID = GetValue('CategoryID', $ContentItem, NULL);
    if(is_null($CategoryID) || $CategoryID === FALSE) {
      return FALSE;
    }

    $Category = CategoryModel::Categories($CategoryID);
    $CanView = GetValue('PermsDiscussionsView', $Category);
    if(!$CanView) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * Condense an interleaved content list down to the required size
   *
   * @param array $Content
   * @param int $Limit
   * @param int $Offset
   */
  protected function Condense(&$Content, $Limit, $Offset) {
    $Content = array_slice($Content, $Offset, $Limit);
  }

}
