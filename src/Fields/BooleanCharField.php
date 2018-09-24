<?php

namespace Tsukasa\Orm\Fields;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class BooleanCharField extends BooleanField
{
    public function getSqlType()
    {
        return Type::getType(Type::STRING);
    }

    public function convertToDatabaseValueSQL($value, AbstractPlatform $platform)
    {
        $value = ($value == 'Y');

        return parent::convertToPHPValue($value, $platform);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        $value =  ((bool)$value ) ? 'Y': 'N';

        return parent::convertToDatabaseValue($value, $platform);
    }

}