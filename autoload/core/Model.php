<?php
class Model{
    static $agregation_parameter = array('sort' => 1, 'orderby' => 1,  'limit' => 1, 'page' => 1);
    static $connection = null;
    public $conn = null;
    public $table = null;
    public $sql = '';
    
    function __construct(){
        if (is_null(self::$connection)){
            self::$connection = new MySQLADO;
        }
        $this->conn = self::$connection;
    }
    
    function utf8($str){
        return $str;
    }
    
    function special_condition(Array &$param, &$extra){
        foreach ($param as $id => $val){
            if (trim($val) == ''){
                unset($param[$id]);
            } elseif (substr($val, 0, 1) == '!'){
                unset($param[$id]);
                $extra[$id] = "`".$id."` NOT IN ('";
                $batch = explode_f(array(',', str_replace(array('(', ')', '!'), '', $val)), 'trim');
                $extra[$id] .= implode("', '", $batch);
                $extra[$id] .= "') ";
            } elseif (substr($val, 0, 1) == '('){
                unset($param[$id]);
                $extra[$id] = "`".$id."` IN ('";
                $batch = explode_f(array(',', str_replace(array('(', ')', '!'), '', $val)), 'trim');
                $extra[$id] .= implode("', '", $batch);
                $extra[$id] .= "') ";
            } elseif (substr($val, 0, 6) == 'range('){
                unset($param[$id]);
                $batch = str_replace(array('range(', ')'), '', $val);
                $batch = explode(',', $batch);
                $extra[$id] = '';
                if ($batch[0] != '-')
                    $extra[$id] .= "`".$id."` >= '".$batch[0]."'";
                if ($batch[0] != '-' && $batch[1] != '-')
                    $extra[$id] .= ' AND ';
                if ($batch[1] != '-')
                    $extra[$id] .= "`".$id."` <= '".$batch[1]."'";
            }
        }
    }
    
    function save($data){
//         if ()
        $sql = 'INSERT INTO `'.$this->table.'` ('.implode(', ', array_keys($data)).', create_at) VALUES (\''.implode('\', \'', $data).'\', LOCALTIMESTAMP)';
        mysql_query($sql);
        $out = array();
        $out['success'] = mysql_affected_rows() == -1 ? 0 : 1;
        if (!$out['success']){
            $out['error']['message'] = mysql_error();
        } else {
            $out['article_id'] = mysql_insert_id();
        }
        return $out;
    }
    
    function update($article_id, $set = array()){
        $set_to = array();
        foreach ($set as $id => $val){
            $set_to[] = $id.'=\''.$val.'\'';
        }
        $sql = 'UPDATE `'.$this->table.'` SET '.implode(', ', $set_to).', update_at = LOCALTIMESTAMP WHERE article_id = \''.$article_id.'\'';
        mysql_query($sql);
        $out = array();
        $out['success'] = mysql_affected_rows() == -1 ? 0 : 1;
        if (!$out['success']){
            $out['error']['message'] = mysql_error();
        }
        return $out;
    }
    
    function get_all($condition, $fields_to_show = array(), $aggregation = array()){
        $prefix_data = 'data';
        $this->special_condition($condition, $extra);
        $where = array();
        foreach ($condition as $id => $val){
            $where[] = $id.'=\''.$val.'\'';
        }
        if (count($extra)){
            foreach ($extra as $id => $val){
                $where[$id] = $val;
            }
        }
        $where = count($where) ? ' WHERE '.implode(' AND ', $where) : '';
        
        $fields = '*';
        if (count($fields_to_show)){
            $fields = implode(',', $fields_to_show);
        }
        
        if (isset($aggregation['limit']) && (int) $aggregation['limit'] > 0)
            $limit = (int) $aggregation['limit'];
        elseif (isset($aggregation['limit']) && (int) $aggregation['limit'] < 0)
            $limit = 0;
        else
            $limit = 10;

        $skip = (isset($aggregation['page']) && (int) $aggregation['page'] > 0) ? (int) $limit * ($aggregation['page'] - 1) : 0;
        $orderby = '';
        if  (array_key_exists('orderby', $aggregation) && in_array($aggregation['orderby'], array_keys(self::$schema))){
            $orderby = ' ORDER BY '.$aggregation['orderby'].' '.(strtolower($aggregation['sort']) == 'desc' ? 'DESC' : 'ASC');
        }
        $cond = array('fields' => $fields, 'table' => $this->table, 'where' => $where, 'orderby' => $orderby, 'skip' => $skip, 'limit' => $limit);
        $this->sql = $this->create_sql($cond);
        $result = $this->conn->query($this->sql);
        $result_count = $this->conn->query('SELECT FOUND_ROWS() AS alldata');
        $response[$prefix_data] = array();
        while($row = mysql_fetch_assoc($result)){
            $response[$prefix_data][] = $row;
        }
        $response['info']['total'] = count($response[$prefix_data]);
        while ($row = mysql_fetch_assoc($result_count)){
            $response['info']['alldata'] = (int) $row['alldata'];
        }
        
        $response['info']['condition'] = $condition;
        $response['info']['sql'] = $this->sql;
        $response['info']['limit'] = $limit;
        $response['info']['page'] = (isset($aggregation['page']) && (int) $aggregation['page'] > 0) ? (int) $aggregation['page'] : 1;
        $response['info']['allpage'] = ceil($response['info']['alldata'] / ($limit > 0 ? $limit : 1));
        return $response;
    }
    
    // override this function when you need to join table
    function create_sql($cond){
        return 'SELECT SQL_CALC_FOUND_ROWS '.$cond['fields'].' FROM `'.$cond['table'].'` '.$cond['where'].$cond['orderby'].' LIMIT '.$cond['skip'].', '.$cond['limit'];
    }
}
