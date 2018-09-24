<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 16/09/16
 * Time: 15:32
 */

namespace Tsukasa\Orm\Tests\Models;

use Tsukasa\Orm\Fields\OneToOneField;
use Tsukasa\Orm\Model;

class MemberProfile extends Model
{
    public static function getFields()
    {
        return [
            'user' => [
                'class' => OneToOneField::class,
                'modelClass' => Member::class,
                'primary' => true,
                'to' => 'id'
            ]
        ];
    }
}