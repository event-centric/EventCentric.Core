<?php

namespace EventCentric\V2Persistence\Schema\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Schema\Schema as DoctrineSchema;
use EventCentric\V2Persistence\Schema\Schema;

/**
 * Class EventStoreSchema
 */
class EventStoreSchema implements Schema
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @param Connection $connection
     * @param string     $tableName
     */
    public function __construct(Connection $connection, $tableName = 'events')
    {
        $this->connection = $connection;
        $this->tableName  = (string) $tableName;
    }

    public function createSchema()
    {
        $fromSchema = $this
            ->connection
            ->getSchemaManager()
            ->createSchema();

        if ($fromSchema->hasTable($this->tableName)) {
            return;
        }

        $toSchema = clone $fromSchema;
        $table = $toSchema->createTable($this->tableName);

        // Fields
        $table->addColumn('checkpointNumber', Type::BIGINT, ['autoincrement' => true]);
        $table->addColumn('bucket', Type::STRING, ['length' => 64, 'fixed' => true]);
        $table->addColumn('streamContract', Type::STRING, ['length' => 255]);
        $table->addColumn('eventContract', Type::STRING, ['length' => 255]);
        $table->addColumn('eventPayload', Type::TEXT);
        $table->addColumn('streamId', Type::STRING, ['length' => 255]);
        $table->addColumn('streamRevision', Type::INTEGER, ['unsigned' => true]);
        $table->addColumn('utcCommittedTime', Type::DATETIME);
        $table->addColumn('eventMetadataContract', Type::STRING, ['length' => 255, 'notnull' => false]);
        $table->addColumn('eventMetadata', Type::TEXT);
        $table->addColumn('causationId', Type::STRING, ['length' => 36, 'notnull' => false, 'fixed' => true]);
        $table->addColumn('correlationId', Type::STRING, ['length' => 36, 'notnull' => false, 'fixed' => true]);
        $table->addColumn('eventId', Type::STRING, ['length' => 36, 'fixed' => true]);
        $table->addColumn('commitId', Type::STRING, ['length' => 36, 'fixed' => true]);
        $table->addColumn('commitSequence', Type::INTEGER, ['unsigned' => true]);
        $table->addColumn('dispatched', Type::BOOLEAN, ['default' => false]);

        // Table options
        $table->addOption('type', 'InnoDB');
        $table->addOption('charset', 'utf8');

        // Keys
        $table->setPrimaryKey(['checkpointNumber']);
        $table->addIndex(['bucket', 'streamContract', 'streamId'], 'IDX_STREAM_LOOKUP');

        $this->executeDiff($fromSchema, $toSchema, true);
    }

    public function dropSchema()
    {
        $fromSchema = $this
            ->connection
            ->getSchemaManager()
            ->createSchema();

        if ($fromSchema->hasTable($this->tableName)) {
            $toSchema = clone $fromSchema;
            $toSchema->dropTable($this->tableName);
            $this->executeDiff($fromSchema, $toSchema, false);
        }
    }

    public function clearSchema()
    {
        $fromSchema = $this
            ->connection
            ->getSchemaManager()
            ->createSchema();

        if ($fromSchema->hasTable($this->tableName)) {
            $sql = $this
                ->connection
                ->getDatabasePlatform()
                ->getTruncateTableSQL($this->tableName);

            $this->connection->executeQuery($sql);
        }
    }

    /**
     * @param DoctrineSchema $fromSchema
     * @param DoctrineSchema $toSchema
     * @param bool           $save
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function executeDiff(DoctrineSchema $fromSchema, DoctrineSchema $toSchema, $save = false)
    {
        $comparator = new Comparator();
        $diff = $comparator->compare($fromSchema, $toSchema);

        if ($save) {
            $sqlArray = $diff->toSaveSql($this->connection->getDatabasePlatform());
        } else {
            $sqlArray = $diff->toSql($this->connection->getDatabasePlatform());
        }

        foreach ($sqlArray as $sql) {
            $this->connection->executeQuery($sql);
        }
    }
}
