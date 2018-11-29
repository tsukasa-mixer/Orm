<?php

namespace Tsukasa\Orm;

/**
 * Interface ManagerInterface
 * @package Tsukasa\Orm
 */
interface ManagerInterface extends QuerySetInterface
{
    /**
     * @return ModelInterface
     */
    public function getModel();

    /**
     * @return \Tsukasa\Orm\QuerySet
     */
    public function getQuerySet();
}