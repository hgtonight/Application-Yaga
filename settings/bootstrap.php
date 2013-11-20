<?php if (!defined('APPLICATION')) exit();
// Load up the rules
$Map     = Gdn_Autoloader::MAP_LIBRARY;
$Context = Gdn_Autoloader::CONTEXT_APPLICATION;
$Path    = PATH_APPLICATIONS . DS . 'yaga' . DS . 'rules';
$Options = array('Extension' => 'yaga', 'ClassFilter' => '*');

Gdn_Autoloader::RegisterMap($Map, $Context, $Path, $Options);

require_once(PATH_APPLICATIONS . DS . 'yaga' . DS . 'lib' . DS . 'class.yaga.php');
require_once(PATH_APPLICATIONS . DS . 'yaga' . DS . 'lib' . DS . 'functions.render.php');