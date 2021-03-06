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
 * @date 15/07/14.07.2014 17:40
 */

namespace Tsukasa\Orm\Tests\Models;

use Tsukasa\Orm\Fields\ForeignField;
use Tsukasa\Orm\Model;

class Book extends Model
{
    public static function getFields()
    {
        return [
            'category' => [
                'class' => ForeignField::class,
                'modelClass' => BookCategory::class,
                'null' => true,
                'editable' => false
            ],
            'category_new' => [
                'class' => ForeignField::class,
                'modelClass' => BookCategory::class,
                'null' => true,
                'editable' => false
            ]
        ];
    }
}
