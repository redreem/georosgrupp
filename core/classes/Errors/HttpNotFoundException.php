<?php
namespace Core\Errors;


use Throwable;

class HttpNotFoundException extends \Exception
{
    public function __construct($status_code = 404, string $message = "", int $code = 0, Throwable $previous = null)
    {
        $code = $status_code;
        parent::__construct($message, $code, $previous);
    }
}