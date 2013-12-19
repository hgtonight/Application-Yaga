<?php if (!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

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
   * Returns a list of all available actions
   */
  public function Get($UserID, $ActionID, $Limit = FALSE) {
    $Expiry = 600;
    
    // Check cache
    $CacheKey = "yaga.profile.reactions.{$ActionID}";
    $Content = Gdn::Cache()->Get($CacheKey);

    if($Content == Gdn_Cache::CACHEOP_FAILURE) {

      // Get matching Discussions
      $Discussions = Gdn::SQL()->Select('d.*')
                      ->From('Discussion d')
                      ->Join('Reaction r', 'd.DiscussionID = r.ParentID')
                      ->Where('d.InsertUserID', $UserID)
                      ->Where('r.ActionID', $ActionID)
                      ->OrderBy('r.DateInserted', 'DESC')
                      ->Get()->Result(DATASET_TYPE_ARRAY);

      // Get matching Comments
      $Comments = Gdn::SQL()->Select('c.*')
                      ->From('Comment c')
                      ->Join('Reaction r', 'c.CommentID = r.ParentID')
                      ->Where('c.InsertUserID', $UserID)
                      ->Where('r.ActionID', $ActionID)
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
          Gdn_Cache::FEATURE_EXPIRY => $Expiry
      ));
    }

    $this->Security($Content);
    $this->Condense($Content, $Limit);

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

}