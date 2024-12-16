-- MariaDB dump 10.19  Distrib 10.5.18-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: mysql    Database: epigraf
-- ------------------------------------------------------
-- Server version	10.3.34-MariaDB-1:10.3.34+maria~focal


--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`
(
    `id`                  int(11) NOT NULL AUTO_INCREMENT,
    `created`             datetime     DEFAULT NULL,
    `modified`            datetime     DEFAULT current_timestamp() ON UPDATE current_timestamp (),
    `created_by`          int(11) DEFAULT NULL,
    `modified_by`         int(11) DEFAULT NULL,
    `lastaction`          datetime     DEFAULT NULL,
    `username`            varchar(50)  NOT NULL,
    `password`            varchar(255) NOT NULL,
    `email`            varchar(255) DEFAULT NULL,
    `name`            varchar(255) DEFAULT NULL,
    `acronym`            varchar(10) DEFAULT NULL,
    `norm_iri`            varchar(255) NOT NULL,
    `contact`             text         DEFAULT NULL,
    `accesstoken`         varchar(255) DEFAULT NULL,
    `role`                varchar(50)  NOT NULL,
    `databank_id`         int(11) DEFAULT NULL,
    `pipeline_article_id` int(11) DEFAULT NULL,
    `pipeline_book_id`    int(11) DEFAULT NULL,
    `settings`            text         DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=126 DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `databanks`
--

DROP TABLE IF EXISTS `databanks`;
CREATE TABLE `databanks`
(
    `id`          int(11) NOT NULL AUTO_INCREMENT,
    `created`     datetime    DEFAULT NULL,
    `modified`    datetime    DEFAULT current_timestamp() ON UPDATE current_timestamp (),
    `created_by`  int(11) DEFAULT NULL,
    `modified_by` int(11) DEFAULT NULL,
    `name`        varchar(200) NOT NULL COMMENT 'Name der Datenbank',
    `version`     varchar(5)   NOT NULL,
    `description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `published`   tinyint(4) DEFAULT NULL,
    `iriprefix`   varchar(50) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY           `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `docs`
--

DROP TABLE IF EXISTS `docs`;
CREATE TABLE `docs`
(
    `id`          int(11) NOT NULL AUTO_INCREMENT,
    `deleted`     tinyint(4) NOT NULL DEFAULT 0,
    `version_id`  int(11) DEFAULT NULL,
    `created`     datetime             DEFAULT NULL,
    `modified`    datetime             DEFAULT current_timestamp() ON UPDATE current_timestamp (),
    `created_by`  int(11) DEFAULT NULL,
    `modified_by` int(11) DEFAULT NULL,
    `published`   tinyint(4) NOT NULL DEFAULT 0,
    `menu`   tinyint(4) NOT NULL DEFAULT 1,
    `segment`     char(10)             DEFAULT 'wiki',
    `sortkey`     varchar(50)          DEFAULT '',
    `name`        varchar(200)         DEFAULT NULL,
    `category`    varchar(300)         DEFAULT NULL,
    `content`     text                 DEFAULT NULL,
    `format`      varchar(50) NOT NULL DEFAULT 'markdown',
    `norm_iri`    varchar(50)          DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY           `norm_iri` (`norm_iri`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=748 DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
CREATE TABLE `files`
(
    `id`          int(11) NOT NULL AUTO_INCREMENT,
    `deleted`     int(11) NOT NULL DEFAULT 0,
    `published`   int(11) DEFAULT 0,
    `created`     datetime                                         DEFAULT NULL,
    `modified`    datetime                                         DEFAULT current_timestamp() ON UPDATE current_timestamp (),
    `created_by`  int(11) DEFAULT NULL,
    `modified_by` int(11) DEFAULT NULL,
    `name`        varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    `description` text COLLATE utf8mb4_unicode_ci                     DEFAULT NULL,
    `type`        varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
    `size`        int(11) DEFAULT NULL,
    `root`        varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'root',
    `path`        varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
    `isfolder`    tinyint(4) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY           `published` (`published`)
) ENGINE=MyISAM AUTO_INCREMENT=75561 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs`
(
    `id`          int(10) unsigned NOT NULL AUTO_INCREMENT,
    `created`     datetime DEFAULT NULL,
    `modified`    datetime DEFAULT current_timestamp() ON UPDATE current_timestamp (),
    `created_by`  int(11) DEFAULT NULL,
    `modified_by` int(11) DEFAULT NULL,
    `typ`         varchar(100) NOT NULL COMMENT 'Typ',
    `status`      varchar(50)  NOT NULL COMMENT 'Status',
    `progress`    int(11) NOT NULL DEFAULT 0,
    `progressmax` int(11) NOT NULL DEFAULT 0,
    `config`      text     DEFAULT NULL COMMENT 'Optionen',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions`
(
    `id`                 int(11) NOT NULL AUTO_INCREMENT,
    `created`            datetime     DEFAULT NULL,
    `modified`           datetime     DEFAULT current_timestamp() ON UPDATE current_timestamp (),
    `created_by`         int(11) DEFAULT NULL,
    `modified_by`        int(11) DEFAULT NULL,
    `user_id`            int(11) DEFAULT NULL,
    `user_session`       int(11) DEFAULT NULL,
    `user_role`          varchar(50)  DEFAULT NULL,
    `user_request`       varchar(50)  DEFAULT NULL,
    `entity_type`        varchar(50)  DEFAULT NULL,
    `entity_name`        varchar(50)  DEFAULT NULL,
    `entity_id`          int(11) DEFAULT NULL,
    `permission_type`    varchar(200) DEFAULT NULL COMMENT 'Name der Berechtigung',
    `permission_name`    varchar(200) DEFAULT NULL,
    `permission_expires` datetime     DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY                  `user_id` (`user_id`),
    KEY                  `entity_type_entity_id_permission_type` (`entity_type`,`entity_id`,`permission_type`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `pipelines`
--

DROP TABLE IF EXISTS `pipelines`;
CREATE TABLE `pipelines`
(
    `id`          int(11) NOT NULL AUTO_INCREMENT,
    `deleted`     tinyint(4) NOT NULL DEFAULT 0,
    `published`   tinyint(4) DEFAULT NULL,
    `version_id`  int(11) DEFAULT NULL,
    `created`     datetime DEFAULT NULL,
    `modified`    datetime DEFAULT current_timestamp() ON UPDATE current_timestamp (),
    `created_by`  int(11) DEFAULT NULL,
    `modified_by` int(11) DEFAULT NULL,
    `name`        varchar(200) NOT NULL COMMENT 'Name der Pipeline',
    `description` text     DEFAULT NULL,
    `tasks`       text     DEFAULT NULL,
    `norm_iri`            varchar(255) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4;
