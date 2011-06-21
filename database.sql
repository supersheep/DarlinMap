-- Adminer 3.2.2 MySQL dump

SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = 'SYSTEM';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `invite`;
CREATE TABLE `invite` (
  `uid` int(7) NOT NULL,
  `code` varchar(32) NOT NULL,
  `status` tinyint(4) NOT NULL,
  KEY `uid` (`uid`),
  CONSTRAINT `invite_ibfk_2` FOREIGN KEY (`uid`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `rel`;
CREATE TABLE `rel` (
  `from` int(7) NOT NULL,
  `to` int(7) NOT NULL,
  KEY `to` (`to`),
  KEY `from` (`from`),
  CONSTRAINT `rel_ibfk_3` FOREIGN KEY (`to`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rel_ibfk_4` FOREIGN KEY (`from`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `session`;
CREATE TABLE `session` (
  `sid` varchar(32) NOT NULL,
  `uid` varchar(32) NOT NULL,
  `ip` varchar(32) NOT NULL,
  `ua` varchar(255) NOT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `session` (`sid`, `uid`, `ip`, `ua`) VALUES
('34nanjrbqjb9brckdf349u3mh0',	'10',	'127.0.0.1',	'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/534.24 (KHTML, like Gecko) Chrome/11.0.696.14 Safari/534.24'),
('3onl29314qjahabke7fm9e39m3',	'10',	'127.0.0.1',	'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)'),
('3speubq5uu54rh4tg5ha38m1g4',	'14',	'127.0.0.1',	'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/534.24 (KHTML, like Gecko) Chrome/11.0.696.14 Safari/534.24'),
('nkiudi8l5ddgapufbd1cit3fd0',	'14',	'127.0.0.1',	'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/14.0.794.0 Safari/535.1');

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `refid` varchar(32) NOT NULL,
  `nickname` varchar(32) NOT NULL,
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `signature` varchar(255) NOT NULL DEFAULT '',
  `location` varchar(64) NOT NULL,
  `latlng` varchar(64) NOT NULL,
  `icon` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

INSERT INTO `user` (`id`, `username`, `password`, `refid`, `nickname`, `createtime`, `signature`, `location`, `latlng`, `icon`) VALUES
(10,	'2913e5fad3d830f97b3bb1f07b8892eb',	'2c8057b70828ef02',	'52114966',	'山下大芋',	'2011-06-06 13:09:09',	'',	'上海',	'31.230393,121.473704',	'http://img3.douban.com/icon/user_normal.jpg'),
(14,	'e66a671f1b336760b33f355d519a3620',	'9dda6bbe1a84c0d9',	'supersheep',	'山大芋',	'2011-06-06 14:18:00',	'跳进世界里去',	'上海',	'31.230393,121.473704',	'http://img3.douban.com/icon/u1594444-35.jpg');

-- 2011-06-22 07:10:46