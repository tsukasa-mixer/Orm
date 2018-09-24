<?php

/**
 * All rights reserved.
 *
 * @author Falaleev Maxim
 * @email max@studio107.ru
 * @version 1.0
 * @company Studio107
 * @site http://studio107.ru
 * @date 14/02/15 16:32
 */

namespace Tsukasa\Orm\Tests\Models;

use Tsukasa\Orm\Fields\CharField;
use Tsukasa\Orm\Fields\ForeignField;
use Tsukasa\Orm\Model;

class ModelTyre extends Model
{
    public static function getFields()
    {
        return [
            'name' => [
                'class' => CharField::class,
            ],
            'tyre' => [
                'class' => ForeignField::class,
                'modelClass' => Tyre::class
            ]
        ];
    }
}
