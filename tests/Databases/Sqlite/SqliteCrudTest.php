<?php

/**
 * All rights reserved.
 *
 * @author Falaleev Maxim
 * @email max@studio107.ru
 * @version 1.0
 * @company Studio107
 * @site http://studio107.ru
 * @date 06/02/15 18:08
 */

namespace Tsukasa\Orm\Tests\Databases\Sqlite;

use Tsukasa\Orm\Tests\Basic\CrudTest;

class SqliteCrudTest extends CrudTest
{
    public $driver = 'sqlite';
}
