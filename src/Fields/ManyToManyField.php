<?php

namespace Tsukasa\Orm\Fields;

use Exception;
use Mindy\QueryBuilder\Expression;
use RuntimeException;
use Tsukasa\Orm\Manager;
use Tsukasa\Orm\MetaData;
use Tsukasa\Orm\Model;
use Tsukasa\Orm\ModelInterface;
use Tsukasa\Orm\AbstractModel;
use Mindy\QueryBuilder\QueryBuilder;
use Tsukasa\Orm\ManagerInterface;

/**
 * Class ManyToManyField
 * @package Tsukasa\Orm
 */
class ManyToManyField extends RelatedField
{
    public $null = true;

    /**
     * If to self, changes 'to' and 'from' fields
     * @var bool
     */
    public $reversed = false;
    /**
     * @var array
     */
    public $extra = [];
    /**
     * @var null|string
     */
    public $through;

    /**
     * Related model class
     * @var string
     */
    public $modelClass;
    /**
     * Main model
     * @var \Tsukasa\Orm\Model
     */
    protected $_model;
    /**
     * Related model
     * @var \Tsukasa\Orm\Model
     */
    protected $_relatedModel;
    /**
     * Primary key name
     * @var string
     */
    protected $_modelPk;
    /**
     * Primary key name of the related model
     * @var string
     */
    protected $_relatedModelPk;
    /**
     * Model column in "link" table
     * @var string
     */
    protected $_modelColumn;
    /**
     * Related model column in "link" table
     * @var string
     */
    protected $_relatedModelColumn;
    /**
     * @var string
     */
    private $_tableName;
    /**
     * @var
     */
    protected $_columns = [];

    /**
     * @return \Tsukasa\Orm\Model
     */
    public function getRelatedModel()
    {
        if (!$this->_relatedModel) {
            $this->_relatedModel = new $this->modelClass();
        }
        return $this->_relatedModel;
    }

    /**
     * @return string PK name of related model
     */
    public function getRelatedModelPk()
    {
        if (!$this->_relatedModelPk) {
            $this->_relatedModelPk = $this->getRelatedModel()->getPrimaryKeyName();
        }
        return $this->_relatedModelPk;
    }

    /**
     * @return string Related model column in "link" table
     */
    public function getRelatedModelColumn()
    {
        if (empty($this->_modelColumn)) {
            if (!empty($this->through)) {
                if (empty($this->link)) {
                    $throughClass = $this->through;
                    $through = call_user_func([$throughClass, 'create']);

                    /**
                     * @var  Model $through
                     * @var  string $fieldName
                     * @var  mixed $params
                     */
                    foreach ($through->getFields() as $fieldName => $params) {
                        if (isset($params['modelClass']) && $params['modelClass'] == $this->modelClass) {
                            $this->_relatedModelColumn = $through->getField($fieldName)->getAttributeName();
                            break;
                        }
                    }
                } else {
                    if ($this->link) {
                        throw new Exception('throughLink is missing in configutaion');
                    }

                    [$fromId, $toId] = $this->link;

                    $this->_relatedModelColumn = $this->reversed ? $toId : $fromId;
                }
            } else {
                $end = $this->getModelPk();
                if ($this->ownerClassName === $this->modelClass) {
                    $end = $this->reversed ? 'to_id' : 'from_id';
                }
                $tmp = explode('\\', $this->modelClass);
                $column = AbstractModel::normalizeTableName(end($tmp));
                $this->_relatedModelColumn = $column . '_' . $end;
            }
        }

        return $this->_relatedModelColumn;
    }

    /**
     * @return string PK name of model
     */
    public function getModelPk()
    {
        if (!$this->_modelPk) {
            $this->_modelPk = MetaData::getInstance($this->ownerClassName)->getPrimaryKeyName();
        }
        return $this->_modelPk;
    }

