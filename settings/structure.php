<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

if(!isset($Drop)) {
  $Drop = FALSE; // Safe default - Set to TRUE to drop the table if it already exists.
}

if(!isset($Explicit)) {
  $Explicit = FALSE; // Safe default - Set to TRUE to remove all other columns from table.
}

$Database = Gdn::Database();
//$SQL = $Database->SQL(); // To run queries.
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
        ->Column('Description', 'varchar(255)')
        ->Column('Tooltip', 'varchar(255)')
        ->Column('Rule', 'int')
        ->Column('RuleQuantity', 'int')
        ->Column('AwardValue', 'int')
        ->Set($Explicit, $Drop);
