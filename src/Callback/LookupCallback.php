<?php

namespace Tsukasa\Orm\Callback;

use Tsukasa\Orm\Fields\ManyToManyField;
use Tsukasa\Orm\Fields\RelatedField;
use Tsukasa\Orm\Model;
use Tsukasa\Orm\ModelInterface;
use Tsukasa\QueryBuilder\Callbacks\AbstractCallback;
use Tsukasa\QueryBuilder\Interfaces\ILookupBuilder;
use Tsukasa\QueryBuilder\QueryBuilder;

class LookupCallback extends AbstractCallback
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * LookupCallback constructor.
     *
     * @param Model|ModelInterface $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    public function run(QueryBuilder $queryBuilder, ILookupBuilder $lookupBuilder, array $lookupNodes, $value)
    {
        $lookup = $lookupBuilder->getDefault();
        $column = '';
        $joinAlias = $queryBuilder->getAlias();

        $ownerModel = $this->model;
        $connection = $ownerModel->getConnection();

        reset($lookupNodes);
        $field = $ownerModel->getField(current($lookupNodes));
        $prevField = null;

        foreach ($lookupNodes as $i => $node) {
            /** @var \Tsukasa\Orm\Fields\RelatedField $prevField */
            /** @var \Tsukasa\Orm\Fields\RelatedField $field */

            if ($prevField)
            {
                if ($node === 'through') {
                    if ($prevField instanceof ManyToManyField) {
                        $joinAlias = $prevField
                            ->setConnection($connection)
                            ->buildThroughQuery($queryBuilder, $joinAlias);
                    }
                }
                else {
                    $joinAlias = $prevField
                        ->setConnection($connection)
                        ->buildQuery($queryBuilder, $joinAlias);
                }

                $relatedModel = $prevField->getRelatedModel();
                $field = $relatedModel->getField($node);
            }

            $prevField = null;
            if ($field instanceof RelatedField) {
                $prevField = $field;
            }

            if (count($lookupNodes) === ($i + 1)) {
                if ($lookupBuilder->hasLookup($node) === false) {
                    $column = $joinAlias . '.' . $lookupBuilder->fetchColumnName($node);
                    $columnWithLookup = $column . $lookupBuilder->getSeparator() . $lookupBuilder->getDefault();
                    $queryBuilder->where([$columnWithLookup => $value]);
                }
                else {
                    $lookup = $node;
                    $column = $joinAlias . '.' . $lookupBuilder->fetchColumnName($lookupNodes[$i - 1]);
                }
            }
        }

        return [$lookup, $column, $value];
    }
}