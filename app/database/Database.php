<?php 
namespace app\database;

use PDO;

class Database
{
    private $host = '127.0.0.1';
    private $db   = 'macro';
    private $user = 'root';
    private $pass = '';
    private $charset = 'utf8';
    private $pdo;

    public function __construct()
    {
        $dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $this->pdo = new PDO($dsn, $this->user, $this->pass, $opt);
    }

    private function clearData(array $data)
    {
        $set = '';
        foreach($data as $field)
        {
            $set.="`".str_replace("`","``",$field)."`". "=:$field, ";
        }
        return substr($set, 0, -2); 
    }

    public function insert(string $table_name = "", array $data = [])
    {
        $sql = "INSERT INTO $table_name SET ".$this->clearData(array_keys($data));
        $stm = $this->pdo->prepare($sql);
        $stm->execute($data);
        return $this->pdo->lastInsertId();
        
    }
}