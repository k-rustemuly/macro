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
        if(!method_exists($this, $method)) throw new NotFoundException();
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
}