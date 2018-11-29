<?php
namespace Tsukasa\Orm;

use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Connection as DBALConnection;
use Tsukasa\Main\ErrorHandler;


class DefaultConnection extends DBALConnection
{
    private $__errHandler = null;
    private $__enableErrHandler = true;
    private $__ignoreErrors = false;

    private $__countQueries = 0;

    public function setErrorHandler($handlerLink)
    {
        $this->__errHandler = $handlerLink;
        return $this;
    }

    public function unsetErrorHandler()
    {
        $this->__errHandler = null;
        return $this;
    }

    public function setEnableErrHandler($enable = true)
    {
        $this->__enableErrHandler = $enable;
        return $this;
    }

    public function setIgnoreErrors($ignore = false)
    {

        $this->__ignoreErrors = $ignore;
        return $this;
    }


//    /**
//     * {@inheritdoc}
//     */
//    public function connect()
//    {
//        return $this->__internalCall(__FUNCTION__, func_get_args());
//    }

    /**
     * {@inheritdoc}
     */
    public function delete($tableExpression, array $identifier, array $types = [])
    {
        return $this->__internalCall(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function update($tableExpression, array $data, array $identifier, array $types = [])
    {
        return $this->__internalCall(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function insert($tableExpression, array $data, array $types = [])
    {
        return $this->__internalCall(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function prepare($statement)
    {
        return $this->__internalCall(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function executeQuery($query, array $params = [], $types = [], QueryCacheProfile $qcp = null)
    {
        $this->__countQueries++;
        return $this->__internalCall(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function executeCacheQuery($query, $params, $types, QueryCacheProfile $qcp)
    {
        return $this->__internalCall(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function query()
    {
        $this->__countQueries++;
        return $this->__internalCall(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function executeUpdate($query, array $params = [], array $types = [])
    {
        $this->__countQueries++;
        return $this->__internalCall(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function exec($statement)
    {
        $this->__countQueries++;
        return $this->__internalCall(__FUNCTION__, func_get_args());
    }

    private function __internalCall($function, array $args = [])
    {
        if (!$this->__enableErrHandler) {
            call_user_func_array('parent::' . $function, $args);
        }

        try {
            return call_user_func_array('parent::' . $function, $args);
        }
        catch (DBALException $e) {
            $this->processException($e, $args[0]);
        }

        return null;
    }

    public function getCountQueries()
    {
        return $this->__countQueries;
    }

    /**
     * @param DBALException $exception
     * @param string $query
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function processException($exception, $query = '')
    {
        if ($this->__ignoreErrors) {
            return;
        }

        if ($this->__errHandler) {
            call_user_func_array($this->__errHandler, [$exception, $query]);
            return;
        }

        $data = $query ? ['SQL query' => $query] : [];

        $data['Error code']     = $exception->getCode();
        $data['Backtrace']      = "\n" .$exception->getTraceAsString();

        $data = array_merge($data, ErrorHandler::getRequestErrData());

//        Xcart::app()->logger->critical($exception->getMessage(), $data, 'sql');
    }
}