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
//$Validation = new Gdn_Validation(); // To validate permissions (if necessary).

$Construct->Table('Reaction')
        ->PrimaryKey('ReactionID')
        ->Column('InsertUserID', 'int', FALSE, 'index.1')
        ->Column('ActionID', 'int', FALSE, 'index')
        ->Column('ParentID', 'int', TRUE)
        ->Column('ParentType', array('discussion', 'comment', 'activity'), TRUE)
        ->Column('ParentAuthorID', 'int', FALSE)
        ->Column('DateInserted', 'datetime')
        ->Set($Explicit, $Drop);

$Construct->Table('Action')
        ->PrimaryKey('ActionID')
        ->Column('Name', 'varchar(140)')
        ->Column('Description', 'varchar(255)')
        ->Column('Tooltip', 'varchar(255)')
        ->Column('CssClass', 'varchar(255)')
        ->Column('AwardValue', 'int', 1)
        ->Set($Explicit, $Drop);

$Construct->Table('Badge')
        ->PrimaryKey('BadgeID')
        ->Column('Name', 'varchar(140)')
        ->Column('Description', 'varchar(255)', NULL)
        ->Column('Photo', 'varchar(255)', NULL)
        ->Column('RuleClass', 'varchar(255)')
        ->Column('RuleCriteria', 'text', TRUE)
        ->Column('AwardValue', 'int', 0)
        ->Column('Enabled', 'tinyint(1)', '1')
        ->Set($Explicit, $Drop);

$Construct->Table('BadgeAward')
        ->PrimaryKey('BadgeAwardID')
        ->Column('BadgeID', 'int')
        ->Column('UserID', 'int')
        ->Column('DateInserted', 'datetime')
        ->Set($Explicit, $Drop);

if ($SQL->GetWhere('ActivityType', array('Name' => 'BadgeAward'))->NumRows() == 0)
   $SQL->Insert('ActivityType', array('AllowComments' => '1', 'Name' => 'BadgeAward', 'FullHeadline' => '%1$s earned the %6$s badge.', 'ProfileHeadline' => '%1$s earned the %6$s badge.'));
if ($SQL->GetWhere('ActivityType', array('Name' => 'RankPromotion'))->NumRows() == 0)
   $SQL->Insert('ActivityType', array('AllowComments' => '1', 'Name' => 'RankPromotion', 'FullHeadline' => '%1$s was promoted to %6$s.', 'ProfileHeadline' => '%1$s was promoted to %6$s.'));
