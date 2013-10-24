<?php if(!defined('APPLICATION')) exit();
// Use this file to do any database changes for your application.

if(!isset($Drop))
  $Drop = FALSE; // Safe default - Set to TRUE to drop the table if it already exists.

if(!isset($Explicit))
  $Explicit = FALSE; // Safe default - Set to TRUE to remove all other columns from table.

$Database = Gdn::Database();
$SQL = $Database->SQL(); // To run queries.
$Construct = $Database->Structure(); // To modify and add database tables.
$Validation = new Gdn_Validation(); // To validate permissions (if necessary).


