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
use Tsukasa\Orm\Fields\ForeignField;
use Tsukasa\Orm\Fields\ManyToManyField;
use Tsukasa\Orm\Fields\TextField;
use Tsukasa\Orm\Model;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Product
 * @package Tsukasa\Orm\Tests\Models
 * @property string name
 * @property string price
 * @property string description
 * @property \Tsukasa\Orm\Tests\Models\Category category
 * @property \Tsukasa\Orm\Manager lists
 */
class Product extends Model
{
    public $type = 'SIMPLE';

    public static function getFields()
    {
        return [
            'name' => [
                'class' => CharField::class,
                'default' => 'Product',
                'validators' => [
                    new Assert\Length(['min' => 3])
                ]
            ],
            'price' => [
                'class' => CharField::class,
                'default' => 0
            ],
            'description' => [
                'class' => TextField::class,
                'null' => true
            ],
            'category' => [
                'class' => ForeignField::class,
                'modelClass' => Category::class,
                'null' => true,
            ],
            'lists' => [
                'class' => ManyToManyField::class,
                'modelClass' => ProductList::class,
                'link' => ['product_id', 'product_list_id']
            ]
        ];
    }
}
