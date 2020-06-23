<?php

namespace Apps\Core_MobileApi\Api\Exception;

class UndefinedResourceName extends \Exception
{

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        if (empty($message)) {
            $message = "Undefined Resource Name";
        }
        parent::__construct($message, $code, $previous);
    }
}