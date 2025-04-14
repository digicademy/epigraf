-- --------------------------------------------------------
-- Host:                         localhost
-- Server Version:               10.3.34-MariaDB-1:10.3.34+maria~focal - mariadb.org binary distribution
-- Server Betriebssystem:        debian-linux-gnu
-- HeidiSQL Version:             11.3.0.6295
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Exportiere Struktur von Tabelle epi_public.articles
CREATE TABLE IF NOT EXISTS `articles` (
                                          `id` int(11) NOT NULL AUTO_INCREMENT,
                                          `projects_id` int(11) DEFAULT NULL,
                                          `deleted` int(11) NOT NULL DEFAULT 0,
                                          `version_id` int(11) DEFAULT NULL,
                                          `job_id` int(11) DEFAULT NULL,
                                          `published` int(11) DEFAULT NULL,
                                          `created` timestamp NULL DEFAULT '0000-00-00 00:00:00',
                                          `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                                          `modified_by` int(11) DEFAULT NULL,
                                          `created_by` int(11) DEFAULT NULL,
                                          `articletype` varchar(50)  COLLATE utf8mb4_unicode_ci NOT NULL,
                                          `articlenumber` varchar(1500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                          `title` varchar(1500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                          `status` varchar(1500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                          `norm_data` varchar(1500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                          `norm_iri` varchar(1500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                          `norm_type` varchar(1500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                          `lastopen_id` int(11) DEFAULT NULL,
                                          `lastopen_tab` varchar(500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                          `lastopen_field` varchar(500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                          `lastopen_tagid` varchar(500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                          PRIMARY KEY (`id`),
                                          KEY `projects_id` (`projects_id`),
                                          KEY `modified_by` (`modified_by`),
                                          KEY `created_by` (`created_by`),
                                          KEY `lastopen_id` (`lastopen_id`),
                                          KEY `lastopen_tab` (`lastopen_tab`(255)),
                                          KEY `lastopen_feld` (`lastopen_field`(255)),
                                          KEY `lastopen_subid` (`lastopen_tagid`(255)),
                                          KEY `published` (`published`),
                                          KEY `articletype` (`articletype`),
                                          KEY `deleted` (`deleted`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle epi_public.files
CREATE TABLE IF NOT EXISTS `files` (
                                       `id` int(11) NOT NULL AUTO_INCREMENT,
                                       `deleted` int(11) NOT NULL DEFAULT 0,
                                       `version_id` int(11) DEFAULT NULL,
                                       `job_id` int(11) DEFAULT NULL,
                                       `published` int(11) DEFAULT NULL,
                                       `created` datetime DEFAULT NULL,
                                       `modified` datetime DEFAULT NULL,
                                       `name` varchar(500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `description` text  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `type` varchar(100)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `size` int(11) DEFAULT NULL,
                                       `root` varchar(100)  COLLATE utf8mb4_unicode_ci DEFAULT 'root',
                                       `path` varchar(500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `isfolder` tinyint(4) NOT NULL DEFAULT 0,
                                       PRIMARY KEY (`id`),
                                       KEY `published` (`published`),
                                       KEY `name` (`name`(333)),
                                       KEY `type` (`type`),
                                       KEY `path` (`path`(333)),
                                       KEY `root` (`root`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle epi_public.footnotes
CREATE TABLE IF NOT EXISTS `footnotes` (
                                           `id` int(11) NOT NULL AUTO_INCREMENT,
                                           `deleted` int(11) NOT NULL DEFAULT 0,
                                           `version_id` int(11) DEFAULT NULL,
                                           `job_id` int(11) DEFAULT NULL,
                                           `published` int(11) DEFAULT NULL,
                                           `created` timestamp NULL DEFAULT '0000-00-00 00:00:00',
                                           `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                                           `modified_by` int(11) DEFAULT NULL,
                                           `created_by` int(11) DEFAULT NULL,
                                           `sortno` int(11) DEFAULT NULL,
                                           `fntype` tinyint(1) DEFAULT NULL,
                                           `displayname` varchar(200)  COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
                                           `content` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                           `root_id` int(11) DEFAULT NULL,
                                           `root_tab` varchar(500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                           `from_id` int(11) DEFAULT NULL,
                                           `from_tab` varchar(500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                           `from_field` varchar(500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                           `from_tagname` varchar(50)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                           `from_tagid` varchar(500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                           `from_sort` int(11) DEFAULT NULL,
                                           PRIMARY KEY (`id`),
                                           KEY `modified_by` (`modified_by`),
                                           KEY `created_by` (`created_by`),
                                           KEY `rootrecord_id` (`root_id`),
                                           KEY `rootrecord_tab` (`root_tab`(255)),
                                           KEY `published` (`published`),
                                           KEY `fntype` (`fntype`),
                                           KEY `from_tagname` (`from_tagname`),
                                           KEY `deleted` (`deleted`) USING BTREE,
                                           KEY `from_id` (`from_id`) USING BTREE,
                                           KEY `from_tab` (`from_tab`(255)) USING BTREE,
                                           KEY `from_field` (`from_field`(255)) USING BTREE,
                                           KEY `from_tagid` (`from_tagid`(255)) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle epi_public.items
CREATE TABLE IF NOT EXISTS `items` (
                                       `id` int(11) NOT NULL AUTO_INCREMENT,
                                       `deleted` int(11) NOT NULL DEFAULT 0,
                                       `version_id` int(11) DEFAULT NULL,
                                       `job_id` int(11) DEFAULT NULL,
                                       `published` int(11) DEFAULT NULL,
                                       `created` timestamp NULL DEFAULT '0000-00-00 00:00:00',
                                       `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                                       `created_by` int(11) DEFAULT NULL,
                                       `modified_by` int(11) DEFAULT NULL,
                                       `sortno` int(11) DEFAULT NULL,
                                       `itemtype` varchar(500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `properties_id` int(11) DEFAULT NULL,
                                       `value` varchar(1500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `content` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `translation` text  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `flagged` tinyint(1) DEFAULT NULL,
                                       `links_id` int(11) DEFAULT NULL,
                                       `links_tab` varchar(500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `links_field` varchar(500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `links_tagid` varchar(500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `file_name` varchar(1500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `file_type` varchar(10)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `file_path` varchar(1500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `file_source` varchar(1500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `file_copyright` text  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `file_online` tinyint(1) DEFAULT NULL,
                                       `date_sort` varchar(1500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `date_value` varchar(1500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `date_add` text  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `date_start` double DEFAULT NULL,
                                       `date_end` double DEFAULT NULL,
                                       `source_autopsy` tinyint(1) DEFAULT NULL,
                                       `source_from` text  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `source_addition` text  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `pos_x` int(11) DEFAULT NULL,
                                       `pos_y` int(11) DEFAULT NULL,
                                       `pos_z` int(11) DEFAULT NULL,
                                       `articles_id` int(11) DEFAULT NULL,
                                       `sections_id` int(11) DEFAULT NULL,
                                       PRIMARY KEY (`id`),
                                       KEY `modified_by` (`modified_by`),
                                       KEY `created_by` (`created_by`),
                                       KEY `properties_id` (`properties_id`),
                                       KEY `orderidx` (`sortno`),
                                       KEY `sections_id` (`sections_id`),
                                       KEY `articles_id` (`articles_id`),
                                       KEY `published` (`published`),
                                       KEY `file_name` (`file_name`(1024)),
                                       KEY `file_path` (`file_path`(1024)),
                                       KEY `file_type` (`file_type`),
                                       KEY `deleted` (`deleted`) USING BTREE,
                                       KEY `itemtype` (`deleted`,`itemtype`(255)) USING BTREE,
                                       KEY `countbyarticle` (`deleted`,`itemtype`,`articles_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle epi_public.links
CREATE TABLE IF NOT EXISTS `links` (
                                       `id` int(11) NOT NULL AUTO_INCREMENT,
                                       `deleted` int(11) NOT NULL DEFAULT 0,
                                       `version_id` int(11) DEFAULT NULL,
                                       `job_id` int(11) DEFAULT NULL,
                                       `published` int(11) DEFAULT NULL,
                                       `created` timestamp NULL DEFAULT '0000-00-00 00:00:00',
                                       `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                                       `modified_by` int(11) DEFAULT NULL,
                                       `created_by` int(11) DEFAULT NULL,
                                       `root_id` int(11) DEFAULT NULL,
                                       `root_tab` varchar(500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `from_id` int(11) DEFAULT NULL,
                                       `from_tab` varchar(500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `from_field` varchar(500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `from_tagname` varchar(1500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `from_tagid` varchar(500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `to_id` int(11) DEFAULT NULL,
                                       `to_tab` varchar(500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `to_field` varchar(500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `to_tagid` varchar(500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       PRIMARY KEY (`id`),
                                       KEY `modified_by` (`modified_by`),
                                       KEY `created_by` (`created_by`),
                                       KEY `rootrecord_id` (`root_id`),
                                       KEY `rootrecord_tab` (`root_tab`(255)),
                                       KEY `published` (`published`),
                                       KEY `from_tagname` (`from_tagname`(1024)),
                                       KEY `deleted` (`deleted`) USING BTREE,
                                       KEY `from_id` (`from_id`) USING BTREE,
                                       KEY `from_tab` (`from_tab`(255)) USING BTREE,
                                       KEY `from_field` (`from_field`(255)) USING BTREE,
                                       KEY `from_tagid` (`from_tagid`(255)) USING BTREE,
                                       KEY `to_id` (`to_id`) USING BTREE,
                                       KEY `to_tab` (`to_tab`(255)) USING BTREE,
                                       KEY `to_field` (`to_field`(255)) USING BTREE,
                                       KEY `to_tagid` (`to_tagid`(255)) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle epi_public.locktable
CREATE TABLE IF NOT EXISTS `locktable` (
                                           `id` int(11) NOT NULL AUTO_INCREMENT,
                                           `lock_token` int(11) DEFAULT NULL,
                                           `lock_mode` int(11) DEFAULT NULL,
                                           `lock_table` varchar(1500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                           `lock_segment` varchar(255)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                           `lock_id` int(11) DEFAULT NULL,
                                           PRIMARY KEY (`id`),
                                           KEY `lockid` (`lock_token`),
                                           KEY `lockdatensatz` (`lock_id`),
                                           KEY `lockmode` (`lock_mode`),
                                           KEY `locksegment` (`lock_segment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle epi_public.meta
CREATE TABLE IF NOT EXISTS `meta` (
                                      `id` int(11) NOT NULL AUTO_INCREMENT,
                                      `deleted` int(11) NOT NULL DEFAULT 0,
                                      `created` timestamp NULL DEFAULT '0000-00-00 00:00:00',
                                      `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                                      `modified_by` int(11) DEFAULT NULL,
                                      `created_by` int(11) DEFAULT NULL,
                                      `name` varchar(1500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                      `value` varchar(1500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                      PRIMARY KEY (`id`),
                                      KEY `modified_by` (`modified_by`),
                                      KEY `created_by` (`created_by`),
                                      KEY `deleted` (`deleted`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle epi_public.notes
CREATE TABLE IF NOT EXISTS `notes` (
                                       `id` int(11) NOT NULL AUTO_INCREMENT,
                                       `deleted` int(11) NOT NULL DEFAULT 0,
                                       `version_id` int(11) DEFAULT NULL,
                                       `job_id` int(11) DEFAULT NULL,
                                       `published` int(11) DEFAULT NULL,
                                       `created` datetime DEFAULT NULL,
                                       `modified` datetime DEFAULT NULL,
                                       `name` char(200)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `category` varchar(300)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `content` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `format` varchar(15)  COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'html',
                                       PRIMARY KEY (`id`),
                                       KEY `published` (`published`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle epi_public.projects
CREATE TABLE IF NOT EXISTS `projects` (
                                          `id` int(11) NOT NULL AUTO_INCREMENT,
                                          `deleted` int(11) NOT NULL DEFAULT 0,
                                          `version_id` int(11) DEFAULT NULL,
                                          `job_id` int(11) DEFAULT NULL,
                                          `published` int(11) DEFAULT NULL,
                                          `created` timestamp NULL DEFAULT '0000-00-00 00:00:00',
                                          `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                                          `modified_by` int(11) DEFAULT NULL,
                                          `created_by` int(11) DEFAULT NULL,
                                          `name` varchar(1500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                          `shortname` varchar(1500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                          `description` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                          `norm_data` varchar(1500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                          `norm_iri` varchar(1500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                          PRIMARY KEY (`id`),
                                          KEY `modified_by` (`modified_by`),
                                          KEY `created_by` (`created_by`),
                                          KEY `published` (`published`),
                                          KEY `deleted` (`deleted`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle epi_public.properties
CREATE TABLE IF NOT EXISTS `properties` (
                                            `id` int(11) NOT NULL AUTO_INCREMENT,
                                            `deleted` int(11) NOT NULL DEFAULT 0,
                                            `version_id` int(11) DEFAULT NULL,
                                            `job_id` int(11) DEFAULT NULL,
                                            `published` int(11) DEFAULT NULL,
                                            `created` timestamp NULL DEFAULT '0000-00-00 00:00:00',
                                            `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                                            `created_by` int(11) DEFAULT NULL,
                                            `modified_by` int(11) DEFAULT NULL,
                                            `sortno` int(11) DEFAULT NULL,
                                            `sortkey` varchar(500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                            `propertytype` varchar(500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                            `number` varchar(1500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                            `file_name` varchar(1500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                            `properties_id` int(11) DEFAULT NULL,
                                            `lemma` varchar(1500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                            `name` varchar(1500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                            `unit` varchar(500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                            `comment` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                            `content` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                            `elements` text  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                            `keywords` varchar(1500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                            `source_from` text  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                            `ishidden` tinyint(4) DEFAULT NULL,
                                            `iscategory` tinyint(4) DEFAULT NULL,
                                            `norm_type` varchar(150)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                            `norm_data` varchar(1500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                            `norm_iri` varchar(1500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                            `import_db` varchar(1500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                            `import_id` varchar(1500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                            `related_id` int(11) DEFAULT NULL,
                                            `mergedto_id` int(11) DEFAULT NULL,
                                            `splitfrom_id` int(11) DEFAULT NULL,
                                            `parent_id` int(11) DEFAULT NULL,
                                            `level` int(11) DEFAULT NULL,
                                            `lft` int(11) DEFAULT NULL,
                                            `rght` int(11) DEFAULT NULL,
                                            PRIMARY KEY (`id`),
                                            KEY `propertytype` (`propertytype`(255)),
                                            KEY `sortno` (`sortno`),
                                            KEY `parent_id` (`parent_id`),
                                            KEY `published` (`published`),
                                            KEY `related_id` (`related_id`),
                                            KEY `lft` (`lft`),
                                            KEY `rght` (`rght`),
                                            KEY `deleted` (`deleted`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle epi_public.sections
CREATE TABLE IF NOT EXISTS `sections` (
                                          `id` int(11) NOT NULL AUTO_INCREMENT,
                                          `deleted` int(11) NOT NULL DEFAULT 0,
                                          `version_id` int(11) DEFAULT NULL,
                                          `job_id` int(11) DEFAULT NULL,
                                          `published` int(11) DEFAULT NULL,
                                          `created` timestamp NULL DEFAULT '0000-00-00 00:00:00',
                                          `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                                          `modified_by` int(11) DEFAULT NULL,
                                          `created_by` int(11) DEFAULT NULL,
                                          `sortno` int(11) NOT NULL DEFAULT 0,
                                          `layout_cols` int(11) NOT NULL DEFAULT 0,
                                          `layout_rows` int(11) NOT NULL DEFAULT 0,
                                          `sectiontype` varchar(200)  COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
                                          `sectionnumber` int(11) DEFAULT NULL,
                                          `sectionname` varchar(1500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                          `sectionalias` varchar(1500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                          `comment` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                          `status` int(11) DEFAULT NULL,
                                          `parent_id` int(11) DEFAULT NULL,
                                          `level` int(11) DEFAULT NULL,
                                          `lft` int(11) DEFAULT NULL,
                                          `rght` int(11) DEFAULT NULL,
                                          `norm_iri` varchar(1500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                          `articles_id` int(11) DEFAULT NULL,
                                          PRIMARY KEY (`id`),
                                          KEY `modified_by` (`modified_by`),
                                          KEY `created_by` (`created_by`),
                                          KEY `status` (`status`),
                                          KEY `articles_id` (`articles_id`),
                                          KEY `sectiontype` (`sectiontype`),
                                          KEY `published` (`published`),
                                          KEY `deleted` (`deleted`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle epi_public.token
CREATE TABLE IF NOT EXISTS `token` (
                                       `id` int(11) NOT NULL AUTO_INCREMENT,
                                       `deleted` int(11) NOT NULL DEFAULT 0,
                                       `created` timestamp NULL DEFAULT '0000-00-00 00:00:00',
                                       `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                                       `modified_by` int(11) DEFAULT NULL,
                                       `created_by` int(11) DEFAULT NULL,
                                       `usertoken` varchar(255)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `sessiontoken` varchar(255)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       PRIMARY KEY (`id`),
                                       KEY `modified_by` (`modified_by`),
                                       KEY `created_by` (`created_by`),
                                       KEY `userrole` (`usertoken`),
                                       KEY `deleted` (`deleted`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle epi_public.types
CREATE TABLE IF NOT EXISTS `types` (
                                       `id` int(11) NOT NULL AUTO_INCREMENT,
                                       `deleted` int(11) NOT NULL DEFAULT 0,
                                       `version_id` int(11) DEFAULT NULL,
                                       `job_id` int(11) DEFAULT NULL,
                                       `created` timestamp NULL DEFAULT '0000-00-00 00:00:00',
                                       `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                                       `created_by` int(11) DEFAULT NULL,
                                       `modified_by` int(11) DEFAULT NULL,
                                       `scope` varchar(50)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `name` varchar(100)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `sortno` int(11) NOT NULL DEFAULT 0,
                                       `category` varchar(100)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `caption` varchar(200)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `description` varchar(500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `config` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `norm_iri` varchar(1500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       PRIMARY KEY (`id`),
                                       KEY `name` (`name`),
                                       KEY `sortno` (`sortno`),
                                       KEY `deleted` (`deleted`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle epi_public.users
CREATE TABLE IF NOT EXISTS `users` (
                                       `id` int(11) NOT NULL AUTO_INCREMENT,
                                       `deleted` int(11) NOT NULL DEFAULT 0,
                                       `version_id` int(11) DEFAULT NULL,
                                       `job_id` int(11) DEFAULT NULL,
                                       `created` timestamp NULL DEFAULT '0000-00-00 00:00:00',
                                       `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                                       `modified_by` int(11) DEFAULT NULL,
                                       `created_by` int(11) DEFAULT NULL,
                                       `name` varchar(1500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `acronym` varchar(1500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `userrole` int(11) DEFAULT NULL,
                                       `norm_iri` varchar(1500)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       PRIMARY KEY (`id`),
                                       KEY `modified_by` (`modified_by`),
                                       KEY `created_by` (`created_by`),
                                       KEY `userrole` (`userrole`),
                                       KEY `deleted` (`deleted`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `footnotes`
	CHANGE COLUMN `displayname` `displayname` VARCHAR(200) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `fntype`;

ALTER TABLE `items`
    ADD COLUMN `norm_iri` VARCHAR(1500) NULL DEFAULT NULL AFTER `pos_z`;

ALTER TABLE `projects`
    ADD COLUMN `projecttype` VARCHAR(50) NULL DEFAULT NULL AFTER `created_by`;

ALTER TABLE `users`
    ADD COLUMN `usertype` VARCHAR(50) NULL DEFAULT NULL AFTER `created_by`;

ALTER TABLE `notes`
    ADD COLUMN `norm_iri` VARCHAR(1500) NULL DEFAULT NULL AFTER `modified`;

ALTER TABLE `notes`
    ADD COLUMN `notetype` VARCHAR(50) NULL DEFAULT NULL AFTER `modified`;

ALTER TABLE `notes`
    ADD COLUMN `created_by` INT(11) NULL DEFAULT NULL AFTER `modified`,
	ADD COLUMN `modified_by` INT(11) NULL DEFAULT NULL AFTER `created_by`;

ALTER TABLE `notes`
    ADD COLUMN `sortkey` VARCHAR(50) NULL DEFAULT NULL AFTER `category`;


ALTER TABLE `footnotes`
    ADD COLUMN `norm_iri` VARCHAR(1500) NULL DEFAULT NULL AFTER `from_sort`;

ALTER TABLE `links`
    ADD COLUMN `norm_iri` VARCHAR(1500) NULL DEFAULT NULL AFTER `to_tagid`;



CREATE TABLE IF NOT EXISTS `grains` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `deleted` int(11) NOT NULL DEFAULT 0,
    `version_id` int(11) DEFAULT NULL,
    `job_id` int(11) DEFAULT NULL,
    `published` int(11) DEFAULT NULL,
    `created` timestamp NULL DEFAULT '0000-00-00 00:00:00',
    `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `created_by` int(11) DEFAULT NULL,
    `modified_by` int(11) DEFAULT NULL,
    `sortno` int(11) DEFAULT NULL,
    `graintype` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `content` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `file_name` varchar(1500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `file_type` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `file_path` varchar(1500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `file_source` varchar(1500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `file_copyright` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `links_tab` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `links_id` int(11) DEFAULT NULL,
    `properties_id` int(11) DEFAULT NULL,
    `norm_iri` varchar(1500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `deleted` (`deleted`),
    KEY `properties_id` (`properties_id`),
    KEY `sortno` (`sortno`),
    KEY `itemtype` (`graintype`(255)) USING BTREE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


/* Add published field to types */
ALTER TABLE `types`
    ADD COLUMN `published` TINYINT NULL DEFAULT NULL AFTER `job_id`;

/* Add created_by and modified_by to files */
ALTER TABLE `files`
    ADD COLUMN `created_by` int(11) DEFAULT NULL AFTER modified,
   ADD COLUMN `modified_by` int(11) DEFAULT NULL AFTER `created_by`;



/* Add project and article sort no */
ALTER TABLE `projects`
    ADD COLUMN `sortno` INT NULL DEFAULT NULL AFTER `projecttype`;

ALTER TABLE `articles`
    ADD COLUMN `sortno` INT NULL DEFAULT NULL AFTER `articlenumber`;

ALTER TABLE `articles`
    CHANGE COLUMN `articletype` `articletype` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `created_by`,
    CHANGE COLUMN `articlenumber` `signature` VARCHAR(1500) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `articletype`;

ALTER TABLE `projects`
    CHANGE COLUMN `shortname` `signature` VARCHAR(1500) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `name`;

ALTER TABLE `properties`
    CHANGE COLUMN `number` `signature` VARCHAR(1500) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `propertytype`;

ALTER TABLE `articles`
    CHANGE COLUMN `title` `name` VARCHAR(1500) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `signature`;

ALTER TABLE `footnotes`
    CHANGE COLUMN `displayname` `name` VARCHAR(200) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `fntype`;

ALTER TABLE `sections`
    CHANGE COLUMN `sectionnumber` `number` INT(11) NULL DEFAULT NULL AFTER `sectiontype`,
    CHANGE COLUMN `sectionname` `name` VARCHAR(1500) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `number`,
    CHANGE COLUMN `sectionalias` `alias` VARCHAR(1500) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `name`;



INSERT INTO `meta` (`name`, `value`) VALUES
                                         ('db_version', '4.5'),
                                         ('db_name', 'Epigraf');

ALTER TABLE `properties`
    DROP INDEX IF EXISTS `lemma`,
    ADD INDEX `lemma` (`lemma`);

ALTER TABLE `properties`
    DROP INDEX IF EXISTS `tree`,
    ADD INDEX `tree` (`deleted`, `propertytype`, `level`, `lft`);

ALTER TABLE `properties`
    DROP INDEX IF EXISTS `lft`,
    ADD INDEX `lft` (`deleted`,`propertytype`, `lft`) USING BTREE,

    DROP INDEX IF EXISTS `rght`,
    ADD INDEX `rght` (`deleted`,`propertytype`, `rght`) USING BTREE;

/** Merge tracking fields **/
# ALTER TABLE `items`
#     ADD COLUMN `properties_mergedfrom_id` INT(11) NULL DEFAULT NULL AFTER `properties_id`;
#
# ALTER TABLE `links`
#     ADD COLUMN `to_mergedfrom_id` INT(11) NULL DEFAULT NULL AFTER `to_tagid`,
#     ADD COLUMN `from_mergedfrom_id` INT(11) NULL DEFAULT NULL AFTER `from_tagid`;
#
# ALTER TABLE `properties`
#     ADD COLUMN `properties_mergedfrom_id` INT(11) NULL DEFAULT NULL AFTER `properties_id`,
#     ADD COLUMN `related_mergedfrom_id` INT(11) NULL DEFAULT NULL AFTER `related_id`,
#     ADD COLUMN `parent_mergedfrom_id` INT(11) NULL DEFAULT NULL AFTER `parent_id`;


ALTER TABLE `properties`
    CHANGE COLUMN `sortkey` `sortkey` VARCHAR(1500) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `sortno`;

ALTER TABLE `types`
    ADD COLUMN `mode` VARCHAR(50) NOT NULL DEFAULT 'default' COLLATE 'utf8mb4_unicode_ci' AFTER `scope`;

ALTER TABLE `types`
    DROP INDEX `name`,
    ADD INDEX `name` (`deleted`, `scope`, `name`) USING BTREE,
    ADD INDEX `mode` (`deleted`, `scope`, `name`, `mode`);


ALTER TABLE `footnotes`
    CHANGE COLUMN `from_tagname` `from_tagname` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `from_field`;

ALTER TABLE `links`
    CHANGE COLUMN `from_tagname` `from_tagname` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `from_field`;

ALTER TABLE `footnotes`
    ADD COLUMN `segment` TEXT NULL DEFAULT NULL AFTER `name`;

ALTER TABLE `notes`
	ADD COLUMN `menu` TINYINT(4) NOT NULL DEFAULT 1 AFTER `published`;

ALTER TABLE `locktable`
	ADD COLUMN `expires` DATETIME NULL DEFAULT NULL AFTER `lock_id`;

ALTER TABLE `items`
	ADD COLUMN `file_meta` TEXT NULL DEFAULT NULL COLLATE 'utf8_unicode_ci' AFTER `file_copyright`;


ALTER TABLE `types`
	ADD COLUMN `preset` VARCHAR(50) NOT NULL DEFAULT 'default' COLLATE 'utf8mb4_unicode_ci' AFTER `mode`,
	DROP INDEX `mode`,
	ADD INDEX `mode` (`deleted`, `scope`, `name`, `preset`) USING BTREE;

ALTER TABLE `notes` CHANGE COLUMN `norm_iri` `norm_iri` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `format`;

ALTER TABLE `items`
	ADD COLUMN `itemgroup` VARCHAR(100) NULL DEFAULT NULL AFTER `itemtype`;