    /**
     * @return string Model column in "link" table
     * @throws Exception
     */
    public function getModelColumn()
    {
        if (empty($this->_modelColumn)) {
            if (!empty($this->through)) {
                if (empty($this->link)) {
                    $throughClass = $this->through;
                    $through = call_user_func([$throughClass, 'create']);

                    /**
                     * @var  Model $through
                     * @var  string $fieldName
                     * @var  mixed $params
                     */
                    foreach ($through->getFields() as $fieldName => $params) {
                        if (isset($params['modelClass']) && $params['modelClass'] == $this->ownerClassName) {
                            $this->_modelColumn = $through->getField($fieldName)->getAttributeName();
                            break;
                        }
                    }
                } else {
                    if ($this->link) {
                        throw new Exception('throughLink is missing in configutaion');
                    }

                    [$fromId, $toId] = $this->link;

                    $this->_modelColumn = $this->reversed ? $toId : $fromId;
                }
            } else {
                $end = $this->getModelPk();
                if ($this->ownerClassName === $this->modelClass) {
                    $end = $this->reversed ? 'to_id' : 'from_id';
                }
                $tmp = explode('\\', $this->ownerClassName);
                $column = AbstractModel::normalizeTableName(end($tmp));
                $this->_modelColumn = $column . '_' . $end;
            }
        }

        return $this->_modelColumn;
    }

    public function getSelectJoin(QueryBuilder $qb, $topAlias)
    {
        $throughAlias = $qb->makeAliasKey($this->getTableName());
        $alias = $qb->makeAliasKey($this->getRelatedTable());
        $on = [];

        $through = call_user_func([$this->through, 'create']);

        foreach ($through->getFields() as $fieldName => $params) {
            if (isset($params['modelClass']) && $params['modelClass'] == $this->modelClass) {
                if ($params['link'])
                {
                    foreach ($params['link'] as $from => $to)
                    {
                        $on[$throughAlias . '.' . $from] = $alias . '.' . $to;
                    }
                }
            }
        }

        return [
            ['INNER JOIN', $this->getTableName(), $on, $throughAlias]
        ];
    }

    public function getSelectThroughJoin(QueryBuilder $qb, $topAlias)
    {
        $joins = $this->getJoin($qb, $topAlias);

        return [
            $joins[0]
        ];
    }

    /**
     * @return \Tsukasa\Orm\ManagerInterface
     * @throws \Exception
     */
    public function getManager()
    {
        $className = \get_class($this->getRelatedModel()->objects());
        $config = [
            'modelColumn' => $this->getRelatedModelColumn(),
            'primaryModelColumn' => $this->getModelColumn(),
            'primaryModel' => $this->getModel(),
            'relatedTable' => $this->getTableName(),
            'extra' => $this->extra,
            'through' => $this->through,
        ];

        /** @var \Tsukasa\Orm\Manager $manager */
        $manager = (new \ReflectionClass($className))->newInstanceArgs([
                                                                           $this->getRelatedModel(),
                                                                           $this->getRelatedModel()->getConnection(),
                                                                           $config
                                                                       ]);


        $throughAlias = $manager->getQueryBuilder()->makeAliasKey($this->getTableName());
        $this->buildSelectQuery($manager->getQueryBuilder(), $manager->getQueryBuilder()->makeAliasKey($this->getModel()->tableName()));

        $through = \call_user_func([$this->through, 'create']);
        $adapter = $manager->getQueryBuilder()->getAdapter();

        foreach ($through->getFields() as $fieldName => $params) {
            if (isset($params['modelClass'])
                && (
                    $this->ownerClassName == $params['modelClass']
                    || is_subclass_of($this->ownerClassName, $params['modelClass']))
                )
            {
                if ($params['link'])
                {
                    foreach ($params['link'] as $from => $to)
                    {
                        $throughAlias = $adapter->quoteColumn($throughAlias);
                        $from = $adapter->quoteColumn($from);

                        if (\is_null($this->getModel()->{$to})) {
                            $manager->filter("{$throughAlias}.{$from} IS NULL");
                        }
                        else {
                            $to = $adapter->quoteValue($this->getModel()->{$to});
                            $manager->filter("{$throughAlias}.{$from} = {$to}");
                        }
                    }
                }
            }
        }

        if (!empty($this->extra)) {
            $manager->filter($this->extra);
        }

        return $manager;
    }

    private $_build_through = false;

    public function buildThroughQuery(QueryBuilder $qb, $topAlias)
    {
        $this->_build_through = true;
        $joinAlias = '???';
        foreach ($this->getSelectThroughJoin($qb, $topAlias) as $join) {
            list($joinType, $tableName, $on, $alias) = $join;
            $qb->join($joinType, $tableName, $on, $alias);
            $joinAlias = $alias;
        }
        return $joinAlias;
    }

