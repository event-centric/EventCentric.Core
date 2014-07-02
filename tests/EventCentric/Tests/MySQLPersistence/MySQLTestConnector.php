<?php

namespace EventCentric\Tests\MySQLPersistence;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;

// I guess doing it statically is ok for testing
final class MySQLTestConnector
{
    static private $connection;

    public static function connect()
    {
        if(!self::$connection){
            self::$connection = self::create();
        }
        return self::$connection;
    }

    /**
     * @return \Doctrine\DBAL\Connection
     * @throws \Doctrine\DBAL\DBALException
     */
    private static function create()
    {
        $configuration = new Configuration();
        $parameters = [
            'dbname' => 'eventcentric',
            'user' => 'root',
            'password' => 'root',
            'host' => 'localhost',
            'driver' => 'pdo_mysql',
        ];
        $connection = DriverManager::getConnection(
            $parameters,
            $configuration
        );

        return $connection;
    }
}