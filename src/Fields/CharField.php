<?php

namespace Tsukasa\Orm\Fields;

use Doctrine\DBAL\Types\Type;

/**
 * Class CharField
 * @package Tsukasa\Orm
 */
class CharField extends Field
{
    /**
     * @var int
     */
    public $length = 255;

    /**
     * @return string
     */
    public function getSqlType()
    {
        return Type::getType(Type::STRING);
    }
}
