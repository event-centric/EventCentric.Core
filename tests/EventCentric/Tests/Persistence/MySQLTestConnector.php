<?php

namespace EventCentric\Tests\Persistence;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Logging\EchoSQLLogger;

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
     * @return Connection
     * @throws DBALException
     */
    private static function create()
    {
        $configuration = new Configuration();
        $configuration->getSQLLogger(new EchoSQLLogger());
        $parameters = [
            'dbname' => $_ENV['testsuite_db_name'],
            'user' => $_ENV['testsuite_db_user'],
            'password' => $_ENV['testsuite_db_password'],
            'host' => $_ENV['testsuite_db_host'],
            'driver' => 'pdo_mysql',
        ];
        $connection = DriverManager::getConnection(
            $parameters,
            $configuration
        );

        return $connection;
    }
}