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

    /**
     * Получить список записей и последние 3 комментария к каждой записи. В выводе должно быть не больше 15 объектов. Предусмотреть пагинацию.
     */
    public function getRecords($params = array(), $queryParams = array())
    {
        $page = isset($queryParams["page"]) && $queryParams["page"]>0 ? $queryParams["page"] : 1;
        $db = new Database();
        $rows = $db->getRecordsWithCommentsByPage($page);
        $records = array();
        foreach($rows as $row)
        {
            $record_id = $row["id"];
            $comment_id = $row['comment_id'];
             // Если это новый пост, создаем новый объект поста
            if (!isset($records[$record_id])) {
                $records[$record_id] = array(
                    "id" => $row["id"],
                    "name" => $row["name"],
                    "message" => $row["message"],
                    "comments" => array()
                );
            }

            // Если это новый комментарий, создаем новый объект комментария
            if (!empty($comment_id)) {
                $comment = array(
                    "id" => $row["comment_id"],
                    "name" => $row["comment_name"],
                    "message" => $row["comment_message"]
                );
                
                // Добавляем комментарий в массив комментариев поста
                $records[$record_id]["comments"][] = $comment;
            }
        }
        return array_values($records);
    }

    /**
     * Получить список комментариев к записи
     */
    public function getRecordsRecordsIdComments($params = array(), $queryParams = array())
    {
        $recordId = $params["RecordsId"];
        $db = new Database();
        $comments = $db->selectAll("comments", ["id", "name", "message"], ["record_id" => $recordId]);
        if(!$comments) throw new NotFoundException("Record not found");
        return $comments;
    }
}