<?php

namespace Tsukasa\Orm\Fields;

use Doctrine\DBAL\Types\Type;

/**
 * Class BlobField
 * @package Tsukasa\Orm
 */
class BlobField extends Field
{
    public function getSqlType()
    {
        return Type::getType(Type::BLOB);
    }
}

