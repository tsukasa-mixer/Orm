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
 * @date 05/05/14.05.2014 19:53
 */

namespace Tsukasa\Orm\Tests\Models;


use Tsukasa\Orm\Fields\IntField;
use Tsukasa\Orm\Model;

class Hits extends Model
{
    public static function getFields()
    {
        return [
            'hits' => [
                'class' => IntField::class,
                'default' => 0
            ]
        ];
    }
}
