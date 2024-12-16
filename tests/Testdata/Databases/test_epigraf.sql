-- MariaDB dump 10.19  Distrib 10.5.18-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: mysql    Database: epigraf
-- ------------------------------------------------------
-- Server version	10.3.34-MariaDB-1:10.3.34+maria~focal

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `databanks`
--

DROP TABLE IF EXISTS `databanks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `docs`
--

DROP TABLE IF EXISTS `docs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=748 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;

CREATE TABLE `files` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`deleted` INT(11) NOT NULL DEFAULT '0',
	`published` INT(11) NOT NULL DEFAULT '0',
	`created` DATETIME NULL DEFAULT NULL,
	`modified` DATETIME NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
	`created_by` INT(11) NULL DEFAULT NULL,
	`modified_by` INT(11) NULL DEFAULT NULL,
	`name` VARCHAR(500) NOT NULL COLLATE 'utf8mb4_general_ci',
	`description` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`config` LONGTEXT NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`type` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`size` INT(11) NULL DEFAULT NULL,
	`root` VARCHAR(100) NULL DEFAULT 'root' COLLATE 'utf8mb4_general_ci',
	`path` VARCHAR(500) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`isfolder` TINYINT(4) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`) USING BTREE,
	INDEX `published` (`published`) USING BTREE
) COLLATE='utf8mb4_general_ci' ENGINE=MyISAM AUTO_INCREMENT=75561;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=46315 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=73978 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pipelines`
--

