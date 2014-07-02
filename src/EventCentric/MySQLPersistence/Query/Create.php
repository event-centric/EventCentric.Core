<?php
namespace EventCentric\MySQLPersistence\Query;

final class Create
{
    const QUERY = <<<MYSQL
CREATE TABLE `%s` (
  `checkpointNumber` bigint(20) NOT NULL AUTO_INCREMENT,
  `bucket` char(64) NOT NULL DEFAULT '@default',
  `streamContract` varchar(255) NOT NULL,
  `streamId` varchar(255) NOT NULL,
  `streamRevision` int(11) NOT NULL,
  `eventContract` varchar(255) NOT NULL,
  `eventPayload` text NOT NULL,
  `eventId` char(36) NOT NULL,
  `causationId` char(36) DEFAULT NULL,
  `correlationId` char(36) DEFAULT NULL,
  `eventMetadataContract` varchar(255) NULL DEFAULT NULL,
  `eventMetadata` text DEFAULT NULL,
  `commitId` char(36) NOT NULL,
  -- `commitSequence` int(11) NOT NULL,
  `utcCommittedTime` DATETIME NOT NULL,
  -- `dispatched` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`checkpointNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
MYSQL;

    public static function table($tableName)
    {
        return sprintf(self::QUERY, $tableName);
    }
}