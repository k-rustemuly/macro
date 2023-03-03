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

    /**
     * Подключение к бд через pdo
     */
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

    /**
     *  Подготовка данных
     */
    private function clearData(array $data)
    {
        $set = '';
        foreach($data as $field)
        {
            $set.="`".str_replace("`","``",$field)."`". "=:$field, ";
        }
        return substr($set, 0, -2); 
    }

    /**
     * Данные из бд
     */
    private function select(string $table_name = "", array $fields = [], array $search = [])
    {
        $fields = implode(',', $fields);
        if(empty($fields)) $fields = "*";
        $sql = "SELECT $fields FROM $table_name WHERE ".$this->clearData(array_keys($search));
        $stm = $this->pdo->prepare($sql);
        $stm->execute($search);
        return $stm;
    }

    /**
     * Добавить новую запись
     */
    public function insert(string $table_name = "", array $data = [])
    {
        $sql = "INSERT INTO $table_name SET ".$this->clearData(array_keys($data));
        $stm = $this->pdo->prepare($sql);
        $stm->execute($data);
        return $this->pdo->lastInsertId();
        
    }

    /**
     * Выборка одной записи
     */
    public function selectOne(string $table_name = "", array $fields = [], array $search = [])
    {
        $stmt = $this->select($table_name, $fields, $search);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }
}