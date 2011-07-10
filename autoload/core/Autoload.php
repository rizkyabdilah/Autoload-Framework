<?php
if (!defined('APP_PATH')) die('403 Forbidden');

class AutoLoad{
    protected $is_cli = false;
    public $_var = array();
    public $_config =  array();
    public $_instance = array();
    
    
    function __construct($config){
        $this->_config = $config;
        if (array_key_exists('argv', $_SERVER)){
            $this->is_cli = True;
        }
        
        if ($this->_config['error_log'] == 1){
            ini_set('display_errors', 1);
            error_reporting(E_ALL);
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
        } elseif ($_SERVER['REQUEST_URI'] != '/') {
            $uri = $_SERVER['REQUEST_URI'];
        } else {
            $uri = '/'.$this->_config['default_controller'];
        }
        $this->_var = explode('/', $uri);
        return $this;
    }
    
    function process_request(){
        $controller = ucfirst($this->_var[1]);
        // create new _instance
//         $this->_instance[$controller] = $this->load_class('controller/'.$controller, $this);
        $function = 'index';
        if (isset($this->_var[2]) && strlen($this->_var[2])/* && method_exists($this->_instance[$controller], $this->_var[2])*/){
            $function = $this->_var[2];
        }
        $RC = $this->load_class('controller/'.$controller, $this);
        $RC->$function();
//         $this->_instance[$controller]->$function();
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
                return new $cn($args);
            else
                return new $cn;
        } elseif ($this->_config['mode'] === 0) {
            $this->_instance['Error']->raise_error(404, $file_path.' file not exists');
        } else {
            return $this->load_class('controller/'.$this->_config['default_controller']);
        }
    }
}

