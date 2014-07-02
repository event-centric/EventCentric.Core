DROP TABLE `events`;
CREATE TABLE `events` (
  `checkpointNumber` bigint(20) NOT NULL AUTO_INCREMENT,
  `bucket` char(64) NOT NULL DEFAULT '@default',
  `streamContract` varchar(255) NOT NULL,
  `streamId` varchar(255) NOT NULL,
  `streamRevision` int(11) NOT NULL,
  `eventContract` varchar(255) NOT NULL,
  `eventPayload` text NOT NULL,
  `eventId` char(36) NOT NULL,
  -- `causationId` char(36) DEFAULT '',
  -- `correlationId` char(36) DEFAULT '',
  -- `eventMetadataContract` varchar(255) DEFAULT '',
  -- `eventMetadata` text,
  `commitId` char(36) NOT NULL,
  -- `commitSequence` int(11) NOT NULL,
  `utcCommittedTime` DATETIME NOT NULL,
  -- `dispatched` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`checkpointNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

