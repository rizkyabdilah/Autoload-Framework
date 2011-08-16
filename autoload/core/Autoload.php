<?php
if (!defined('APP_PATH')) die('403 Forbidden');

class AutoLoad{
    protected $is_cli = false;
    public $_var = array();
    public $_config =  array();
    public $_instance = array();
    public $_uri = null;
    
    
    function __construct($config){
        $this->_config = $config;
        if (array_key_exists('argv', $_SERVER)){
            $this->is_cli = false; #true;
        }
        
        if ($this->_config['error_log'] == 1){
            ini_set('display_errors', 1);
            error_reporting(E_ALL ^ E_NOTICE);
        }
        
        foreach ($this->_config['autoload_file'] as $id => $val){
            include_once($val);
        }
        foreach ($this->_config['autoload_class'] as $id => $val){
            $this->_instance[$id] = $this->load_class($val.$id);
        }
        unset($this->_config['autoload_file'], $this->_config['autoload_class']);

        return $this;
    }
    
    function run(){
        $this->parse_uri_string()->process_request();
    }
    
    function parse_uri_string(){
        if ($this->is_cli){
            $uri = '/'.implode('/', array_splice($_SERVER['argv'], 1));
        } elseif (($req_uri = str_replace(site_data('relative_path'), '', $_SERVER['REQUEST_URI'])) != '/') {
            $uri = $req_uri;
        } else {
            $uri = '/'.$this->_config['default_controller'];
        }
        $this->_uri = $uri;
        $routes = require_once(APP_PATH.'/routes.php');
        $_SERVER['QUERY_STRING'] = preg_replace(array_keys($routes), $routes, $this->_uri).'&'.$_SERVER['QUERY_STRING'];
        parse_str($_SERVER['QUERY_STRING'], $this->_var);
        return $this;
    }
    
    function process_request(){
        $controller = $this->_config['default_controller'];
        if (isset($this->_var['ac']) && strlen($this->_var['ac'])){
            $controller = ucfirst($this->_var['ac']);
        }

        $function = 'index';
        if (isset($this->_var['af']) && strlen($this->_var['af'])/* && method_exists($this->_instance[$controller], $this->_var[2])*/){
            $function = $this->_var['af'];
        }
        // create new _instance
        $RC = $this->load_class('controller/'.$controller, $this);
        $RC->$function();
        return $this;
    }
    
    function load_class($req, $args = null){
        $file_path = APP_PATH.'/'.$req.'.php';
        $batch = explode('/', $req);
        $cn = end($batch);

        if (isset($this->_instance[$cn]) && $this->_instance[$cn] instanceof $cn){
            return $this->_instance[$cn];
        } elseif (file_exists($file_path)){
            include_once(APP_PATH.'/'.$req.'.php');
            if ($args !== null)
                $this->_instance[$cn] = new $cn($args);
            else
                $this->_instance[$cn] = new $cn;
            return $this->_instance[$cn];
        } elseif ($this->_config['mode'] === 0) {
            $this->_instance['Error']->raise_error(404, $file_path.' file not exists');
        } else {
            return $this->load_class('controller/'.$this->_config['default_controller']);
        }
    }
}

