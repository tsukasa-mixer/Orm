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
 * @date 04/03/14.03.2014 01:14
 */

namespace Tsukasa\Orm\Tests\Models;


use Tsukasa\Orm\Fields\ForeignField;
use Tsukasa\Orm\Fields\TextField;
use Tsukasa\Orm\Model;

/**
 * Class Customer
 * @package Tsukasa\Orm\Tests\Models
 * @property \Tsukasa\Orm\Tests\Models\User user
 * @property string address
 */
class Customer extends Model
{
    public static function getFields()
    {
        return [
            'user' => [
                'class' => ForeignField::class,
                'modelClass' => User::class,
                'null' => true
            ],
            'address' => TextField::class
        ];
    }
}
