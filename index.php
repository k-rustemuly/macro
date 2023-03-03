<?php
require 'app/exception/NotFoundException.php';
require "app/exception/ValidationException.php";

use app\exception\NotFoundException;
use app\exception\ValidationException;

$requestMethod = $_SERVER["REQUEST_METHOD"];
$uri = getUri();
$eUri = explodeUri($uri);

function getUri() :string
{
    $mainPath = str_replace("index.php", '', $_SERVER["PHP_SELF"]);
    $path = str_replace($mainPath, '', $_SERVER["REQUEST_URI"]);
    return $path;
}

function explodeUri(string $uri = null)
{
    if(!$uri) return [];
    $parsedUrl =  parse_url($uri);
    $query = $parsedUrl["query"]??[];
    $paths = explode('/', $parsedUrl["path"]);
    $uriParts = [];
    $uriParams = [];
    foreach($paths as $path)
    {
        if(is_numeric($path))
        {
            $uriParamName = "Id";
            if($previousPart = end($uriParts))
            {
                $uriParamName = $previousPart.$uriParamName;
            }
            $uriParams[$uriParamName] = $path;
            $path = $uriParamName;
        }
        $uriParts[] = ucfirst($path);
    }
    $queryParams = [];
    if($query){
        parse_str($query, $queryParams);
    }
    return array("parts" => $uriParts, "params" => $uriParams, "queryParams" => $queryParams);
}

require_once __DIR__.'/app/route.php';

$route = new Route($requestMethod);
try
{
    $response = $route->request($eUri);
    header('Content-Type: application/json');
    echo json_encode($response);
}
catch(NotFoundException $e)
{
    echo $e->getMessage();
}
catch(ValidationException $e)
{
    echo $e->getMessage();
}