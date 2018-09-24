<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 16/09/16
 * Time: 19:43
 */

namespace Tsukasa\Orm\Tests\Models;

use Tsukasa\Orm\Base;

class DummyModel extends Base
{
    public function update(array $fields = []) : bool
    {
        $state = true;
        if ($state) {
            $this->attributes->resetOldAttributes();
        }
        return $state;
    }

    public function insert(array $fields = []) : bool
    {
        $state = true;
        if ($state) {
            $this->attributes->resetOldAttributes();
        }
        return $state;
    }
}