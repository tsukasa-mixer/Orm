<?php

namespace Tsukasa\Orm;

use ArrayIterator;

/**
 * Class DataIterator
 * @package Tsukasa\Orm
 */
class DataIterator extends ArrayIterator
{
    /**
     * @var bool
     */
    public $asArray;
    /**
     * @var QuerySet
     */
    public $qs;

    /**
     * DataIterator constructor.
     * @param array $data
     * @param array $config
     * @param int $flags
     */
    public function __construct(array $data, array $config = [], $flags = 0)
    {
        parent::__construct($data, $flags);

        foreach ($config as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }
}
