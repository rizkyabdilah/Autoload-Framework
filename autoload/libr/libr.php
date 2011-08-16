<?php
function site_data($key){
    global $config;
    return $config['site'][$key];
}

function static_url($static_file){
    return site_data('domain').'/static'.$static_file;
}

function static_content_url($static_file){
    return site_data('domain').'/static'.$static_file;
}

function explode_f($data, $callback){
    $out = explode($data[0], $data[1]);
    foreach($out as $id => $val){
        $out[$id] = $callback(strtolower($val));
    }
    return $out;
}

function debug($var){
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}

