<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013-2016 Zachary Doll */

$SQL = Gdn::Database()->SQL();

// Only insert stub content if nothing exists
$Row = $SQL->get('Action', '', 'asc', 1)->firstRow(DATASET_TYPE_ARRAY);
if (!$Row) {
  $SQL->Insert('Action', array(
      'ActionID' => 1,
      'Name' => 'Promote',
      'Description' => 'This post deserves to be featured on the best of page!',
      'Tooltip' => 'Click me if this content should be featured.',
      'CssClass' => 'ReactPointUp',
      'AwardValue' => 5,
      'Permission' => 'Garden.Curation.Manage',
      'Sort' => 0
  ));

  $SQL->Insert('Action', array(
      'ActionID' => 2,
      'Name' => 'Insightful',
      'Description' => 'This post brings new meaning to the discussion.',
      'Tooltip' => 'Insightful',
      'CssClass' => 'ReactEye2',
      'AwardValue' => 1,
      'Permission' => 'Yaga.Reactions.Add',
      'Sort' => 1
  ));

  $SQL->Insert('Action', array(
      'ActionID' => 3,
      'Name' => 'Awesome',
      'Description' => 'This post is made of pure win.',
      'Tooltip' => 'Awesome',
      'CssClass' => 'ReactHeart',
      'AwardValue' => 1,
      'Permission' => 'Yaga.Reactions.Add',
      'Sort' => 2
  ));

  $SQL->Insert('Action', array(
      'ActionID' => 4,
      'Name' => 'LOL',
      'Description' => 'This post is funny.',
      'Tooltip' => 'LOL',
      'CssClass' => 'ReactWink',
      'AwardValue' => 1,
      'Permission' => 'Yaga.Reactions.Add',
      'Sort' => 3
  ));

  $SQL->Insert('Action', array(
      'ActionID' => 5,
      'Name' => 'WTF',
      'Description' => 'This post is all sorts of shocking.',
      'Tooltip' => 'WTF',
      'CssClass' => 'ReactShocked',
      'AwardValue' => 1,
      'Permission' => 'Yaga.Reactions.Add',
      'Sort' => 4
  ));

  $SQL->Insert('Action', array(
      'ActionID' => 6,
      'Name' => 'Spam',
      'Description' => 'This post is spam.',
      'Tooltip' => 'Spam',
      'CssClass' => 'ReactWarning',
      'AwardValue' => -5,
      'Permission' => 'Garden.Curation.Manage',
      'Sort' => 5
  ));    
}

// Insert stub badge
$SQL->Insert('Badge', array(
    'Name' => 'What did I just do?',
    'Description' => 'You installed Yaga! Feel free to edit or delete this badge by clicking the buttons on the right.',
    'Photo' => 'applications/yaga/design/images/default_badge.png',
    'RuleClass' => 'ManualAward',
    'RuleCriteria' => 'a:0:{}',
    'AwardValue' => 1,
    'Enabled' => 0
));

// Insert stub ranks
$SQL->Insert('Rank', array(
    'Name' => 'Entry Level',
    'Description' => 'You are in the minor leagues. I suggest you work on your content to progress.',
    'PointReq' => 1,
    'Sort' => 1,
    'Enabled' => 0
));
$SQL->Insert('Rank', array(
    'Name' => 'Big Time',
    'Description' => 'You have hit the big time! Keep up the good work.',
    'PointReq' => 100,
    'Sort' => 2,
    'Enabled' => 0
));