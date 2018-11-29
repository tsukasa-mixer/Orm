<?php

namespace Tsukasa\Orm\Fields;

use DateTime;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Mindy\QueryBuilder\QueryBuilder;
use Tsukasa\Orm\ModelInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class DateField
 * @package Tsukasa\Orm
 */
class DateField extends Field
{


    /**
     * @var bool
     */
    public $autoNowAdd = false;

    /**
     * @var bool
     */
    public $autoNow = false;

    /**
     * {@inheritdoc}
     */
    public function getSqlType()
    {
        return Type::getType(Type::DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function getValidationConstraints()
    {
        $constraints = [
            new Assert\Date()
        ];
        if ($this->isRequired()) {
            $constraints[] = new Assert\NotBlank();
        }

        return $constraints;
    }

    /**
     * {@inheritdoc}
     */
    public function isRequired()
    {
        if ($this->autoNow || $this->autoNowAdd) {
            return false;
        }
        return parent::isRequired();
    }

    /**
     * {@inheritdoc}
     */
    public function beforeInsert(ModelInterface $model, $value)
    {
        if (($this->autoNow || $this->autoNowAdd) && $model->getIsNewRecord()) {
            $model->setAttribute($this->getAttributeName(), new \DateTime());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function beforeUpdate(ModelInterface $model, $value)
    {
        if ($this->autoNow && $model->getIsNewRecord() === false) {
            $model->setAttribute($this->getAttributeName(), new \DateTime());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        $value = null;

        if ($value = parent::getValue()) {

            $pl = $this->getModel()->getConnection()->getDatabasePlatform();
            $value = $this->convertToPHPValue($value, $pl);
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (($value instanceof DateTime) == false && !is_null($value)) {
            $value = (new DateTime())->setTimestamp(is_numeric($value) ? $value : strtotime($value));
        }

        return $this->getSqlType()->convertToDatabaseValue($value, $platform);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value && is_string($value)) {
            $value = (new \DateTime())->setTimestamp(strtotime($value));
        }

        return parent::convertToPHPValue($value, $platform); // TODO: Change the autogenerated stub
    }
}