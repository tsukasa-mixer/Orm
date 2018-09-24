<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 24/07/16
 * Time: 07:35
 */

namespace Tsukasa\Orm\Tests\Databases\Mysql\Fields;

use Tsukasa\Orm\Tests\Fields\OneToOneFieldTest;

class MysqlOneToOneFieldTest extends OneToOneFieldTest
{
    public $driver = 'mysql';
}