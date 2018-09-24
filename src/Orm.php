<?php

namespace Tsukasa\Orm;

use Doctrine\DBAL\Driver\Connection;

use Xcart\Connection as XcartConnection;

class Orm
{
    protected static $connection;

    public static function setDefaultConnection(Connection $connection)
    {
        self::$connection = $connection;
    }

    public static function getDefaultConnection()
    {
        if (!self::$connection) {
            throw new \RuntimeException('Connection not set');
        }
        // if (self::$connection === null) {

        //     if (Xcart::app()->db && Xcart::app()->db instanceof ConnectionManager)
        //     {
        //         self::$connection = Xcart::app()->db->getConnection('default');
        //     }
        //     else {
        //         self::$connection = XcartConnection::getInstance();
        //     }
        // }
        return self::$connection;
    }
}