    /**
     * Table name of the "link" table
     *
     * @return string
     * @throws \Exception
     */
    public function getTableName()
    {
        if (!$this->through) {
            //@TODO: remove me - not used, saved for history
            $adapter = QueryBuilder::getInstance($this->getRelatedModel()->getConnection())->getAdapter();
            $parts = [$adapter->getRawTableName($this->getTable()), $adapter->getRawTableName($this->getRelatedTable())];
            sort($parts);
            return '{{%' . implode('_', $parts) . '}}';
        }
        else {
            return call_user_func([$this->through, 'tableName']);
        }
    }

    /**
     * @return array "link" table columns
     * @throws \Doctrine\DBAL\DBALException|\Exception
     */
    public function getColumns()
    {
        if (empty($this->link)) {
            $from = $this->getRelatedModelColumn();
            $to = $this->getModelColumn();
        } else {
            list($from, $to) = $this->link;
        }

        return [
            (new IntField(['name' => $from]))->getColumn(),
            (new IntField(['name' => $to]))->getColumn()
        ];
    }

    /**
     * @return bool|string
     */
    public function getSqlType()
    {
        return false;
    }

    protected function preformatValue($value)
    {
        if (empty($value)) {
            return [];
        }

        if (!\is_array($value)) {
            if (\is_string($value) && strpos($value, ',') !== false) {
                $value = explode(',', $value);
            } else {
                $value = [$value];
            }
        }

        if (\is_array($value) && \count($value) > 0) {
            if (
                is_numeric($value[0]) ||
                $value[0] instanceof Model
            ) {
                return $value;
            }
            else {
                if (empty($value[0])) {
                    return [];
                }

                throw new RuntimeException("{$this->getName()}: ManyToMany field can set only arrays of Models or existing primary keys");
            }
        }
        else {
            return [];
        }
    }

    /**
     * @param array $value
     * @throws \Exception
     */
    public function setValue($value)
    {
        if ($value instanceof Manager) {
            $value = $value->all();
        }

        $value = $this->preformatValue($value);
        $manager = $this->getManager();
        $manager->clean();

        foreach ($value as $linkModel) {
            if (
                ($linkModel instanceof ModelInterface) === false &&
                !is_a($linkModel, $this->modelClass)) {
                $linkModel = \call_user_func([$this->modelClass, 'objects'])->get(['pk' => $linkModel]);
            }

            if ($linkModel instanceof ModelInterface) {
                $manager->link($linkModel);
            } else {
                throw new Exception("ManyToMany field can set only arrays of Models or existing primary keys");
            }
        }
    }

    protected function getThroughTableName()
    {
        if ($this->through) {
            return \call_user_func([$this->through, 'tableName']);
        }
    }

    public function getJoin(QueryBuilder $qb, $topAlias)
    {
        $on_owner = [];
        $on_related = [];

        $throughAlias = $qb->makeAliasKey($this->getTableName());
        $alias = $qb->makeAliasKey($this->getRelatedTable());

        $through = call_user_func([$this->through, 'create']);

        foreach ($through->getFields() as $fieldName => $params) {
            if (isset($params['modelClass']) && $params['modelClass'] == $this->ownerClassName) {
                if ($params['link'])
                {
                    foreach ($params['link'] as $from => $to)
                    {
                        $on_owner[$throughAlias . '.' . $from] = $topAlias . '.' . $to;
                    }
                }
            }

            if (isset($params['modelClass']) && $params['modelClass'] == $this->modelClass) {
                if ($params['link'])
                {
                    foreach ($params['link'] as $from => $to)
                    {
                        $on_related[$throughAlias . '.' . $from] = $alias . '.' . $to;
                    }
                }
            }
        }

        return [
            ['LEFT OUTER JOIN', $this->getTableName(), $on_owner, $throughAlias],
            ['LEFT OUTER JOIN', $this->getRelatedTable(), $on_related, $alias],
        ];
    }

    public function fetch($value)
    {
        // TODO: Implement fetch() method.
    }

    public function getAttributeName()
    {
        return false;
    }

    public function getValue()
    {
        return $this->getManager();
    }
}
