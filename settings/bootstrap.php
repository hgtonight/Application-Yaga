<?php if (!defined('APPLICATION')) exit();
// Load up the rules
$Map     = Gdn_Autoloader::MAP_LIBRARY;
$Context = Gdn_Autoloader::CONTEXT_APPLICATION;
$Path    = PATH_APPLICATIONS . DS . 'yaga' . DS . 'rules';
$Options = array();

// Set the map options
$Options['Extension'] = 'yaga';

Gdn_Autoloader::RegisterMap($Map, $Context, $Path, $Options);