DROP TABLE IF EXISTS `pipelines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
    `email`            varchar(255) NOT NULL,
    `name`            varchar(255) NOT NULL,
    `acronym`            varchar(10) NOT NULL,
    `norm_iri`            varchar(255) NOT NULL,
    `contact`             text         DEFAULT NULL,
    `accesstoken`         varchar(255) DEFAULT NULL,
    `role`                varchar(50)  NOT NULL,
    `databank_id`         int(11) DEFAULT NULL,
    `pipeline_article_id` int(11) DEFAULT NULL,
    `pipeline_book_id`    int(11) DEFAULT NULL,
    `settings`            text         DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=126 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-03-28 20:11:03


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

INSERT INTO `databanks` (`id`, `created`, `modified`, `created_by`, `modified_by`, `name`, `version`, `description`,
                         `published`, `iriprefix`)
VALUES
       (43, '2021-12-29 10:45:06', '2023-02-11 23:57:47', NULL, NULL, 'test_projects', '4.5', NULL, 1, 'projects');

INSERT INTO `permissions` (`id`, `created`, `modified`, `created_by`, `modified_by`, `user_id`, `user_session`,
                           `user_role`, `user_request`, `entity_type`, `entity_name`, `entity_id`, `permission_type`,
                           `permission_name`, `permission_expires`)
VALUES (5460, '2019-11-17 11:19:36', '2022-11-15 23:55:36', NULL, NULL, 121, NULL, NULL, NULL, 'databank', 'test_projects',
        43, 'access', NULL, NULL),
       (71896, '2022-12-27 22:48:40', '2022-12-27 22:48:40', NULL, NULL, 121, NULL, NULL, 'api', 'databank', 'test_projects',
        NULL, 'access', 'types/index', NULL),
       (72774, '2023-02-13 13:03:48', '2023-02-13 14:07:41', NULL, NULL, 121, NULL, NULL, 'api', 'databank', 'test_projects',
        58, 'access', 'articles/index', NULL),
       (73958, '2023-04-03 10:54:58', '2023-04-03 10:54:58', NULL, NULL, 121, NULL, NULL, 'api', 'databank', 'test_projects',
        NULL, 'access', 'articles/import', NULL),
       (73959, '2023-04-03 10:55:14', '2023-04-03 10:55:14', NULL, NULL, 121, NULL, NULL, 'api', 'databank', 'epigraf',
        NULL, 'access', 'jobs/execute', NULL);

INSERT INTO `pipelines` (`id`, `deleted`, `published`, `version_id`, `created`, `modified`, `created_by`, `modified_by`,
                         `name`, `description`, `tasks`)
VALUES
       (16, 0, NULL, NULL, '2019-11-29 09:11:26', '2022-12-27 23:03:04', NULL, NULL, 'Rohdaten',
        'Ausgabe der Daten im XML-Format ohne zusätzliche Transformation',
        '[{"number":"1","type":"options","data":{"objects":"1","index":"1","text":"1"},"outputfile":""},{"number":"2","type":"data_projects","outputfile":""},{"number":"3","type":"data_articles","articletype":"epi-book","outputfile":""},{"number":"4","type":"data_articles","articletype":"object, dio-article","outputfile":""},{"number":"5","type":"data_index","outputfile":""},{"number":"6","type":"bundle","prefix":"<?xml version=\\"1.0\\" encoding=\\"UTF-8\\" standalone=\\"yes\\"?>\\r\\n<book>","postfix":" \\r\\n<\\/book>","outputfile":""},{"inputfile":"","number":"7","type":"save","extension":"xml","download":"1"}]'),
       (19, 0, NULL, NULL, '2021-07-09 07:14:23', '2023-02-10 19:45:01', NULL, NULL, 'Artikel',
        'Ausgabe des Inschriftenkatalgs, wahlweise mit Registern',
        '[{"number":"1","type":"options","data":{"objects":"1","index":"1","text":"0"},"options":[{"number":"1","output":"1","category":"Allgemeines","label":"Signatur anzeigen","key":"signature","radio":"0","value":""},{"number":"2","output":"1","category":"Allgemeines","label":"Notizen","key":"notes","radio":"0","value":""},{"number":"3","output":"0","category":"Allgemeines","label":"Letzte \\u00c4nderung","key":"modified","radio":"0","value":""},{"number":"4","output":"0","category":"Register","label":"Register","key":"indices","radio":"0","value":""},{"number":"5","output":"0","category":"Register","label":"Literatur und Quellen","key":"biblio","radio":"0","value":""},{"number":"6","output":"0","category":"Standorte-Register","label":"Basis-Standort anzeigen","key":"base_location","radio":"0","value":""},{"number":"7","output":"1","category":"Ligaturen","label":"unterstreichen","key":"ligature_arcs","radio":"1","value":""},{"number":"8","output":"0","category":"Ligaturen","label":"Ligaturb\\u00f6gen","key":"ligature_arcs","radio":"1","value":"1"},{"number":"9","output":"0","category":"Marken","label":"zu einer Kategorie zusammenfassen","key":"marks_together","radio":"0","value":""},{"number":"10","output":"0","category":"Ausgabemodus","label":"M\\u00fcnchener Reihe","key":"modus","radio":"1","value":"projects_bay"},{"number":"11","output":"1","category":"Ausgabemodus","label":"alle anderen Reihen","key":"modus","radio":"1","value":"projects_all"}],"outputfile":""},{"number":"2","type":"data_projects","outputfile":""},{"number":"3","type":"data_articles","articletype":"epi-book","outputfile":""},{"number":"4","type":"data_articles","articletype":"epi-article","outputfile":""},{"number":"5","type":"data_index","outputfile":""},{"number":"6","type":"bundle","prefix":"<?xml version=\\"1.0\\" encoding=\\"UTF-8\\" standalone=\\"yes\\"?>\\r\\n<book>","postfix":" \\r\\n<\\/book>","outputfile":""},{"inputfile":"","number":"7","type":"transformxsl","xslfile":"templates\\/epi-trans0.xsl","processor":"saxon","outputfile":""},{"inputfile":"","number":"8","type":"transformxsl","xslfile":"templates\\/epi-trans1.xsl","processor":"saxon","outputfile":""},{"inputfile":"","number":"9","type":"transformxsl","xslfile":"templates\\/epi-trans2.xsl","processor":"saxon","outputfile":""},{"inputfile":"","number":"10","type":"replace","replacefile":"templates\\/epi-replace-whitespace.txt","outputfile":""},{"inputfile":"","number":"11","type":"transformxsl","xslfile":"templates\\/epi-trans-word1.xsl","processor":"saxon","outputfile":""},{"inputfile":"","number":"12","type":"save","extension":"doc","download":"1"}]'),
       (21, 0, NULL, NULL, '2021-12-18 11:51:34', '2023-01-15 11:55:06', NULL, NULL, 'Band',
        'Ausgabe eines Inschriftenbandes in Word',
        '[{"number":"1","type":"options","data":{"objects":"1","index":"1","text":"1"},"options":[{"number":"1","output":"1","category":"Allgemeines","label":"Signatur","key":"signature","radio":"0","value":""},{"number":"2","output":"0","category":"Allgemeines","label":"Notizen","key":"notes","radio":"0","value":""},{"number":"3","output":"0","category":"Allgemeines","label":"Letzte \\u00c4nderung","key":"modified","radio":"0","value":""},{"number":"4","output":"0","category":"Band","label":"Titelei","key":"preliminaries","radio":"0","value":""},{"number":"5","output":"0","category":"Band","label":"Inhaltsverzeichnis","key":"table_of_content","radio":"0","value":""},{"number":"6","output":"0","category":"Band","label":"Vorwort","key":"prefaces","radio":"0","value":""},{"number":"7","output":"0","category":"Band","label":"Einleitung","key":"introduction","radio":"0","value":""},{"number":"8","output":"1","category":"Band","label":"Katalog der Inschriften","key":"articles","radio":"0","value":""},{"number":"9","output":"0","category":"Band","label":"Chronologische Liste der Inschriften","key":"table_of_inscriptions","radio":"0","value":""},{"number":"10","output":"0","category":"Band","label":"Register","key":"indices","radio":"0","value":""},{"number":"11","output":"0","category":"Band","label":"Abk\\u00fcrzungen","key":"abbreviations","radio":"0","value":""},{"number":"12","output":"0","category":"Band","label":"Quellen und Literatur","key":"biblio","radio":"0","value":""},{"number":"13","output":"0","category":"Band","label":"Liste der bisher erschienenen DI-B\\u00e4nde","key":"di-volumes","radio":"0","value":""},{"number":"14","output":"0","category":"Band","label":"Zeichnungen","key":"drawings","radio":"0","value":""},{"number":"15","output":"0","category":"Band","label":"Marken","key":"marks","radio":"0","value":""},{"number":"16","output":"0","category":"Band","label":"Bildtafeln","key":"plates","radio":"0","value":""},{"number":"17","output":"0","category":"Standorte-Register","label":"Basis-Standort anzeigen","key":"base_location","radio":"0","value":""},{"number":"18","output":"0","category":"Fu\\u00dfnoten","label":"Nummern rechtsb\\u00fcndig","key":"footnotes","radio":"0","value":""},{"number":"19","output":"1","category":"Ligaturen","label":"unterstreichen","key":"ligature_arcs","radio":"1","value":""},{"number":"20","output":"0","category":"Ligaturen","label":"Ligaturb\\u00f6gen","key":"ligature_arcs","radio":"1","value":"1"},{"number":"21","output":"0","category":"Marken","label":"zu einer Kategorie zusammenfassen ","key":"marks_together ","radio":"0","value":""},{"number":"22","output":"0","category":"Sonstiges","label":"Sortierung nach Signatur","key":"sort_signatures","radio":"0","value":""},{"number":"23","output":"0","category":"Quellen und Literatur","label":"DI-B\\u00e4nde ausblenden","key":"di_volumes_hide","radio":"0","value":""}],"outputfile":""},{"number":"2","type":"data_projects","outputfile":""},{"number":"3","type":"data_articles","articletype":"epi-book","outputfile":""},{"number":"4","type":"data_articles","articletype":"epi-article","outputfile":""},{"number":"5","type":"data_index","outputfile":""},{"number":"6","type":"bundle","prefix":"<?xml version=\\"1.0\\" encoding=\\"UTF-8\\" standalone=\\"yes\\"?>\\r\\n<book>","postfix":" \\r\\n<\\/book>","outputfile":""},{"inputfile":"","number":"7","type":"transformxsl","xslfile":"templates\\/epi-trans0.xsl","processor":"saxon","outputfile":""},{"inputfile":"","number":"8","type":"transformxsl","xslfile":"templates\\/epi-trans1.xsl","processor":"saxon","outputfile":""},{"inputfile":"","number":"9","type":"transformxsl","xslfile":"templates\\/epi-trans2.xsl","processor":"saxon","outputfile":""},{"inputfile":"","number":"10","type":"replace","replacefile":"templates\\/epi-replace-whitespace.txt","outputfile":""},{"inputfile":"","number":"11","type":"transformxsl","xslfile":"templates\\/epi-trans-word1.xsl","processor":"saxon","outputfile":""},{"inputfile":"","number":"12","type":"replace","replacefile":"templates\\/epi-silbentrennung.txt","outputfile":""},{"inputfile":"","number":"13","type":"transformxsl","xslfile":"templates\\/epi-trans-word2.xsl","processor":"php","outputfile":""},{"inputfile":"","number":"14","type":"replace","replacefile":"templates\\/epi-contract-numbers.txt","outputfile":""},{"inputfile":"","number":"15","type":"save","extension":"doc","download":"1"}]'),
       (28, 0, NULL, NULL, '2022-11-11 17:13:22', '2022-12-27 23:06:56', NULL, NULL, 'Diodaten', 'Ausgabe DIO-Rohdaten',
        '[{"number":"1","type":"options","data":{"objects":"1","index":"1","text":"1"},"outputfile":""},{"number":"2","type":"data_projects","outputfile":""},{"number":"3","type":"data_articles","articletype":"epi-book","outputfile":""},{"number":"4","type":"data_articles","articletype":"dio-article","outputfile":""},{"number":"5","type":"data_index","outputfile":""},{"number":"6","type":"bundle","prefix":"<?xml version=\\"1.0\\" encoding=\\"UTF-8\\" standalone=\\"yes\\"?>\\r\\n<book>","postfix":" \\r\\n<\\/book>","outputfile":""},{"inputfile":"","number":"7","type":"save","extension":"xml","download":"1"}]');

INSERT INTO `users` (`id`, `created`, `modified`, `created_by`, `modified_by`, `lastaction`, `username`, `password`,
                     `contact`, `accesstoken`, `role`, `databank_id`, `pipeline_article_id`, `pipeline_book_id`,
                     `settings`,`norm_iri`)
VALUES
       (121, '2023-04-01 13:52:33', '2023-04-05 23:40:38', NULL, NULL, '2023-04-05 23:40:38', 'devel',
        '$2y$10$6HYzKCe2AlUKVwaHjNXbIOy876pb8dnwICbxOfYGc1yjp3M2FGSUi', 'Automatically created user',
        'M15C0I6W9NYT0P0XTP1L', 'devel', NULL, NULL, NULL,NULL,'devel'),
       (122, '2023-04-01 13:52:34', '2023-04-01 13:52:34', NULL, NULL, NULL, 'admin',
        '$2y$10$cRD9/9he9426Pdnl3C2uouO4XgZ4HedULryiNZnWGB4XfgPxjQ2s.', 'Automatically created user',
        '22IF0BZRW8N31U34YRFS', 'admin', NULL, NULL, NULL, NULL,'admin'),
       (123, '2023-04-01 13:52:34', '2023-04-01 13:52:34', NULL, NULL, NULL, 'editor',
        '$2y$10$/yQQkhdWUR3njoRqwUxQTeyeNtBiAYmjsudOq.RSVcbqdfdtasQry', 'Automatically created user',
        'IF4GX9LX7U8H9HDR9Q00', 'editor', NULL, NULL, NULL, NULL,'editor'),
       (124, '2023-04-01 13:52:34', '2023-04-01 13:52:34', NULL, NULL, NULL, 'author',
        '$2y$10$qVQKF2GtQZvtJGTNtbdcJucMrDM9IscgNbANr2zIEY5TKupuZyqRO', 'Automatically created user',
        'QWMM0MXH8WNZHM2QP9I', 'author', NULL, NULL, NULL, NULL,'author'),
       (125, '2023-04-01 13:52:34', '2023-04-01 13:52:34', NULL, NULL, NULL, 'reader',
        '$2y$10$kNw2/DB9LXUe4Ei.c90iReBS1cMUWG.KwztZOHEnUNzX1gwQl.oKC', 'Automatically created user',
        '1VGPM4EUZ99F2JZCP79W', 'reader', NULL, NULL, NULL, NULL,'reader');

--
-- Docs exported using the following criteria:
-- (segment = 'help' AND deleted=0 AND (category LIKE 'A%' OR category LIKE 'B%')) OR
-- (segment='wiki' AND deleted=0 AND (name='Wiki') OR (category='G. Dokumentation')) OR
-- (segment='pages' AND deleted=0)
--

INSERT INTO `docs` VALUES (519, 0, NULL, '2022-09-10 13:46:13', '2022-09-10 13:47:41', NULL, NULL, 0, 1, 'help', '', 'Wiki', 'B. Dateneingabe / 8. Wiki', '', 'html', '');
INSERT INTO `docs` VALUES (541, 0, NULL, '2022-09-20 15:40:39', '2022-09-20 20:02:17', NULL, NULL, 1, 1, 'help', '', 'Anwendung', 'A. Überblick / 2. Anwendung', '', 'html', '');
INSERT INTO `docs` VALUES (468, 0, NULL, '2022-09-09 13:37:37', '2022-09-10 13:39:41', NULL, NULL, 1, 1, 'help', '', 'Überblick', 'A. Überblick', '<p class="infobox">An dieser Stelle entsteht die öffentliche Dokumentation für Epigraf 5 (<i>work in progress</i>).<br><br>Die <a href="/help/start-epi4">Hilfeseiten für Epigraf 4 </a>sind weiterhin für registrierte Nutzer:innen zugänglich.</p>', 'html', 'start');
INSERT INTO `docs` VALUES (469, 0, NULL, '2022-09-09 13:38:29', '2022-09-09 22:33:24', NULL, NULL, 1, 1, 'help', '3', 'Datenmodell', 'A. Überblick / 3. Datenmodell', '', 'html', 'epiweb-model');
INSERT INTO `docs` VALUES (470, 0, NULL, '2022-09-09 13:38:43', '2022-09-20 19:55:41', NULL, NULL, 1, 1, 'help', '1', 'Module', 'A. Überblick / 1. Module', '<p>Die Module von Epigraf unterstützen den gesamten Forschungsdatenlebenszyklus (<a href="https://doi.org/10.2218/ijdc.v3i1.48"><span>Higgins 2008</span></a><span>)</span>:</p><figure class="image"><img src="	/files/display/74462?format=thumb&amp;size=800"></figure><p>&nbsp;</p><ul><li class="MsoListParagraph"><strong>Kollaboration:</strong> Epigraf unterstützt die Koordination mehrerer Arbeitsstellen. Jede Arbeitsstelle arbeitet in einer eigenen Datenbank, für die Koordination werden Wikis und ein Dateirepositorium eingesetzt. Die einzelnen Datenbanken werden schließlich in einer gemeinsamen Referenzdatenbank zusammengeführt.</li><li class="MsoListParagraph"><strong>Datenerfassung:</strong> Der Kern der Anwendung besteht in Funktionen zur Beschreibung von Objekten und Texten – für jedes Objekt wird ein Artikel erstellt. Artikel und weitere Datensätze können sowohl in der Anwendung angelegt als auch importiert werden.&nbsp;&nbsp;<br><o:p></o:p></li><li class="MsoListParagraph"><strong>Annotation:</strong> Jeder Artikel setzt sich aus flexibel kombinierbaren Abschnitten zusammen, in denen der Text und alle relevanten Metadaten (Beschreibungen, Kommentare, Kategorisierungen über Vokabulare) sowie zugehörige Dateien bzw. Bilder enthalten sind. Für die Annotation von Texten steht eine projektspezifisch konfigurierbare Toolbar zur Verfügung.&nbsp;</li><li class="MsoListParagraph"><strong>Analyse: </strong>Der Gesamtbestand ist im Volltext durchsuchbar und die zur Erschließung verwendeten Vokabulare können als Rechercheinstrument eingesetzt werden. Durch die tabellarische Erfassung lassen sich auf den mittels der API exportierten Daten statistische Verfahren wie Clusteranalysen durchführen.</li><li class="MsoListParagraph"><strong>Vernetzung:</strong> Um die Daten später als Linked Open Data nach den FAIR-Prinzipien (<a href="https://doi.org/10.1038/sdata.2016.18">Wilkinson et al. 2016</a>) zu veröffentlichen, können für jeden Artikel und jede Kategorie Normdatenbezeichner (IRIs; <a href="https://www.w3.org/TR/rdf11-concepts/">W3C 2014</a>) erstellt werden. Dadurch lassen sich auch Datenbestände zwischen verschiedenen Epigrafdatenbanken abgleichen. Das Datenmodell von Epigraf ist darüber hinaus mit dem Resource Description Framework (<a href="https://www.w3.org/TR/rdf11-concepts/">W3C 2014</a>) kompatibel, so dass die Beziehungen zwischen Datenpunkten in der Form von Aussagen modellierbar sind.</li><li class="MsoListParagraph"><strong>Publikation: </strong>Über ein Pipeline-System werden die erfassten Daten mittels XSL-Stylesheets in Formate wie Word-Dateien konvertiert, die nach der Endredaktion etwa als Druckvorlage an einen Verlag übergeben werden können. Dieser klassische Publikationsworkflow wird durch Epigraf zunehmend erweitert, so dass über eine Programmierschnittstelle CSV-, JSON- oder XML-Daten sowie standardisierte Dokumentformate wie TEI (<a href="https://tei-c.org/">TEI 2022</a>; <a href="https://epidoc.stoa.org/gl/latest/">Elliott et al. 2020</a>) ausgegeben werden.&nbsp;<o:p></o:p></li></ul>', 'html', 'epiweb-modules');
INSERT INTO `docs` VALUES (472, 0, NULL, '2022-09-09 13:39:07', '2022-09-20 20:17:30', NULL, NULL, 1, 1, 'help', '', 'Dateneingabe', 'B. Dateneingabe', '<p>Ausgangspunkt der Datenerfassung mit Epigraf sind Artikel. Was genau unter einem Artikel verstanden wird, hängt vom <a href="/help/epiweb-usecases">Anwendungsfall</a> ab. Geht es um die Erschließung von historischen Inschriften, dann enthält ein Artikel die Beschreibung des Objekts und der darauf angebrachten Inschrift. Bei einer Briefedition würde jeder Brief als Artikel erfasst werden. Für die Social-Media-Analyse bietet es sich an, einen Thread (bestehend aus einem Post und dazugehörigen Kommentaren) als einzelnen Artikel zu erfassen. Ein Artikel enthält somit die Beschreibung des Analyseobjekts.</p><p><strong>Artikel </strong>und alle dazu gehörigen Daten können entweder aus anderen Datenbeständen <a href="/help/epiweb-import">importiert</a> oder direkt in der Oberfläche <a href="/help/epiweb-epi-articles-view">angelegt und bearbeitet</a> werden. Zu einem Artikel gehören:</p><ul><li><strong>Projekte:</strong> Mehrere Artikel werden zu <a href="/help/epiweb-epi-projects-index">Projekten</a> zusammengefasst. Ein Projekt umfasst beispielsweise die Inschriften einer Region oder ein Sample von Threads auf Social-Media-Plattformen.</li><li><strong>Kategorien:</strong> Neben Textabschnitten können in den Artikeln standardisierte Vokabulare zur <a href="/help/epiweb-annotations">Annotation</a> oder zur <a href="/help/epiweb-epi-properties-index">Kategorisierung</a> der Inhalte verwendet werden.</li><li><strong>Dateien:</strong> Um Bilder oder Dokumente in die Artikel oder Notizen einzubinden, werden sie <a href="/help/epiweb-epi-files-index">zunächst hochgeladen</a> und dann in den Artikel eingebunden.</li><li><strong>Geodaten:</strong> Sofern sich die Artikel auf bestimmte Orte beziehen, können sie in einer Karte dargestellt werden. Dazu werden im Artikel die <a href="/help/epiweb-geodata">Geokoordinaten</a> erfasst.</li></ul><p>In den datenbankspezifischen <a href="/help/epiweb-epi-notes-index">Notizen</a> und im datenbankübergreifenden <a href="/docs/view/help/519">Wiki</a> werden projektorganisatorische Dinge festgehalten, beispielsweise ein gemeinsamer Leitfaden zur Edition oder Kodierung der Inhalte.</p>', 'html', 'epiweb-editing');
INSERT INTO `docs` VALUES (473, 0, NULL, '2022-09-09 13:39:43', '2022-09-09 13:39:43', NULL, NULL, 1, 1, 'help', '1', 'Projekte', 'B. Dateneingabe / 1. Projekte', '', 'html', 'epiweb-epi-projects-index');
INSERT INTO `docs` VALUES (474, 0, NULL, '2022-09-09 13:39:56', '2023-02-13 20:50:06', NULL, NULL, 1, 1, 'help', '2', 'Artikel', 'B. Dateneingabe / 2. Artikel ', '<p class="infobox">Dieser Abschnitt wird noch überarbeitet!</p><p>Zur schnellen Überarbeitung mehrerer Artikel kann die Coding-Ansicht verwendet werden. Sie wird in der Artikelliste über die Schaltfläche im Fußbereich oder über den URL-Parameter template=coding aktiviert. In der Coding-Ansicht wird ein Artikel in der rechte Sidebar angezeigt und in den Bearbeitungsmodus geschaltet - erkennbar an der Schaltflächen zum Speichern unten in der Sidebar.</p><p>Welche Felder bearbeitbar sind, lässt sich gezielt über die Konfiguration angeben. Um beispielsweise nur die Geokoordinaten eines Artikels freizugeben, werden die folgenden Einstellungen vorgenommen.<br><br><strong>Typkonfiguration des Artikels</strong></p><p>Der Artikel wird insgesamt auf nicht bearbeitbar geschaltet (edit=false) und die Abschnitte mit den Geokodierungen und mit den Beschreibungen durch negative Gewichte an den Anfang des Artikels gestellt.&nbsp;</p><pre><code class="language-plaintext">  "templates": {\r\n        "coding": {\r\n            "edit": false,\r\n            "sections": {\r\n                "locations": {\r\n                    "weight": -300\r\n                },\r\n                "text[Beschreibung]": {\r\n                    "weight": -100\r\n                }\r\n            }\r\n        }\r\n    }</code></pre><p>&nbsp;</p><p>Optional können einzelne Abschnitte mit der Angabe fixed=true direkt oberhalb des Speicherbuttons fixiert werden, um sie schneller zugänglich zu machen.</p><p>&nbsp;</p><p><strong>Typkonfiguration der Items</strong></p><p>Einzelne Felder der Geocoding-Items werden zur Bearbeitung freigegeben (edit=true). Da Geokoordinaten innerhalb des value-Feldes im JSON-Format erfasst werden, wird zur Ansteuerung verschachtelter Felder der keys-Schlüssel verwendet.</p><pre><code class="language-plaintext"> "templates": {\r\n        "coding": {\r\n            "fields": {\r\n                "value": {\r\n                    "keys": {\r\n                        "lat": {\r\n                            "caption": "Latitude",\r\n                            "edit": true\r\n                        },\r\n                        "lng": {\r\n                            "caption": "Longitude",\r\n                            "edit": true\r\n                        },\r\n                        "radius": {\r\n                            "caption": "Radius",\r\n                            "edit": true\r\n                        }\r\n                    }\r\n                },\r\n                "published": {\r\n                    "edit": true\r\n                },\r\n                "content": {\r\n                    "edit": true\r\n                }\r\n            }\r\n        }\r\n    }</code></pre><p>&nbsp;</p><p>Für den Publikationsstatus (published) muss zudem in der Feldkonfiguration vorgegeben werden, welche Werte erlaubt sind:</p><pre><code class="language-plaintext"> "published": {\r\n            "caption": "Ver\\u00f6ffentlicht",\r\n            "format": "select",\r\n            "codes": [\r\n                "drafted",\r\n                "in progress",\r\n                "complete",\r\n                "published",\r\n                "searchable"\r\n            ]\r\n        },</code></pre><p>&nbsp;</p>', 'html', 'epiweb-epi-articles-view');
INSERT INTO `docs` VALUES (475, 0, NULL, '2022-09-09 13:40:19', '2022-09-22 22:17:12', NULL, NULL, 1, 1, 'help', '2', 'Kategorien', 'B. Dateneingabe / 3. Kategorien', '', 'html', 'epiweb-epi-properties-index');
INSERT INTO `docs` VALUES (476, 0, NULL, '2022-09-09 13:40:38', '2022-09-22 23:08:59', NULL, NULL, 1, 1, 'help', '4', 'Dateien', 'B. Dateneingabe / 4. Dateien', '<h1>Die Ordnerstruktur von Epigraf</h1><p>In die Projektdatenbanken können Bilder, PDF-Dateien oder weitere Dokumente eingebunden werden. Projektübergreifend sind solche Dateien für das Erstellen von Pipelines, die Hilfe und das Wiki relevant.&nbsp;</p><p>Die <strong>datenbankspezifischen Dateien</strong> werden über den Menüpunkt "Dateien" verwaltet. Achten Sie darauf, die passenden Ordner zu verwenden:</p><ul><li>notes: Dateien, die in Notizen eingebunden werden. Es wird empfohlen, für jede Kategorie einen eigenen Unterordner einzurichten.</li><li>bilder: der richtige Ort für alle artikelbezogenen Dateien. Es sollten Unterordner mit dem Kürzel des Projekts und darin wiederum Ordner mit der Artikelnummer verwendet werden.</li><li>properties: Dateien, die in Kategorien verwendet werden. Legen Sie für die verschiedenen Kategoriensysteme jeweils eigene Unterordner an.</li></ul><p>Die Verwaltung der weiteren Ordner sollte Epigraf überlassen werden:</p><ul><li>backup:<strong> </strong>hier befinden sich die <a href="help/epiweb-databanks-index">Backups der Datenbank</a>.</li><li>jobs: enthält das Ergebnis von <a href="/help/epiweb-pipelines-index">Pipeline</a>-Aufrufen.</li><li>import: darin werden die <a href="/help/epiweb-import">importierten CSV-Dateien</a> zwischengespeichert.</li></ul><p>Darüberhinaus können über den Menpunkt "Repositorium" <strong>datenbankübergreifende Dateien </strong>verwaltet werden. Die Dateien sind in verschiedene Mounts gruppiert und sie sind nur mit speziellen <a href="/help/epiweb-users-index">Berechtigungen</a> zugänglich. Für die Zusammenarbeit sind in Epigraf zunächst die Ordner im shared- und export-Mount relevant:</p><ul><li>help: Der help-Ordner im shared-Mount enthält Screenshots und weitere Dokumente für die Hilfe.</li><li>wiki: Ebenfalls im shared-Mount befinden sich in diesem Unterordner die Dateien für das Wiki.</li><li>public: Dateien, die ohne Anmeldung zugänglich sind, sollten hier abgelegt werden, zum Beispiel Bilder für die Startseite von Epigraf.</li><li>export: im export-Mount finden sich alle für die Pipelines verwendeten Dateien, zum Beispiel XSL-Stylesheets.</li></ul><p>Der <strong>root-Mount</strong> erlaubt einen Einstieg in alle Ordner - auch die projektspezifischen - und ist Administrator:innen vorbehalten.</p><h1>Hochladen von Dateien</h1><p>In Epigraf gelten für die Benennung von Dateien und Ordnern einige Konventionen:</p><ul><li><i>Erlaubt:</i> Buchstaben a-z, nur Kleinschreibung</li><li><i>Erlaubt:</i> Ziffern 0-9, Bindestrich, Unterstrich, Pluszeichen</li><li><i>Nicht erlaubt:</i> Keine Umlaute und kein ß, stattdessen ue, ae, oe, ss</li><li><i>Nicht erlaubt: </i>Keine Leerzeichen und keine Kommata, stattdessen Unterstrich oder Bindestrich</li><li><i>Nicht erlaubt: </i>Keine Punkte, außer vor der Dateiendung</li></ul><p>Werden neue Dateien hochgeladen, bereinigt Epigraf die Dateinamen automatisch.</p>', 'html', 'epiweb-epi-files-index');
INSERT INTO `docs` VALUES (477, 0, NULL, '2022-09-09 13:41:10', '2022-09-09 13:41:10', NULL, NULL, 1, 1, 'help', '5', 'Notizen', 'B. Dateneingabe / 5. Notizen', '', 'html', 'epiweb-epi-notes-index');
INSERT INTO `docs` VALUES (494, 0, NULL, '2022-09-09 22:48:53', '2022-09-09 22:48:53', NULL, NULL, 1, 1, 'help', '6', 'Annotationen', 'B. Dateneingabe / 6. Annotationen', '', 'html', 'epiweb-annotations');
INSERT INTO `docs` VALUES (495, 0, NULL, '2022-09-09 22:49:04', '2023-02-14 00:02:09', NULL, NULL, 1, 1, 'help', '7', 'Geodaten', 'B. Dateneingabe / 7. Geodaten', '<p class="infobox">Dieser Abschnitt wird noch überarbeitet!</p><p>Die in Epigraf erfassten Artikel lassen sich auf einer Karte darstellen, indem Geokoordinaten erfasst werden. Dazu müssen zunächst Einträge vom Typ "geolocations" <a href="docs/view/help/487">konfiguriert</a> werden. Die Koordinaten werden im JSON-Format im value-Feld abgespeichert, das für den Itemtype wie folgt konfiguriert wird:</p><p>&nbsp;</p><pre><code class="language-plaintext">{\r\n   "fields": {\r\n       "value": {\r\n           "caption": "Geokoordinaten",\r\n           "showcaption": false,\r\n           "format": "geodata",\r\n           "template": "list",\r\n           "keys": {\r\n             "lat": "Latitude",\r\n             "lng": "Longitude",\r\n             "radius": "Radius"\r\n           }\r\n       }\r\n   }\r\n}</code></pre><p>&nbsp;</p><p>Ein einzelner Datensatz enthält im JSON-Wert folgende Schlüssel:</p><figure class="table"><table><thead><tr><th>Schlüssel</th><th>Beschreibung</th></tr></thead><tbody><tr><td>lat</td><td>Latitude</td></tr><tr><td>lng</td><td>Longitude</td></tr><tr><td>radius</td><td>Für ungenaue Koordinaten der Radius in Metern</td></tr></tbody></table></figure><p>&nbsp;</p><p>Über den Publikationsstatus wird festgehalten, wie zuverlässig eine Angabe ist:</p><ul><li>0 = drafted = <strong>ungeprüfte Koordinaten</strong></li><li>1 = in progress = <strong>vermutete Koordinaten</strong> (automatisch erfasste Datensätze)</li><li>2 = complete = <strong>geprüfte Koordinaten</strong> (manuell durch die Bearbeiter:innen erfasst, z.B. über Geonames)</li><li>3 = published = <strong>geprüfte Koordinaten</strong>, die auf öffentlichen Karten dargestellt werden</li><li>4 = searchable = <strong>geprüfte Koordinaten</strong>, die auf öffentlichen Karten dargestellt werden</li></ul><p>&nbsp;</p>', 'html', 'epiweb-geodata');
INSERT INTO `docs` VALUES (148, 0, NULL, '2020-12-28 12:12:46', '2023-04-08 00:53:23', NULL, NULL, 1, 1, 'pages', '1', 'Startseite', '', '<div class="content-flex-container"><div class="content-flex-first"><p>EPIGRAF ist eine Plattform zur Erfassung, Annotation, Vernetzung und Publikation von Kommunikationsdaten. Die geisteswissenschaftliche Ausrichtung des Projekts wird in den kommenden Jahren auf datenwissenschaftliche und sozialwissenschaftliche Anwendungsfelder erweitert und stellt dann dank eines universellen Datenmodells Möglichkeiten zur Verfügung, Datenbestände von Briefeditionen bis zu Social-Media-Korpora zu erschließen.</p><p>EPIGRAF dient aktuell vor allem der Aufnahme epigrafischer Daten – von Inschriften im Zusammenhang mit den Objekten, auf denen sie angebracht sind – und deren Publikation sowohl in gedruckter Form als auch in digitalen Medien nach den <a href="https://www.nature.com/articles/sdata201618">FAIR-Prinzipien</a> für das Semantic Web.</p><p>EPIGRAF umfasst zwei Komponenten:</p><ul><li>EpigrafDesktop: Redaktionssystem zur Erfassung und Bearbeitung epigrafischer Daten</li><li>EpigrafWeb: Kollaborative Funktionen, Support, Recherche und Datenexport</li></ul><p>EPIGRAF wurde für das <a href="https://www.akademienunion.de/">interakademische Editionsunternehmen</a> „Die Deutschen Inschriften des Mittelalters und der Frühen Neuzeit“ entworfen und wird in den neun Inschriften-Forschungsstellen der sechs beteiligten Akademien der Wissenschaften eingesetzt. Die Entwicklung erfolgt in einer Kooperation der <a href="http://www.adwmainz.de/digitalitaet/digitale-akademie.html">Digitalen Akademie der Wissenschaften und der Literatur | Mainz</a> und des <a href="https://www.uni-muenster.de/Kowi/institut/arbeitsbereiche/digital-media-computational-methods.html">Arbeitsbereichs Digital Media &amp; Computational Methods an der Universität Münster</a>. Die Datenbestände des Projekts werden in gedruckter Form in der Reihe „Die Deutschen Inschriften“ und digital auf <a href="http://www.inschriften.net/">Deutsche Inschriften Online (DIO)</a> publiziert.</p></div><div class="content-flex-1 figure-list-logos"><figure><p><a href="https://www.adwmainz.de/digitalitaet/digitale-akademie.html"><img src="/files/download/2701" alt="Digitale Akademie Mainz"></a></p></figure><figure><p><a href="https://www.uni-muenster.de/Kowi/institut/arbeitsbereiche/digital-media-computational-methods.html"><img src="/files/download/2702" alt="Westfälische Wilhelms-Universität Münster"></a></p></figure><figure><p><a href="https://www.adwmainz.de"><img src="/files/download/898?format=thumb&amp;size=150" alt="Akademie der Wissenschaften und der Literatur Mainz"></a></p><figcaption>Akademie der Wissenschaften und der Literatur Mainz</figcaption></figure><figure><p><a href="https://adw-goe.de/"><img src="/files/download/896?format=thumb&amp;size=150" alt="Akademie der Wissenschaften Göttingen"></a></p><figcaption>Akademie der Wissenschaften Göttingen</figcaption></figure><figure><p><a href="http://www.awk.nrw.de/"><img src="/files/download/897?format=thumb&amp;size=150" alt="Nordrhein-Westfälische Akademie der Wissenschaften und der Künste"></a></p><figcaption>Nordrhein-Westfälische Akademie der Wissenschaften und der Künste</figcaption></figure><figure><p><a href="http://www.saw-leipzig.de/"><img src="/files/download/899?format=thumb&amp;size=150" alt="Sächsische Akademie der Wissenschaften"></a></p><figcaption>Sächsische Akademie der Wissenschaften</figcaption></figure><figure><p><a href="https://www.hadw-bw.de/"><img src="/files/download/900?format=thumb&amp;size=150" alt="Heidelberger Akademie der Wissenschaften"></a></p><figcaption>Heidelberger Akademie der Wissenschaften</figcaption></figure><figure><p><a href="https://www.badw.de/"><img src="/files/download/382?format=thumb&amp;size=150" alt="Bayerische Akademie der Wissenschaften"></a></p><figcaption>Bayerische Akademie der Wissenschaften</figcaption></figure></div></div>', 'html', 'start');
INSERT INTO `docs` VALUES (150, 0, NULL, '2020-12-28 12:16:57', '2022-09-10 13:52:43', NULL, NULL, 1, 1, 'pages', '3', 'Team', '', '<div class="figure-list"><div class="row"><div class="col col-width-4 nopaddingleft"><p><img src="/files/download/9?format=thumb&amp;size=600" alt="Jakob Jünger"></p></div><div class="col col-width-8"><p><a href="https://www.uni-muenster.de/Kowi/personen/jakob-juenger.html">Jakob Jünger</a> baut seit 2003 die Architektur von Epigraf auf - zunächst im Auftrag der Akademie der Wissenschaften zu Göttingen und mittlerweile in einer Kooperation der Digitalen Akademie Mainz und dem <span>Arbeitsbereich Digital Media &amp; Computational Methods der Universität Münster</span>.</p></div></div><div class="row"><div class="col col-width-4 nopaddingleft"><p><img src="/files/download/10?format=thumb&amp;size=600" alt="Jürgen Herold"></p></div><div class="col col-width-8"><p><a href="https://adw-goe.de/forschung/forschungsprojekte-akademienprogramm/deutsche-inschriften/arbeitsstelle-greifswald/mitarbeiterinnen-u-mitarbeiter/">Jürgen Herold</a>. entwickelt seit 2002 in der Greifswalder Inschriftenarbeitsstelle die Vision für eine kollaborative Forschungsinfrastruktur der digitalen Epigrafik.</p></div></div><div class="row"><div class="col col-width-4 nopaddingleft"><p><img src="/files/download/2608?format=thumb&amp;size=600" alt="Chantal Gärtner"></p></div><div class="col col-width-8"><p><a href="https://www.uni-muenster.de/Kowi/personen/chantal-gaertner.html">Chantal Gärtner</a> ist im Rahmen einer Kooperation mit der Digitalen Akademie Mainz als wissenschaftliche Mitarbeiterin im Arbeitsbereich Digital Media &amp; Computational Methods der Universität Münster beschäftigt. Sie erkundet im Epigrafprojekt die Anwendungsfelder automatisierter Datenerhebung und -analyse.</p></div></div><div class="row"><div class="col col-width-4 nopaddingleft"><p><img src="/files/download/2006?format=thumb&amp;size=600" alt="Wolf-Dieter Syring"></p></div><div class="col col-width-8"><p><a href="https://www.adwmainz.de/mitarbeiterinnen/profil/dr-wolf-dieter-syring.html">Wolf-Dieter Syring</a> beschäftigt sich seit über zwei Jahrzehnten mit den Digitalen Geisteswissenschaften und sorgt nun dafür, Epigraf in eine Webanwendung zu überführen.</p></div></div><div class="row"><div class="col col-width-4 nopaddingleft"><p><img src="/files/download/2007?format=thumb&amp;size=600" alt="Maximilian Michel"></p></div><div class="col col-width-8"><p><a href="https://www.adwmainz.de/mitarbeiterinnen/profil/maximilian-michel.html">Maximilian Michel</a> studiert an der Universität Mainz den Masterstudiengang Digitale Methodik in den Geistes- und Kulturwissenschaften. Im Epigraf-Projekt bringt er seine Kenntnisse in die Entwicklung der Webanwendung ein.</p></div></div></div>', 'html', 'team');
INSERT INTO `docs` VALUES (152, 0, NULL, '2020-12-28 12:21:19', '2023-04-08 00:53:27', NULL, NULL, 1, 1, 'pages', '4', 'Impressum', '', '<h1>Herausgeber</h1>\r\n\r\n<p>\r\n    Akademie der Wissenschaften und der Literatur | Mainz<br>\r\n    Geschwister-Scholl-Straße 2<br>\r\n    D-55131 Mainz<br>\r\n    Telefon: 06131/577-0<br>\r\n    Telefax: 06131/577-206<br>\r\n    E-Mail: <a href="mailto:generalsekretariat@adwmainz.de">generalsekretariat@adwmainz.de</a>\r\n</p>\r\n\r\n<h1>Urheberrecht</h1>\r\n<p>\r\n    Die auf dieser Website veröffentlichten Inhalte unterliegen dem deutschen Urheber- und Leistungsschutzrecht. Jede vom deutschen Urheber- und Leistungsschutzrecht nicht zugelassene Verwertung bedarf der vorherigen schriftlichen Zustimmung des Anbieters oder jeweiligen Rechteinhabers. Dies gilt insbesondere für Vervielfältigung, Bearbeitung, Übersetzung, Einspeicherung, Verarbeitung bzw. Wiedergabe von Inhalten in Datenbanken oder anderen elektronischen Medien und Systemen. Inhalte und Rechte Dritter sind dabei als solche gekennzeichnet. Die unerlaubte Vervielfältigung oder Weitergabe einzelner Inhalte oder kompletter Seiten ist nicht gestattet und strafbar. Lediglich die Herstellung von Kopien und Downloads für den persönlichen, privaten und nicht kommerziellen Gebrauch ist erlaubt.\r\n</p>\r\n\r\n<h1>Links</h1>\r\n<p>\r\n    Wir haben keinen Einfluss auf den Inhalt der von dieser Seite verlinkten externen Internetseiten und sind für diese auch nicht verantwortlich. Eine Prüfung bei der Verlinkung ergab keine strafbaren Inhalte auf diesen Seiten. Alle Linkangaben ohne Gewähr. Seitenaufrufe externer Seiten über diese Links erfolgen auf eigene Gefahr. Dies gilt für alle Links auf dieser Internetseite.\r\n</p>\r\n\r\n<h1>Haftungsausschluss</h1>\r\n<p>\r\n    Die Inhalte dieses Internetangebotes wurden sorgfältig geprüft und nach bestem Wissen erstellt. Eine Haftung oder Garantie für die Aktualität, Richtigkeit und Vollständigkeit der zur Verfügung gestellten Informationen und Daten ist jedoch ausgeschlossen. In keinem Fall wird für Schäden, die sich aus der Verwendung der abgerufenen Informationen ergeben, eine Haftung übernommen.\r\n</p>\r\n', 'markdown', 'impressum');
INSERT INTO `docs` VALUES (754, 0, NULL, '2023-03-25 15:24:23', '2023-03-25 15:24:53', NULL, NULL, 1, 1, 'pages', '5', 'Datenschutz', '', '', 'html', 'privacy');
INSERT INTO `docs` VALUES (117, 0, NULL, '2018-11-10 22:58:07', '2022-02-03 13:08:38', NULL, NULL, 0, 1, 'wiki', '', 'Wiki', '', '<h2>Kollektive Intelligenz: Epigraf Wiki&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>&nbsp;</h2><p><a href="https://mattermost.gitlab.rlp.net/di/channels/epigraf-user-support">Mattermost: Epigraf User Support</a></p><p><a href="/docs/view/wiki/118">Inschriften Online</a></p><p><a href="/docs/view/wiki/143">Deutsche Inschriften - Kumulierte Register</a></p><p><a href="/docs/view/wiki/162">Registermanuale</a></p><p><a href="/docs/view/wiki/96">Hilfsmittel</a></p><p><a href="/docs/view/wiki/103">Repositorium: DI-Bände, Literatur (Sonderdrucke u. dgl.) u. Filesharing</a></p><p><a href="/docs/view/wiki/120">Web-Recherchen</a></p><p><a href="/docs/view/wiki/104">Organisation</a></p><p><a href="154">EpiDoc</a></p><p><a href="https://twitter.com/inschriften"><img src="/files/download?root=shared&amp;path=help%2Fscreenshots&amp;filename=twitter_logo.png"></a></p>', 'html', 'start');
INSERT INTO `docs` VALUES (145, 0, NULL, '2020-10-18 17:51:54', '2021-06-28 11:49:30', NULL, NULL, 0, 1, 'wiki', '', 'Dokumentationen', 'G. Dokumentation', '[Jürgen Herold: Einführung in Epigraf 4](/files/download?root=shared&path=wiki%2FDI-Allgemein&filename=epigraf4-doku.pdf)\r\n\r\n[Thomas Kollatz: Epidoc-Workshop für DIO (22. Oktober 2020)](https://digicademy.github.io/2020_DIO/)\r\n\r\n[Jens Borchert-Pickenhan: Leitfaden für die Erstellung des Buchsatzes aus Epigraf in Word](/files/download?root=shared&path=wiki%2FHilfsmittel&filename=Leitfaden-Satzherstellung-aus-Epigraf-in-Word.docx)', 'markdown', '');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

INSERT INTO `files` (`id`, `deleted`, `published`, `created`, `modified`, `created_by`, `modified_by`, `name`, `description`, `config`, `type`, `size`, `root`, `path`, `isfolder`) VALUES (382, 0, 1, '2021-02-27 10:18:36', '2021-06-20 13:06:15', NULL, NULL, 'testbild.png', '', NULL, 'png', 13682, 'shared', 'images', 0);
INSERT INTO `files` (`id`, `deleted`, `published`, `created`, `modified`, `created_by`, `modified_by`, `name`, `description`, `config`, `type`, `size`, `root`, `path`, `isfolder`) VALUES (896, 0, 1, '2021-06-20 11:28:17', '2021-06-20 13:07:32', NULL, NULL, 'testbild.png', '', NULL, 'png', 39974, 'shared', 'images', 0);
INSERT INTO `files` (`id`, `deleted`, `published`, `created`, `modified`, `created_by`, `modified_by`, `name`, `description`, `config`, `type`, `size`, `root`, `path`, `isfolder`) VALUES (897, 0, 1, '2021-06-20 11:28:47', '2021-06-20 13:08:04', NULL, NULL, 'testbild.png', '', NULL, 'png', 29286, 'shared', 'images', 0);
INSERT INTO `files` (`id`, `deleted`, `published`, `created`, `modified`, `created_by`, `modified_by`, `name`, `description`, `config`, `type`, `size`, `root`, `path`, `isfolder`) VALUES (898, 0, 1, '2021-06-20 11:29:07', '2021-06-20 13:07:54', NULL, NULL, 'testbild.png', '', NULL, 'png', 15331, 'shared', 'images', 0);
INSERT INTO `files` (`id`, `deleted`, `published`, `created`, `modified`, `created_by`, `modified_by`, `name`, `description`, `config`, `type`, `size`, `root`, `path`, `isfolder`) VALUES (899, 0, 1, '2021-06-20 11:29:20', '2021-06-20 13:07:46', NULL, NULL, 'testbild.png', '', NULL, 'png', 13992, 'shared', 'images', 0);
INSERT INTO `files` (`id`, `deleted`, `published`, `created`, `modified`, `created_by`, `modified_by`, `name`, `description`, `config`, `type`, `size`, `root`, `path`, `isfolder`) VALUES (900, 0, 1, '2021-06-20 11:29:32', '2021-06-20 13:07:40', NULL, NULL, 'testbild.png', '', NULL, 'png', 30804, 'shared', 'images', 0);
INSERT INTO `files` (`id`, `deleted`, `published`, `created`, `modified`, `created_by`, `modified_by`, `name`, `description`, `config`, `type`, `size`, `root`, `path`, `isfolder`) VALUES (2702, 0, 1, '2022-01-27 12:28:57', '2023-12-19 21:19:13', NULL, 2, 'testbild.png', '', NULL, 'png', 26415, 'shared', 'images', 0);
INSERT INTO `files` (`id`, `deleted`, `published`, `created`, `modified`, `created_by`, `modified_by`, `name`, `description`, `config`, `type`, `size`, `root`, `path`, `isfolder`) VALUES (2701, 0, 1, '2022-01-27 12:28:57', '2022-01-27 12:29:19', NULL, NULL, 'testbild.png', '', NULL, 'png', 20437, 'shared', 'images', 0);
