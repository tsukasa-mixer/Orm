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
 * @date 04/03/14.03.2014 01:15
 */

namespace Tsukasa\Orm\Tests\Models;


use Tsukasa\Orm\Fields\CharField;
use Tsukasa\Orm\Fields\ManyToManyField;
use Tsukasa\Orm\Model;

/**
 * Class Worker
 * @package Tsukasa\Orm\Tests\Models
 * @property string name
 * @property \Tsukasa\Orm\ManyToManyManager projects
 */
class Worker extends Model
{
    public static function getFields()
    {
        return [
            'name' => [
                'class' => CharField::class
            ],
            'projects' => [
                'class' => ManyToManyField::class,
                'modelClass' => Project::class,
                'through' => ProjectMembership::class,
                'link' => ['worker_id', 'project_id']
            ]
        ];
    }
}
