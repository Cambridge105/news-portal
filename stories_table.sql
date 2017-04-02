CREATE TABLE `stories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  `embargo` datetime DEFAULT NULL,
  `cart` int(10) unsigned DEFAULT NULL,
  `audiocredit` varchar(45) DEFAULT NULL,
  `addedby` varchar(45) NOT NULL,
  `addeddate` datetime NOT NULL,
  `text` text,
  `category` enum('NEWS','SPORT','SHOWBIZ','BUSINESS','BBC','PROSPECTS','PINNED') NOT NULL,
  `audioimported` tinyint(1) DEFAULT NULL,
  `audiofilename` varchar(128) DEFAULT NULL,
  `scriptused` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
