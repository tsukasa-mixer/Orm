<?php

namespace Tsukasa\Orm\Fields;

use Doctrine\DBAL\Types\Type;

/**
 * Class FloatField
 * @package Tsukasa\Orm
 */
class FloatField extends Field
{
    /**
     * @return string
     */
    public function getSqlType()
    {
        return Type::getType(Type::FLOAT);
    }

    public function getValue()
    {
        return floatval(parent::getValue());
    }
}
