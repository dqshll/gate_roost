-- mysql -uroot -pe5cda60c7e

// fly doy
CREATE DATABASE stat DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `flip` (
  `id` int(11) AUTO_INCREMENT PRIMARY KEY,
  `user_id` varchar(16) DEFAULT NULL,
  `openid` varchar(128) NOT NULL,
  `nickname` varchar(128) DEFAULT NULL,
  `country` varchar(128) DEFAULT NULL,
  `city` varchar(128) DEFAULT NULL,
  `sex` varchar(2) NOT NULL DEFAULT '',
  `ts_barcode` TIMESTAMP,
  `ts_wait` TIMESTAMP,
  `ts_start` TIMESTAMP,
  `ts_end` TIMESTAMP,
  `ts_retry` TIMESTAMP,
  `duration` FLOAT NOT NULL DEFAULT '0',
  `score` int(8) NOT NULL DEFAULT '0',
  `quit` int(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

// jie you
CREATE DATABASE jy DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `upload` (
  `id` int(11) AUTO_INCREMENT PRIMARY KEY,
  `user_id` varchar(16) DEFAULT NULL,
  `openid` varchar(128) NOT NULL,
  `nickname` varchar(128) DEFAULT NULL,
  `country` varchar(128) DEFAULT NULL,
  `city` varchar(128) DEFAULT NULL,
  `sex` varchar(2) NOT NULL DEFAULT '',
  `pic_num` int(4) NOT NULL DEFAULT '0',
  `cur_time` TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

ALTER TABLE upload ADD `cur_time` TIMESTAMP;

// game total play record table
CREATE TABLE IF NOT EXISTS `play` (
  `id` int(11) AUTO_INCREMENT PRIMARY KEY,
  `user_id` varchar(16) DEFAULT NULL,
  `openid` varchar(128) NOT NULL,
  `screen_id` varchar(16) DEFAULT '0',
  `round_id` varchar(16) DEFAULT '0',
  `nickname` varchar(128) DEFAULT NULL,
  `thumb` varchar(512) DEFAULT NULL,
  `country` varchar(128) DEFAULT NULL,
  `city` varchar(128) DEFAULT NULL,
  `sex` varchar(2) NOT NULL DEFAULT '',
  `ts_barcode` TIMESTAMP,
  `ts_wait` TIMESTAMP,
  `ts_start` TIMESTAMP,
  `ts_end` TIMESTAMP,
  `ts_retry` TIMESTAMP,
  `duration` FLOAT NOT NULL DEFAULT '0',
  `score` int(8) NOT NULL DEFAULT '0',
  `quit` int(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

ALTER TABLE play ADD `thumb` varchar(512) DEFAULT NULL;

// app version table
CREATE TABLE IF NOT EXISTS `app_pub` (
  `id` int(11) AUTO_INCREMENT PRIMARY KEY,
  `app_name` varchar(16) DEFAULT NULL,
  `version` int(16) NOT NULL DEFAULT '0',
  `release_notes` varchar(512) NOT NULL DEFAULT '',
  `upload_time` TIMESTAMP,
  `file_path` varchar(512) DEFAULT NULL,
  `reserved` varchar(32) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

ALTER TABLE app_pub ADD `file_type` varchar(8) DEFAULT NULL;

// app version check table
CREATE TABLE IF NOT EXISTS `app_check` (
  `id` int(11) AUTO_INCREMENT PRIMARY KEY,
  `pcid` varchar(16) NOT NULL DEFAULT '',
  `py` varchar(16) DEFAULT NULL,
  `time` TIMESTAMP,
  `reserved` varchar(32) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

// game find play record table
CREATE TABLE IF NOT EXISTS `play_find` (
  `id` int(11) AUTO_INCREMENT PRIMARY KEY,
  `user_id` varchar(16) DEFAULT NULL,
  `openid` varchar(128) NOT NULL,
  `screen_id` varchar(16) DEFAULT '0',
  `round_id` varchar(16) DEFAULT '0',
  `nickname` varchar(128) DEFAULT NULL,
  `thumb` varchar(512) DEFAULT NULL,
  `country` varchar(128) DEFAULT NULL,
  `city` varchar(128) DEFAULT NULL,
  `sex` varchar(2) NOT NULL DEFAULT '',
  `ts_barcode` TIMESTAMP,
  `ts_wait` TIMESTAMP,
  `ts_start` TIMESTAMP,
  `ts_end` TIMESTAMP,
  `ts_retry` TIMESTAMP,
  `duration` FLOAT NOT NULL DEFAULT '0',
  `score` int(8) NOT NULL DEFAULT '0',
  `quit` int(1) NOT NULL DEFAULT '0',
  `play_detail_json` varchar(512) DEFAULT NULL,
  `register` int(1) NOT NULL DEFAULT '0',
  `award_draw` int(4) NOT NULL DEFAULT '0',
  `award_level` int(4) NOT NULL DEFAULT '0',
  `award_receive` varchar(128) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE play_find ADD `xxx` varchar(512) DEFAULT NULL;

// new remote visitor
CREATE USER 'tom'@'localhost' IDENTIFIED BY 'Ed1s0nX';

GRANT ALL PRIVILEGES ON *.* TO 'tom'@'localhost' WITH GRANT OPTION;

CREATE USER 'tom'@'%' IDENTIFIED BY 'Ed1s0nX';

GRANT ALL PRIVILEGES ON *.* TO 'tom'@'%' WITH GRANT OPTION;

FLUSH PRIVILEGES;

/* 这是注释 */