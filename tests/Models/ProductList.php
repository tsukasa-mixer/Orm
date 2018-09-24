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
use Tsukasa\Orm\Fields\DateTimeField;
use Tsukasa\Orm\Fields\ManyToManyField;
use Tsukasa\Orm\Model;

/**
 * Class ProductList
 * @package Tsukasa\Orm\Tests\Models
 * @property string name
 * @property \Tsukasa\Orm\ManyToManyManager products
 */
class ProductList extends Model
{
    public static function getFields()
    {
        return [
            'name' => [
                'class' => CharField::class
            ],
            'products' => [
                'class' => ManyToManyField::class,
                'modelClass' => Product::class,
                'link' => ['product_list_id', 'product_id']
            ],
            'date_action' => [
                'class' => DateTimeField::class,
                'required' => false,
                'null' => true
            ]
        ];
    }
}
