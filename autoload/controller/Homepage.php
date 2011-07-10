<?php
class Homepage extends AL_Core{
    
    function index(){
        $out = $this->view('homepage');
        print $out;
    }
}