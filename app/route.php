<?php
require_once "app/exception/NotFoundException.php";
require_once "app/validation/Validation.php";
require_once "app/database/Database.php";

use app\exception\NotFoundException;
use app\validation\Validation;
use app\database\Database;

class Route
{
    private $method;

    public function __construct(string $method = "GET")
    {
        $this->method = $method;
    }

    public function __call($method, $arguments)
    {
        if(!method_exists($this, $method)) throw new NotFoundException($method);
    }


    public function request(array $params)
    {
        if(empty($params)) throw new NotFoundException("Resource not found");
        $parts = $params["parts"]??[];
        array_unshift($parts, strtolower($this->method));
        $camelCase = implode('', $parts);
        return $this->{$camelCase}($params["params"], $params["queryParams"]);
    }

    /**
     * Добавить запись
     */
    public function postRecordsAdd($params = array(), $queryParams = array())
    {
        $validation = [
            "name" => "required|string",
            "message" => "required|string",
        ];
        $validated = Validation::make($validation)->validate();
        $db = new Database();
        $recordId = $db->insert("records", $validated);
        return array(
            "id" => $recordId
        );
    }

    /**
     * Добавить коммент к записи
     */
    public function postRecordsRecordsIdCommentsAdd($params = array(), $queryParams = array())
    {
        $validation = [
            "name" => "required|string",
            "message" => "required|string",
        ];
        $validated = Validation::make($validation)->validate();
        $validated["record_id"] = $params["RecordsId"];
        $db = new Database();
        $commentId = $db->insert("comments", $validated);
        return array(
            "id" => $commentId
        );
    }

    /**
     * Получить запись по ID
     */
    public function getRecordsRecordsId($params = array(), $queryParams = array())
    {
        $recordId = $params["RecordsId"];
        $db = new Database();
        $record = $db->selectOne("records", [], ["id" => $recordId]);
        if(!$record) throw new NotFoundException("Record not found");
        return $record;
    }
}