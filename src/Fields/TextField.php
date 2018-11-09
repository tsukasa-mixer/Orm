<?php

namespace Tsukasa\Orm\Fields;

use Doctrine\DBAL\Types\Type;

/**
 * Class TextField
 * @package Tsukasa\Orm
 */
class TextField extends Field
{
    public $formField = '\Tsukasa\Form\Fields\TextField';

    /**
     * @return string
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getSqlType()
    {
        return Type::getType(Type::TEXT);
    }
}