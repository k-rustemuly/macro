<?php
require_once "app/exception/NotFoundException.php";
require_once "app/validation/Validation.php";
use app\exception\NotFoundException;
use app\validation\Validation;

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
        $this->{$camelCase}($params["params"], $params["queryParams"]);
        return "aaa";
    }

    /**
     * Добавить запись
     */
    public function postRecordsAdd($params = array(), $queryParams = array(), $postParams = array())
    {
        $validation = [
            "name" => "required|string",
            "message" => "required|string",
        ];
        $validated = Validation::make($validation)->validate();
        var_dump($validated);
    }
}