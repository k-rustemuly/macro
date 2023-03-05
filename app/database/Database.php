<?php 
namespace app\database;

use PDO;

class Database
{
    private $host = 'database';
    private $db   = 'macro';
    private $user = 'macro_user';
    private $pass = 'macro_password';
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

    /**
     * Выборка всей записи
     */
    public function selectAll(string $table_name = "", array $fields = [], array $search = [])
    {
        $stmt = $this->select($table_name, $fields, $search);
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }

    public function getRecordsWithCommentsByPage($page = 1)
    {
        $offset = $page*15-15;
        $sql = "SELECT r.*, c.id as comment_id, c.name as comment_name, c.message as comment_message FROM records r LEFT JOIN ( SELECT *, ROW_NUMBER() OVER (PARTITION BY record_id ORDER BY id DESC) AS row_num FROM comments ) c ON r.id = c.record_id AND c.row_num <= 3 WHERE r.id IN (SELECT * FROM (SELECT id FROM records WHERE 1 ORDER BY id DESC LIMIT 15 OFFSET $offset) as s); ";
        $stm = $this->pdo->prepare($sql);
        $stm->execute();
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }
}