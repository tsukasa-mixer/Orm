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

class Member extends Model
{
    public static function getFields()
    {
        return [
            'profile' => [
                'class' => OneToOneField::class,
                'modelClass' => MemberProfile::class,
            ],
        ];
    }
}