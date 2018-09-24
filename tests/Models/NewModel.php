<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 16/09/16
 * Time: 19:43
 */

namespace Tsukasa\Orm\Tests\Models;

use Tsukasa\Orm\Fields\CharField;

class NewModel extends DummyModel
{
    public static function getFields()
    {
        return [
            'username' => [
                'class' => CharField::class
            ],
            'password' => [
                'class' => CharField::class
            ],
        ];
    }
}