<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

// Only do this once, ever.
if(!$Drop)
  return;

$SQL = Gdn::Database()->SQL();

// Insert stub actions
$SQL->Insert('Action', array(
    'Name' => 'Like',
    'Description' => 'I approve!',
    'Tooltip' => 'This indicates casual approval',
    'CssClass' => 'ReactThumbsUp',
    'AwardValue' => 1
));
$SQL->Insert('Action', array(
    'Name' => 'Dislike',
    'Description' => 'I disapprove!',
    'Tooltip' => 'This indicates casual disapproval',
    'CssClass' => 'ReactThumbsUp2',
    'AwardValue' => -1
));
$SQL->Insert('Action', array(
    'Name' => 'Promote',
    'Description' => 'This post deserves to be featured on the best of page!',
    'Tooltip' => 'Click me if this content should be featured.',
    'CssClass' => 'ReactPointUp',
    'AwardValue' => 5,
    'Permission' => 'Garden.Curation.Manage'
));

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