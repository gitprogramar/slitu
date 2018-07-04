CREATE TABLE IF NOT EXISTS `#__rokquickcart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `description` text,
  `price` decimal(7,2) NOT NULL,
  `shipping` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL,
  `ordering` int(11) NOT NULL,
  `params` text,
  `access` int(11) NOT NULL DEFAULT '1',
  `asset_id` INT(10)     NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
);

