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
 * @date 15/09/14.09.2014 15:11
 */

namespace Tsukasa\Orm\Tests\Models;

use Tsukasa\Orm\Fields\CharField;
use Tsukasa\Orm\Fields\DateTimeField;
use Tsukasa\Orm\Fields\FileField;
use Tsukasa\Orm\Fields\IntField;
use Tsukasa\Orm\Fields\TextField;
use Tsukasa\Orm\Model;

class Solution extends Model
{
    const STATUS_COMPLETE = 1;
    const STATUS_SUCCESS = 2;

    public static function getFields()
    {
        return [
            'name' => [
                'class' => CharField::class,
            ],
            'court' => [
                'class' => CharField::class,
            ],
            'question' => [
                'class' => CharField::class,
            ],
            'result' => [
                'class' => CharField::class,
            ],
            'document' => [
                'class' => CharField::class,
                'null' => true
            ],
            'content' => [
                'class' => TextField::class,
            ],
            'status' => [
                'class' => IntField::class,
                'choices' => [
                    self::STATUS_SUCCESS => 'Successful',
                    self::STATUS_COMPLETE => 'Complete'
                ]
            ],
            'created_at' => [
                'class' => DateTimeField::class,
                'autoNowAdd' => true
            ]
        ];
    }

    public function getIsComplete()
    {
        return self::STATUS_SUCCESS == $this->status;
    }
}
