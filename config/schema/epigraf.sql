CREATE TABLE IF NOT EXISTS `databanks`
(
    `id`          int(11)      NOT NULL AUTO_INCREMENT,
    `created`     datetime                                                    DEFAULT NULL,
    `modified`    datetime                                                    DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `name`        varchar(200) NOT NULL COMMENT 'Name der Datenbank',
    `version`     varchar(5)   NOT NULL,
    `description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `published`   tinyint(4)                                                  DEFAULT NULL,
    `iriprovider` tinyint(4)   NOT NULL                                       DEFAULT 0 COMMENT 'Referenzdatenbank ja/nein',
    `iriprefix`   varchar(50)                                                 DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `name` (`name`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `docs`
(
    `id`         int(11)     NOT NULL AUTO_INCREMENT,
    `deleted`    tinyint(4)  NOT NULL DEFAULT 0,
    `version_id` int(11)              DEFAULT NULL,
    `created`    datetime             DEFAULT NULL,
    `modified`   datetime             DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `published`  tinyint(4)  NOT NULL DEFAULT 0,
    `segment`    char(10)             DEFAULT 'wiki',
    `sortkey`    varchar(50)          DEFAULT '',
    `name`       varchar(200)         DEFAULT NULL,
    `category`   varchar(300)         DEFAULT NULL,
    `content`    mediumtext                 DEFAULT NULL,
    `format`     varchar(50) NOT NULL DEFAULT 'html',
    `norm_iri`   varchar(50)          DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `norm_iri` (`norm_iri`) USING BTREE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `files`
(
    `id`          int(11)                                          NOT NULL AUTO_INCREMENT,
    `deleted`     int(11)                                          NOT NULL DEFAULT 0,
    `published`   int(11)                                          NOT NULL DEFAULT 0,
    `created`     datetime                                                  DEFAULT NULL,
    `modified`    datetime                                                  DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `name`        varchar(500) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    `description` mediumtext COLLATE utf8mb4_unicode_ci                              DEFAULT NULL,
    `type`        varchar(100) CHARACTER SET utf8 COLLATE utf8_bin          DEFAULT NULL,
    `size`        int(11)                                                   DEFAULT NULL,
    `root`        varchar(100) CHARACTER SET utf8 COLLATE utf8_bin          DEFAULT 'root',
    `path`        varchar(500) CHARACTER SET utf8 COLLATE utf8_bin          DEFAULT NULL,
    `isfolder`    tinyint(4)                                       NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `published` (`published`)
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `jobs`
(
    `id`          int(10) unsigned NOT NULL AUTO_INCREMENT,
    `created`     datetime                  DEFAULT NULL,
    `modified`    datetime                  DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `typ`         varchar(100)     NOT NULL COMMENT 'Typ',
    `status`      varchar(50)      NOT NULL COMMENT 'Status',
    `progress`    int(11)          NOT NULL DEFAULT 0,
    `progressmax` int(11)          NOT NULL DEFAULT 0,
    `config`      mediumtext                      DEFAULT NULL COMMENT 'Optionen',
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `permissions`
(
    `id`                 int(11) NOT NULL AUTO_INCREMENT,
    `created`            datetime     DEFAULT NULL,
    `modified`           datetime     DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `user_id`            int(11)      DEFAULT NULL,
    `user_session`       int(11)      DEFAULT NULL,
    `user_role`          varchar(50)  DEFAULT NULL,
    `user_request`       varchar(50)  DEFAULT NULL,
    `entity_type`        varchar(50)  DEFAULT NULL,
    `entity_name`        varchar(50)  DEFAULT NULL,
    `entity_id`          int(11)      DEFAULT NULL,
    `permission_type`    varchar(200) DEFAULT NULL COMMENT 'Name der Berechtigung',
    `permission_name`    varchar(200) DEFAULT NULL,
    `permission_expires` datetime     DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `entity_type_entity_id_permission_type` (`entity_type`, `entity_id`, `permission_type`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `pipelines` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`deleted` TINYINT(4) NOT NULL DEFAULT '0',
	`published` TINYINT(4) NULL DEFAULT NULL,
	`version_id` INT(11) NULL DEFAULT NULL,
	`created` DATETIME NULL DEFAULT NULL,
	`modified` DATETIME NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
	`created_by` INT(11) NULL DEFAULT NULL,
	`modified_by` INT(11) NULL DEFAULT NULL,
	`name` VARCHAR(200) NOT NULL COMMENT 'Name der Pipeline' COLLATE 'utf8mb4_general_ci',
	`norm_iri` VARCHAR(1500) NOT NULL COLLATE 'utf8mb4_general_ci',
	`description` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`tasks` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	PRIMARY KEY (`id`) USING BTREE
)
DEFAULT CHARSET = utf8mb4
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS `users` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`created` DATETIME NULL DEFAULT NULL,
	`modified` DATETIME NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
	`created_by` INT(11) NULL DEFAULT NULL,
	`modified_by` INT(11) NULL DEFAULT NULL,
	`lastaction` DATETIME NULL DEFAULT NULL,
	`username` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_general_ci',
	`password` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_general_ci',
	`email` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`name` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`acronym` VARCHAR(10) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`norm_iri` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`contact` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`accesstoken` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`role` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_general_ci',
	`databank_id` INT(11) NULL DEFAULT NULL,
	`pipeline_article_id` INT(11) NULL DEFAULT NULL,
	`pipeline_book_id` INT(11) NULL DEFAULT NULL,
	`settings` MEDIUMTEXT NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	PRIMARY KEY (`id`) USING BTREE,
	UNIQUE INDEX `Username` (`username`) USING BTREE,
	UNIQUE INDEX `iri` (`norm_iri`) USING BTREE
)
ENGINE=InnoDB
DEFAULT CHARSET = utf8mb4
COLLATE='utf8mb4_general_ci';


ALTER TABLE `databanks` DROP COLUMN `iriprovider`;
UPDATE databanks SET published = 1 WHERE name ='epi_public';

ALTER TABLE `databanks`
    ADD COLUMN `created_by` int(11) DEFAULT NULL AFTER modified,
   ADD COLUMN `modified_by` int(11) DEFAULT NULL AFTER `created_by`;

ALTER TABLE `docs`
    ADD COLUMN `created_by` int(11) DEFAULT NULL AFTER modified,
   ADD COLUMN `modified_by` int(11) DEFAULT NULL AFTER `created_by`;

ALTER TABLE `files`
    ADD COLUMN `created_by` int(11) DEFAULT NULL AFTER modified,
   ADD COLUMN `modified_by` int(11) DEFAULT NULL AFTER `created_by`;

ALTER TABLE `jobs`
    ADD COLUMN `created_by` int(11) DEFAULT NULL AFTER modified,
   ADD COLUMN `modified_by` int(11) DEFAULT NULL AFTER `created_by`;

ALTER TABLE `permissions`
    ADD COLUMN `created_by` int(11) DEFAULT NULL AFTER modified,
   ADD COLUMN `modified_by` int(11) DEFAULT NULL AFTER `created_by`;


ALTER TABLE `users`
    ADD COLUMN `created_by` int(11) DEFAULT NULL AFTER modified,
   ADD COLUMN `modified_by` int(11) DEFAULT NULL AFTER `created_by`;

    ALTER TABLE `docs`
        CHANGE COLUMN `norm_iri` `norm_iri` VARCHAR(1500) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `format`;

    ALTER TABLE `docs`
        ADD COLUMN `menu` TINYINT(4) NOT NULL DEFAULT 1 AFTER `published`;

    ALTER TABLE `files`
        ADD COLUMN `config` LONGTEXT NULL AFTER `description`;

ALTER TABLE `databanks`
	ADD COLUMN `category` VARCHAR(200) NULL AFTER `version`;
