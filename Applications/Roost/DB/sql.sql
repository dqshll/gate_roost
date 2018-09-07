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

ALTER TABLE sheets ADD `desc` varchar(128) DEFAULT NULL;

// new remote visitor
CREATE USER 'tom'@'localhost' IDENTIFIED BY 'Ed1s0nX';

GRANT ALL PRIVILEGES ON *.* TO 'tom'@'localhost' WITH GRANT OPTION;

CREATE USER 'tom'@'%' IDENTIFIED BY 'Ed1s0nX';

GRANT ALL PRIVILEGES ON *.* TO 'tom'@'%' WITH GRANT OPTION;

FLUSH PRIVILEGES;

CREATE TABLE flydog
(
  id               INT AUTO_INCREMENT
    PRIMARY KEY,
  user_id          VARCHAR(16)                             NULL,
  openid           VARCHAR(128)                            NOT NULL,
  screen_id        VARCHAR(16) DEFAULT '0'                 NULL,
  round_id         VARCHAR(16) DEFAULT '0'                 NULL,
  nickname         VARCHAR(128)                            NULL,
  thumb            VARCHAR(512)                            NULL,
  country          VARCHAR(128)                            NULL,
  city             VARCHAR(128)                            NULL,
  sex              VARCHAR(2) DEFAULT ''                   NOT NULL,
  ts_barcode       TIMESTAMP DEFAULT CURRENT_TIMESTAMP     NOT NULL,
  ts_wait          TIMESTAMP DEFAULT '0000-00-00 00:00:00' NOT NULL,
  ts_start         TIMESTAMP DEFAULT '0000-00-00 00:00:00' NOT NULL,
  ts_end           TIMESTAMP DEFAULT '0000-00-00 00:00:00' NOT NULL,
  ts_retry         TIMESTAMP DEFAULT '0000-00-00 00:00:00' NOT NULL,
  duration         FLOAT DEFAULT '0'                       NOT NULL,
  score            INT(8) DEFAULT '0'                      NOT NULL,
  quit             INT(1) DEFAULT '0'                      NOT NULL,
  play_detail_json VARCHAR(512)                            NULL,
  register         INT(1) DEFAULT '0'                      NOT NULL,
  award_draw       INT(4) DEFAULT '0'                      NOT NULL,
  award_level      INT(4) DEFAULT '0'                      NOT NULL,
  award_receive    VARCHAR(128)                            NULL,
  host_ip          VARCHAR(30)                             NULL,
  click_num        INT                                     NULL,
  right_click      INT                                     NULL,
  pcid             INT                                     NULL
)ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE egypt
(
  id               INT AUTO_INCREMENT
    PRIMARY KEY,
  user_id          VARCHAR(16)                             NULL,
  openid           VARCHAR(128)                            NOT NULL,
  screen_id        VARCHAR(16) DEFAULT '0'                 NULL,
  round_id         VARCHAR(16) DEFAULT '0'                 NULL,
  nickname         VARCHAR(128)                            NULL,
  thumb            VARCHAR(512)                            NULL,
  country          VARCHAR(128)                            NULL,
  city             VARCHAR(128)                            NULL,
  sex              VARCHAR(2) DEFAULT ''                   NOT NULL,
  ts_barcode       TIMESTAMP DEFAULT CURRENT_TIMESTAMP     NOT NULL,
  ts_wait          TIMESTAMP DEFAULT '0000-00-00 00:00:00' NOT NULL,
  ts_start         TIMESTAMP DEFAULT '0000-00-00 00:00:00' NOT NULL,
  ts_end           TIMESTAMP DEFAULT '0000-00-00 00:00:00' NOT NULL,
  ts_retry         TIMESTAMP DEFAULT '0000-00-00 00:00:00' NOT NULL,
  duration         FLOAT DEFAULT '0'                       NOT NULL,
  score            INT(8) DEFAULT '0'                      NOT NULL,
  quit             INT(1) DEFAULT '0'                      NOT NULL,
  play_detail_json VARCHAR(512)                            NULL,
  register         INT(1) DEFAULT '0'                      NOT NULL,
  award_draw       INT(4) DEFAULT '0'                      NOT NULL,
  award_level      INT(4) DEFAULT '0'                      NOT NULL,
  award_receive    VARCHAR(128)                            NULL,
  host_ip          VARCHAR(30)                             NULL,
  click_num        INT                                     NULL,
  right_click      INT                                     NULL,
  pcid             INT                                     NULL,
  lat              FLOAT DEFAULT '0'                       NULL,
  lng              FLOAT DEFAULT '0'                       NULL
)ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE stat
(
  id               INT AUTO_INCREMENT PRIMARY KEY,
  pcid         VARCHAR(8)                         NOT   NULL,
  cnmid         VARCHAR(8)      NOT NULL,

  start_time       TIMESTAMP,
  end_time         TIMESTAMP,
  duration        INT(10)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE sheets
(
  sid               INT AUTO_INCREMENT PRIMARY KEY,
  name         VARCHAR(128)                            NULL,

  create_time      TIMESTAMP DEFAULT CURRENT_TIMESTAMP     NOT NULL,
  start_time       TIMESTAMP NOT NULL,
  end_time         TIMESTAMP,
  cinema_ids       VARCHAR(128)  DEFAULT NULL ,
  pc_ids           VARCHAR(128)  DEFAULT NULL ,
  programs         VARCHAR(256)  DEFAULT NULL
)ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE programs
(
  pid               INT AUTO_INCREMENT PRIMARY KEY,
  name         VARCHAR(128)                            NULL,

  update_time timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

  type             INT(4) DEFAULT NULL,
  duration         INT(4) DEFAULT '0',
  url            VARCHAR(256)  DEFAULT NULL
)ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE banner
(
  id               INT AUTO_INCREMENT PRIMARY KEY,
  name         VARCHAR(32)                            NULL,
  description         VARCHAR(128)                            NULL,
  create_time      TIMESTAMP DEFAULT CURRENT_TIMESTAMP     NOT NULL,
  start_time       TIMESTAMP NOT NULL,
  end_time         TIMESTAMP,
  cinema_id       VARCHAR(64)  DEFAULT NULL,
  game        VARCHAR(32)  DEFAULT NULL,
)ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE banner ADD `img_url` varchar(256) DEFAULT NULL;
ALTER TABLE banner ADD `text` varchar(256) DEFAULT NULL;