CREATE TABLE `places` (
  `placeId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `lat` float NOT NULL,
  `lon` float NOT NULL,
  PRIMARY KEY (`placeId`)
) ENGINE=InnoDB;

CREATE TABLE `results` (
  `submissionId` int(11) NOT NULL AUTO_INCREMENT,
  `placeId` int(11) NOT NULL,
  `postcode` char(5) DEFAULT NULL,
  `choice` enum('north','south','midlands','dunno') NOT NULL,
  `ip` char(16) NOT NULL,
  PRIMARY KEY (`submissionId`),
  KEY `placeId` (`placeId`)
) ENGINE=InnoDB;

ALTER TABLE `results` ADD CONSTRAINT `results_ibfk_1` FOREIGN KEY (`placeId`) REFERENCES `places` (`placeId`);
