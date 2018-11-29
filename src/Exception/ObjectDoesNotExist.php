<?php

namespace Tsukasa\Orm\Exception;

use Exception;

/**
 * Class ObjectDoesNotExist
 * @package Tsukasa\Orm
 */
class ObjectDoesNotExist extends OrmExceptions
{
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        if (empty($message)) {
            $message = "The requested object does not exist";
        }
        parent::__construct($message, $code, $previous);
    }
}
