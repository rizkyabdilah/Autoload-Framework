<?php
if (!defined('APP_PATH')) die('403 Forbidden');

class AL_Core{    

    function __call($method, $args){
        global $AL;
        if ($AL->_config['mode'] === 0){
            print 'method : '. $method .' tidak ada';
//             $this->AL->_instance['Error']->raise_error(404, 'Method not exists');
        } else {
            $this->index();      
        }
    }
    
    function index(){
        echo 'index nya core';
    }
    
    function view($vn, $data = array()){
        global $AL;
        ob_start();
        include_once(APP_PATH.'/view/'.$vn.'.php');
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }
    
    function model($cp){
        global $AL;
        return $AL->load_class($cp);
    }
}
