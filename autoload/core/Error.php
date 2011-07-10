<?php
if (!defined('APP_PATH')) die('403 Forbidden');

class Error{
    static $http_code = array(404 => 'Not Found');
    function raise_error($code, $custom_message = null){
        $message = self::$http_code[$code];
        if ($custom_message !== null){
            $message = $custom_message;
        }
        header('Status : '.$code.' '.self::$http_code[$code]);
        print '<h2>'.$code.' '.$message.'</h2>';
        exit;
    }
    
    function log_error($message = ''){
        global $config;
        if ($config['error_log'] == 1){
//             die('echo "'.$message.'" >> '.$config['log_file']);
            system('echo "'.date('Y/m/d H:i:s -- ').stripslashes($message).'" >> '.$config['log_file']);
        }
    }
}

