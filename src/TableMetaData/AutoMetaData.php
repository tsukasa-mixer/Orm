<?php
namespace Tsukasa\Orm\TableMetaData;

use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Column;
use ReflectionMethod;

use Tsukasa\Orm\Fields\BigIntField;
use Tsukasa\Orm\Fields\BlobField;
use Tsukasa\Orm\Fields\CharField;
use Tsukasa\Orm\Fields\DateField;
use Tsukasa\Orm\Fields\DateTimeField;
use Tsukasa\Orm\Fields\DecimalField;
use Tsukasa\Orm\Fields\FloatField;
use Tsukasa\Orm\Fields\IntField;
use Tsukasa\Orm\Fields\TextField;
use Tsukasa\Orm\Fields\TimeField;
use Tsukasa\QueryBuilder\QueryBuilder;

class AutoMetaData extends MetaData
{
    const CACHE_KEY = 'auto_meta_data_configs';

    private static $_tables;
    private static $_configs;
    /** @var Connection */
    protected $connection;
    protected $className;

    /**
     * @param string $className
     * @throws \ReflectionException
     */
    protected function init($className)
    {
        $this->className = $className;

        if ((new ReflectionMethod($className, 'getFields'))->isStatic()) {
            parent::init($className);
        }

        $primaryFields = [];


        foreach ($this->getTableConfig($className) as $name => $config)
        {
            if (!isset($this->fields[$name])) {
                /** @var \Tsukasa\Orm\Fields\Field $field */
                $field = $this->createField($config);
                $field->setName($name);
                $field->setModelClass($className);

                $this->fields[$name] = $field;
                $this->mapping[$field->getAttributeName()] = $name;

                if ($field->primary) {
                    $primaryFields[] = $field->getAttributeName();
                }
            }
        }

        if (empty($primaryFields) && empty($this->primaryKeys)) {
            $this->primaryKeys = \call_user_func([$className, 'getPrimaryKeyName']);
        }
        elseif (!empty($primaryFields)) {

            $this->primaryKeys = $primaryFields;
        }
    }

    /**
     * @return Connection
     * @throws \ReflectionException
     */
    protected function getConnection()
    {
        if (!$this->connection) {
            $this->connection = (new \ReflectionClass($this->className))
                ->newInstanceWithoutConstructor()
                ->getConnection();
        }

        return $this->connection;
    }

    /**
     * @param string $className
     *
     * @return \Doctrine\DBAL\Schema\Column[]
     */
    private function getTableColumns($className)
    {
        if (!isset(self::$_tables[$className]))
        {

            $adapter = QueryBuilder::getInstance($this->getConnection())->getAdapter();
            $tableName = $adapter->getRawTableName(\call_user_func([$className, 'tableName']));
            $dataBase = null;

            if (strpos($tableName, '.') !== false) {
                $_t = explode('.', $tableName);
                if (count($_t) === 2) {
                    $dataBase = $_t[0];
                    $tableName = $_t[1];
                }
            }

            self::$_tables[$className] = $this->listTableColumns( $tableName,  $dataBase );
        }

        return self::$_tables[$className];
    }

    /**
     * @param string $className
     *
     * @return array Config fields as $name => $config
     */
    private function getTableConfig($className)
    {
        if (!isset(self::$_configs[$className]))
        {
            foreach ($this->getTableColumns($className) as $column) {
                $name = $column->getName();

                if (!isset($this->fields[$name]) && $config = $this->getConfigFromDBAL($column)) {
                    self::$_configs[$className][$name] = $config;
                }
            }
        }

        return self::$_configs[$className];
    }

    private function getConfigFromDBAL(Column $column)
    {
        if ($type = $column->getType())
        {
            $config = [
                'null'    => !$column->getNotnull(),
                'default' => $column->getDefault(),
            ];

            if ($column->getLength()) {
                $config['length'] = $column->getLength();
            }

            switch ($type->getName()) {
                case 'smallint' :
                case 'integer' : {
                    $config['class'] = IntField::class;
                    break;
                }
                case 'bigint' : {
                    $config['class'] = BigIntField::class;
                    break;
                }
                case 'decimal' : {
                    $config['class'] = DecimalField::class;
                    $config['precision'] = $column->getPrecision();
                    $config['scale'] = $column->getScale();
                    break;
                }
                case 'float' : {
                    $config['class'] = FloatField::class;
                    break;
                }

                case 'blob' : {
                    $config['class'] = BlobField::class;
                    unset($config['length']);
                    break;
                }
                case 'date' : {
                    $config['class'] = DateField::class;
                    break;
                }
                case 'datetime' : {
                    $config['class'] = DateTimeField::class;
                    break;
                }
                case 'time' : {
                    $config['class'] = TimeField::class;
                    break;
                }
//            case 'timeshtamp' : {
//                $config['class'] = TimestampField::class;
//                break;
//            }

                case 'string' : {
                    $config['class'] = CharField::class;
                    break;
                }
                case 'longtext' :
                case 'text' : {
                    unset($config['length']);
                }
                default: {
                    $config['class'] = TextField::class;
                }
            }

            return $config;
        }

        return null;
    }


    public function listTableColumns($table, $database = null) {

        return $this->getConnection()
            ->getSchemaManager()
            ->listTableColumns($table, $database);

        //@TODO: fix me
        $connection = $this->getConnection();
        $platform  = $connection->getDatabasePlatform();

        if (!$database) {
            $database = $connection->getDatabase();
        }

        $qcp = null;
        if ($connection->getConfiguration()->getResultCacheImpl()) {
            $qcp = new QueryCacheProfile(3600*12);
        }

        $sql = $platform->getListTableColumnsSQL($table, $database);

        return $connection->executeQuery($sql, [], [], $qcp)->fetchAll();
    }
}