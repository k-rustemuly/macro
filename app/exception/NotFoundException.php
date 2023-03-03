<?php 
namespace app\exception;

use Exception;

class NotFoundException extends Exception
{
    protected $message = "Resource Not Found";
}