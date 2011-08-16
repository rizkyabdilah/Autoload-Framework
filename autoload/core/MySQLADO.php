<?php
ini_set('display_errors', 'on');
if ($_SERVER['SCRIPT_FILENAME'] === __FILE__) die('Forbidden!');

class MySQLADO{
    // pake variabel static biar koneksinya bisa di cache
    // jadi nggak perlu koneksi ke mongo berkali-kali
    private $host;
    private $user;
    private $password;
    private $db;
    private static $connection = null;
    
    function __construct($args = array()){
        // kalo belum ada koneksi baru bikin yang baru
        if (self::$connection === null){
            global $config;
            if (!count($args)){
                $this->host = $config['mysql_host'];
                $this->user = $config['mysql_user'];
                $this->password = $config['mysql_password'];
                $this->db = $config['mysql_db'];
            } else {
                $this->host = $args[0];
                $this->user = $args[1];
                $this->password = $args[2];
                $this->db = $args[3];
            }

            if (!self::$connection = mysql_connect($this->host, $this->user, $this->password)){
                die('Koneksi ke database gagal: '.mysql_error()); exit;
            }
            mysql_select_db($this->db, self::$connection);
        }
    }
    
    public function query($sql){
        $result = mysql_query($sql, self::$connection);
        return $result;
    }
}
?>