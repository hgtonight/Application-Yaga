<?php if (!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

// Only do this once, ever.
if (!$Drop)
   return;
   
$SQL = Gdn::Database()->SQL();

// Insert stub actions
$SQL->Insert('Action', array(
   'Name' => 'Thumbs Up',
   'Description' => 'I approve!',
   'Tooltip' => 'This indicates casual approval',
   'AwardValue' => 1
));
$SQL->Insert('Action', array(
   'Name' => 'Thumbs Down',
   'Description' => 'I disapprove!',
   'Tooltip' => 'This indicates casual disapproval',
   'AwardValue' => -1
));