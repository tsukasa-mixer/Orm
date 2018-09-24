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
 * @date 17/05/14.05.2014 16:50
 */

namespace Tsukasa\Orm\Tests\Models;


use Tsukasa\Orm\Fields\CharField;
use Tsukasa\Orm\Model;

class CustomPk extends Model
{
    public static function getFields()
    {
        return [
            'id' => [
                'class' => CharField::class,
                'primary' => true
            ]
        ];
    }
}
