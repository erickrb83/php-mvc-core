<?php 
namespace app\core\exception;

class NotFoundException extends \Exception {
    protected $message = "Page not Found";
    protected $code = 404;
}
