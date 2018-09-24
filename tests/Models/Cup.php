<?php
/**
 * 
 *
 * All rights reserved.
 * 
 * @author Falaleev Maxim
 * @email max@studio107.ru
 * @version 1.0
 * @company Studio107
 * @site http://studio107.ru
 * @date 04/03/14.03.2014 01:15
 */

namespace Tsukasa\Orm\Tests\Models;

use Tsukasa\Orm\Fields\CharField;
use Tsukasa\Orm\Fields\HasManyField;
use Tsukasa\Orm\Model;

/**
 * Class Cup
 * @package Tsukasa\Orm\Tests\Models
 * @property string name
 */
class Cup extends Model
{
    public static function getFields()
    {
        return [
            'name' => [
                'class' => CharField::class
            ],
            'designs' => [
                'class' => HasManyField::class,
                'modelClass' => Design::class,
                'link' => ['cup_id', 'id']
            ],
            'colors' => [
                'class' => HasManyField::class,
                'modelClass' => Color::class,
                'link' => ['cup_id', 'id']
            ]
        ];
    }
}
