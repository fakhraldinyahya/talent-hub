<?php
require_once 'config.php';

function generate_url($path = '', $query = []) {
    $url = SITE_URL . '/' . $path;
    if (!empty($query)) {
        $url .= '?' . http_build_query($query);
    }
    return $url;
}
class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;
    
    private $conn;
    private $stmt;
    private $error;
    
    public function __construct() {
        // تعيين DSN
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8';
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        );
        
        // إنشاء كائن PDO
        try {
            $this->conn = new PDO($dsn, $this->user, $this->pass, $options);
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            echo 'Connection Error: ' . $this->error;
        }
    }
    
    // تحضير الاستعلام
    public function query($sql) {
        $this->stmt = $this->conn->prepare($sql);
    }
    
    // ربط القيم
    public function bind($param, $value, $type = null) {
        if(is_null($type)) {
            switch(true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }
    
    // تنفيذ الاستعلام
    public function execute() {
        return $this->stmt->execute();
    }
    
    // الحصول على مجموعة من النتائج
    public function resultSet() {
        $this->execute();
        return $this->stmt->fetchAll();
    }
    
    // الحصول على نتيجة واحدة
    public function single() {
        $this->execute();
        return $this->stmt->fetch();
    }
    
    // الحصول على عدد الصفوف
    public function rowCount() {
        return $this->stmt->rowCount();
    }
    
    // الحصول على آخر معرف تم إدراجه
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
}