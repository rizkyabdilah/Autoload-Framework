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
$config['site']['domain'] = #'http://localhost';
$config['site']['title'] = 'Site Title';

// auto include file
$config['autoload_file'] = array();
$config['autoload_file'][] = CORE_PATH.'/AL_Core.php';
$config['autoload_file'][] = CORE_PATH.'/Error.php';

// auto instantiate class
// key = class name , value = relative path
$config['autoload_class'] = array();
$config['autoload_class']['Error'] = 'core/';


function site_data($key){
    global $config;
    return $config['site'][$key];
}

function static_url($static_file){
    return site_data('domain').'/static'.$static_file;
}

function create_link($uri = ''){
    return site_data('domain').$uri;
}
