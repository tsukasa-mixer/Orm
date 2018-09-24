<?php

namespace Tsukasa\Orm\Tests\Models;

use Tsukasa\Orm\Fields\BlobField;
use Tsukasa\Orm\Fields\CharField;
use Tsukasa\Orm\Fields\IntField;
use Tsukasa\Orm\Model;

/**
 * Class Session
 * @package Modules\User
 */
class Session extends Model
{
    public static function getFields()
    {
        return [
            'id' => [
                'class' => CharField::class,
                'length' => 32,
                'primary' => true,
                'null' => false,
            ],
            'expire' => [
                'class' => IntField::class,
                'null' => false,
            ],
            'data' => [
                'class' => BlobField::class,
                'null' => true,
            ]
        ];
    }

    public function __toString()
    {
        return (string)$this->id;
    }
}
