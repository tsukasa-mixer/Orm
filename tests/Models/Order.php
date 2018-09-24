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


use Tsukasa\Orm\Fields\ForeignField;
use Tsukasa\Orm\Fields\IntField;
use Tsukasa\Orm\Fields\ManyToManyField;
use Tsukasa\Orm\Model;

/**
 * Class Order
 * @package Tsukasa\Orm\Tests\Models
 * @property \Tsukasa\Orm\Tests\Models\Customer customer
 * @property \Tsukasa\Orm\ManyToManyManager products
 */
class Order extends Model
{
    public static function getFields()
    {
        return [
            'customer' => [
                'class' => ForeignField::class,
                'modelClass' => Customer::class
            ],
            'products' => [
                'class' => ManyToManyField::class,
                'modelClass' => Product::class,
                'link' => ['order_id', 'product_id']
            ],
            'discount' => [
                'class' => IntField::class,
                'null' => true
            ]
        ];
    }
}
