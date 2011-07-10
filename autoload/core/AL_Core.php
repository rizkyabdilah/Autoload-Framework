<?php
if (!defined('APP_PATH')) die('403 Forbidden');

class AL_Core{    
    public $AL = null;
    function __call($method, $args){
        if ($this->AL->_config['mode'] === 0){
            print 'method : '. $method .' tidak ada';
//             $this->AL->_instance['Error']->raise_error(404, 'Method not exists');
        } else {
            $this->index();      
        }
    }
    
    function __construct(){
        global $AL;
        $this->AL = $AL;
    }
    
    function index(){
        echo 'index nya core';
    }
    
    function view($vn, $data = array()){
        ob_start();
        include_once(APP_PATH.'/view/'.$vn.'.php');
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }
}
