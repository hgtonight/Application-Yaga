<?php if (!defined('APPLICATION')) exit();

// Register Yaga library classes and interfaces in the autoloader
$Map     = Gdn_Autoloader::MAP_LIBRARY;
$Context = Gdn_Autoloader::CONTEXT_APPLICATION;
$Path    = PATH_APPLICATIONS . DS . 'yaga' . DS . 'library';
$Options = array('Extension' => 'yaga');

Gdn_Autoloader::RegisterMap($Map, $Context, $Path, $Options);

require_once(PATH_APPLICATIONS . DS . 'yaga' . DS . 'library' . DS . 'functions.render.php');
