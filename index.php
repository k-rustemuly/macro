<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
$uri = getUri();
$eUri = explodeUri($uri);
var_dump($eUri);

function getUri() :string
{
    $scriptPath = $_SERVER["PHP_SELF"];
    $mainPath = trim($scriptPath, "index.php");
    $path = trim($_SERVER["REQUEST_URI"], $mainPath);
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