<?php
/**
Autoload Framework
@author rizkyabdilah
@date July 7, 2011
*/
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

define('APP_PATH', dirname(__FILE__).'/autoload');
define('CORE_PATH', APP_PATH.'/core');
define('LIBR_PATH', APP_PATH.'/libr');

require_once(APP_PATH.'/config.php');
require_once(CORE_PATH.'/Autoload.php');

$AL = new AutoLoad($config);
$AL->run();