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
 * @date 15/07/14.07.2014 17:41
 */

namespace Tsukasa\Orm\Tests\Models;

use Tsukasa\Orm\Fields\HasManyField;
use Tsukasa\Orm\Model;

class BookCategory extends Model
{
    public static function getFields()
    {
        return [
            'category_set' => [
                'class' => HasManyField::class,
                'modelClass' => Book::class,
            ],
            'categories' => [
                'class' => HasManyField::class,
                'modelClass' => Book::class
            ]
        ];
    }
}
