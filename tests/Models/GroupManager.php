<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 24/07/16
 * Time: 13:12
 */

namespace Tsukasa\Orm\Tests\Models;

use Tsukasa\Orm\Manager;

class GroupManager extends Manager
{
    public function published()
    {
        return $this;
    }
}