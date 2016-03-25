<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

if(!isset($Drop)) {
  $Drop = FALSE; // Safe default - Set to TRUE to drop the table if it already exists.
}

if(!isset($Explicit)) {
  $Explicit = FALSE; // Safe default - Set to TRUE to remove all other columns from table.
}

$Database = Gdn::Database();
$SQL = $Database->SQL(); // To run queries.
$Construct = $Database->Structure(); // To modify and add database tables.
$Px = $Database->DatabasePrefix;

// Tracks the data associated with reacting to content
$Construct->Table('Reaction')
        ->PrimaryKey('ReactionID')
        ->Column('InsertUserID', 'int', FALSE, 'index.1')
        ->Column('ActionID', 'int', FALSE, 'index')
        ->Column('ParentID', 'int', TRUE)
        ->Column('ParentType', 'varchar(100)')
        ->Column('ParentAuthorID', 'int', FALSE)
        ->Column('DateInserted', 'datetime')
        ->Set($Explicit, $Drop);

$result = $SQL->query("SHOW INDEX FROM ${Px}Reaction WHERE Key_name = 'IX_ParentID_ParentType'")->result(); 
if(!$result && !$Construct->CaptureOnly) {
  $SQL->query("ALTER TABLE ${Px}Reaction ADD INDEX IX_ParentID_ParentType (ParentID, ParentType)");
}

// Describes actions that can be taken on a comment, discussion or activity
$Construct->Table('Action')
        ->PrimaryKey('ActionID')
        ->Column('Name', 'varchar(140)')
        ->Column('Description', 'varchar(255)')
        ->Column('Tooltip', 'varchar(255)')
        ->Column('CssClass', 'varchar(255)')
        ->Column('AwardValue', 'int', 1)
        ->Column('Permission', 'varchar(255)', 'Yaga.Reactions.Add')
        ->Column('Sort', 'int', TRUE)
        ->Set($Explicit, $Drop);

// Describes a badge and the associated rule criteria
$Construct->Table('Badge')
        ->PrimaryKey('BadgeID')
        ->Column('Name', 'varchar(140)')
        ->Column('Description', 'varchar(255)', NULL)
        ->Column('Photo', 'varchar(255)', NULL)
        ->Column('RuleClass', 'varchar(255)')
        ->Column('RuleCriteria', 'text', TRUE)
        ->Column('AwardValue', 'int', 0)
        ->Column('Enabled', 'tinyint(1)', '1')
        ->Column('Sort', 'int', TRUE)
        ->Set($Explicit, $Drop);

// Tracks the actual awarding of badges
$Construct->Table('BadgeAward')
        ->PrimaryKey('BadgeAwardID')
        ->Column('BadgeID', 'int')
        ->Column('UserID', 'int')
        ->Column('InsertUserID', 'int', NULL)
        ->Column('Reason', 'text', NULL)
        ->Column('DateInserted', 'datetime')
        ->Set($Explicit, $Drop);

// Describes a rank and associated values
$Construct->Table('Rank')
        ->PrimaryKey('RankID')
        ->Column('Name', 'varchar(140)')
        ->Column('Description', 'varchar(255)', NULL)
        ->Column('Sort', 'int', TRUE)
        ->Column('PointReq', 'int', 0)
        ->Column('PostReq', 'int', 0)
        ->Column('AgeReq', 'int', 0)
        ->Column('Perks', 'text', TRUE)
        ->Column('Enabled', 'tinyint(1)', '1')
        ->Set($Explicit, $Drop);

// Tracks the current rank a user has
$Construct->Table('User')
        ->Column('CountBadges', 'int', 0)
        ->Column('RankID', 'int', TRUE)
        ->Column('RankProgression', 'tinyint(1)', '1')
        ->Set();

// Add activity types for Badge and Rank awards
if ($SQL->GetWhere('ActivityType', array('Name' => 'BadgeAward'))->NumRows() == 0)
   $SQL->Insert('ActivityType', array('AllowComments' => '1', 'Name' => 'BadgeAward', 'FullHeadline' => '%1$s earned a badge.', 'ProfileHeadline' => '%1$s earned a badge.', 'Notify' => 1));
if ($SQL->GetWhere('ActivityType', array('Name' => 'RankPromotion'))->NumRows() == 0)
   $SQL->Insert('ActivityType', array('AllowComments' => '1', 'Name' => 'RankPromotion', 'FullHeadline' => '%1$s was promoted.', 'ProfileHeadline' => '%1$s was promoted.', 'Notify' => 1));
