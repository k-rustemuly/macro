<?php 
namespace app\validation;

require_once "app/exception/ValidationException.php";

use app\exception\ValidationException;

class Validation
{
    private $settings;

    public static function make(array $settings)
    {
        $validation = new self;
        $validation->settings = $settings;
        return $validation;
    }

    public function validate()
    {
        $validated = array();
        $data = $this->getFilteredData();
        foreach($this->settings as $key => $rules)
        {
            $rules = explode('|', $rules);
            foreach($rules as $rule)
            {
                switch($rule)
                {
                    case "required":
                        if(!array_key_exists($key, $data)) throw new ValidationException($key." is", $rule);
                    break;
                    case "string":
                        if(!is_string($data[$key])) throw new ValidationException($key." is not ".$rule);
                    break;
                }
                $validated[$key] = $data[$key];
            }
        }
        return $validated;
    }

    /**
     * Фильтрация данных с пост запросов.
     * Берем только те данные, которые указаны в настройках валидации.
     * Принимаем с form-data или raw
     * 
     * @return array<mixed> $filteredData
     */
    private function getFilteredData()
    {
        $raw = file_get_contents("php://input");
        if($raw && $data = json_decode($raw, true))
        {
            $data = array_intersect_key($data, $this->settings);
        }
        else
        {
            $data = array_intersect_key($_POST, $this->settings);
        }
        return $data;
    }
}