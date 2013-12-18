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