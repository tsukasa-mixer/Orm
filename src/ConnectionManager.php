<?php
namespace Tsukasa\Orm;

use Doctrine\DBAL\DriverManager;
use ReflectionClass;
use Tsukasa\Helpers\SmartProperties;


class ConnectionManager
{
    use SmartProperties;

    /**
     * @var string
     */
    protected $defaultConnection = 'default';

    /**
     * @var array|\Doctrine\DBAL\Connection[]
     */
    protected $connections = [];
    /**
     * @var null
     */
    protected $configuration = null;
    /**
     * @var null
     */
    protected $eventManager = null;

    protected $defaultWrapperClass = 'Tsukasa\Orm\DefaultConnection';

    /**
     * ConnectionManager constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->configure($config);
    }

    /**
     * @param array $connections
     */
    public function setConnections(array $connections)
    {
        foreach ($connections as $name => $config) {

            if (!isset($config['wrapperClass'])) {
                $config['wrapperClass'] = $this->defaultWrapperClass;
            }
            else if($config['wrapperClass'] === false) {
                unset($config['wrapperClass']);
            }

            $this->connections[$name] = DriverManager::getConnection($config, $this->configuration, $this->eventManager);

            if (!empty($config['mapping'])) {
                foreach ($config['mapping'] as $from_type => $to_type) {
                    $this->connections[$name]
                        ->getDatabasePlatform()
                        ->registerDoctrineTypeMapping($from_type, $to_type);
                }
            }

            if (!empty($config['cache'])) {

                $params = $config['cache'];
                $class = $params['class'];
                unset($params['class']);

                if (count($params) == 0) {
                    $adapter = new $class;
                }
                else {
                    $r = new ReflectionClass($class);
                    $adapter = $r->newInstanceArgs($params);
                }

                $this->connections[ $name ]->getConfiguration()->setResultCacheImpl($adapter);
            }
        }
    }

    /**
     * @param array $config
     */
    protected function configure(array $config)
    {
        foreach ($config as $key => $value) {
            if (method_exists($this, 'set' . ucfirst($key))) {
                $this->{'set' . ucfirst($key)}($value);
            } else {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setDefaultConnection($name)
    {
        $this->defaultConnection = $name;
        return $this;
    }

    /**
     * @param null $name
     * @return \Doctrine\DBAL\Connection|null
     */
    public function getConnection($name = null)
    {
        if (empty($name) || empty($this->connections[$name])) {
            $name = $this->defaultConnection;
        }

        if (empty($this->connections[$name])) {
            Xcart::app()->logger->warning('Unknown connection ' . $name);
        }

        return $this->connections[$name];
    }
}