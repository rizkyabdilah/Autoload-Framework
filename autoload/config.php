<?php
if (!defined('APP_PATH')) die('403 Forbidden');

$config = array();
$config['default_controller'] = 'Homepage';
$config['log_file'] = APP_PATH.'/log/error.log';

// mode ? 0 -> development , 1 -> production
$config['mode'] = 0;

// error_log ? 0 -> none , 1 -> debug
$config['error_log'] = 1;

//extra config
$config['site'] = array();
$config['site']['domain'] = 'http://localhost';
$config['site']['relative_path'] = '';
$config['site']['title'] = 'Autoload Framework';

// auto include file
$config['autoload_file'] = array();
$config['autoload_file'][] = CORE_PATH.'/AL_Core.php';
$config['autoload_file'][] = CORE_PATH.'/Error.php';
$config['autoload_file'][] = CORE_PATH.'/Model.php';
$config['autoload_file'][] = CORE_PATH.'/MySQLADO.php';
$config['autoload_file'][] = LIBR_PATH.'/libr.php';

// auto instantiate class
// key = class name , value = relative path
$config['autoload_class'] = array();
$config['autoload_class']['Error'] = 'core/';

//config mysql
$config['mysql_host'] = 'localhost';
$config['mysql_user'] = 'root';
$config['mysql_password'] = '';
$config['mysql_db'] = 'db';

//app extra config