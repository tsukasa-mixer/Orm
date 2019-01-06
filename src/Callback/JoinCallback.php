<?php

namespace Tsukasa\Orm\Callback;

use Tsukasa\Orm\Fields\ManyToManyField;
use Tsukasa\Orm\Fields\RelatedField;
use Tsukasa\Orm\Model;
use Tsukasa\Orm\ModelInterface;
use Tsukasa\QueryBuilder\Callbacks\AbstractJoinCallback;
use Tsukasa\QueryBuilder\Interfaces\ILookupBuilder;
use Tsukasa\QueryBuilder\QueryBuilder;

class JoinCallback extends AbstractJoinCallback
{
    protected $model;

    /**
     * JoinCallback constructor.
     *
     * @param Model|ModelInterface $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    public function run(QueryBuilder $queryBuilder, ILookupBuilder $lookupBuilder, array $lookupNodes)
    {
        $column = '';
        $alias = '';
        /** @var \Tsukasa\Orm\Fields\RelatedField|null $prevField */
        $prevField = null;
        foreach ($lookupNodes as $i => $nodeName) {
            if ($i + 1 == count($lookupNodes)) {
                $column = $nodeName;
            } else {
                if ($nodeName == 'through' && $prevField && $prevField instanceof ManyToManyField) {
                    $alias = $prevField->setConnection($this->model->getConnection())->buildThroughQuery($queryBuilder, $queryBuilder->getAlias());
                }
                else if ($this->model->hasField($nodeName)) {
                    $field = $this->model->getField($nodeName);

                    if ($field instanceof RelatedField) {
                        /** @var \Tsukasa\Orm\Fields\RelatedField $field */
                        $alias = $field->setConnection($this->model->getConnection())->buildQuery($queryBuilder, $queryBuilder->getAlias());
                        $prevField = $field;
                    }
                }
            }
        }

        if (empty($alias) || empty($column)) {
            return false;
        }

        return [$alias, $column];
    }
}