<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 27/07/16
 * Time: 14:49
 */

namespace Tsukasa\Orm\Tests\Models;

use Tsukasa\Orm\Fields\CharField;
use Tsukasa\Orm\Model;

class Dummy extends Model
{
    public static function getFields()
    {
        return [
            'name' => [
                'class' => CharField::class,
            ],
            'address' => [
                'class' => CharField::class,
                'default' => 'example'
            ]
        ];
    }
}