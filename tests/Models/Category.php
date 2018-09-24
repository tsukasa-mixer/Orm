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
 * @date 04/03/14.03.2014 01:17
 */

namespace Tsukasa\Orm\Tests\Models;


use Tsukasa\Orm\Fields\CharField;
use Tsukasa\Orm\Fields\HasManyField;
use Tsukasa\Orm\Model;

/**
 * Class Category
 * @package Tsukasa\Orm\Tests\Models
 * @property string name
 * @property \Tsukasa\Orm\HasManyManager products
 */
class Category extends Model
{
    public static function getFields()
    {
        return [
            'name' => [
                'class' => CharField::class
            ],
            'products' => [
                'class' => HasManyField::class,
                'modelClass' => Product::class,
                'null' => true,
                'editable' => false,
                'link' => ['category_id', 'id']
            ],
        ];
    }
}
