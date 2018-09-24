<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 16/09/16
 * Time: 19:14
 */

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