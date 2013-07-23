CREATE TABLE IF NOT EXISTS `wp_xss` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `user` mediumtext CHARACTER SET utf8 NOT NULL COMMENT 'user name',
  `comment` mediumtext CHARACTER SET utf8 NOT NULL COMMENT 'comment',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'timestamp',
  PRIMARY KEY (`id`)
);