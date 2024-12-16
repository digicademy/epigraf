-- MariaDB dump 10.19  Distrib 10.5.19-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: mysql    Database: epi_test
-- ------------------------------------------------------
-- Server version	10.3.34-MariaDB-1:10.3.34+maria~focal

--
-- Table structure for table `articles`
--

DROP TABLE IF EXISTS `articles`;
CREATE TABLE `articles` (
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
  `articletype` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signature` varchar(1500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(1500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sortno` int(11) DEFAULT NULL,
  `status` varchar(1500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `norm_data` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `norm_iri` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `norm_type` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastopen_id` int(11) DEFAULT NULL,
  `lastopen_tab` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastopen_field` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastopen_tagid` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `projects_id` (`projects_id`),
  KEY `state` (`deleted`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`),
  KEY `lastopen_id` (`lastopen_id`),
  KEY `lastopen_tab` (`lastopen_tab`(255)),
  KEY `lastopen_feld` (`lastopen_field`(255)),
  KEY `lastopen_subid` (`lastopen_tagid`(255)),
  KEY `published` (`published`),
  KEY `articletype` (`articletype`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
CREATE TABLE `files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `version_id` int(11) DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  `published` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `name` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `root` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'root',
  `path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `isfolder` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `published` (`published`),
  KEY `name` (`name`(333)),
  KEY `type` (`type`),
  KEY `path` (`path`(333))
) ENGINE=MyISAM AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- Table structure for table `footnotes`
--

DROP TABLE IF EXISTS `footnotes`;
CREATE TABLE `footnotes` (
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
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `segment` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `root_id` int(11) DEFAULT NULL,
  `root_tab` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_id` int(11) DEFAULT NULL,
  `from_tab` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_field` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_tagname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_tagid` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `state` (`deleted`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`),
  KEY `rootrecord_id` (`root_id`),
  KEY `rootrecord_tab` (`root_tab`(255)),
  KEY `linkrec_id` (`from_id`),
  KEY `linkrec_tab` (`from_tab`(255)),
  KEY `linkrec_feld` (`from_field`(255)),
  KEY `linkrec_subid` (`from_tagid`(255)),
  KEY `published` (`published`),
  KEY `fntype` (`fntype`),
  KEY `from_tagname` (`from_tagname`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `grains`
--

DROP TABLE IF EXISTS `grains`;
CREATE TABLE `grains` (
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
  `itemtype` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` varchar(1500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_name` varchar(1500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_type` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_path` varchar(1500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_source` varchar(1500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_copyright` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `links_tab` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_id` int(11) DEFAULT NULL,
  `properties_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deleted` (`deleted`),
  KEY `properties_id` (`properties_id`),
  KEY `sortno` (`sortno`),
  KEY `itemtype` (`itemtype`(255)) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
CREATE TABLE `items` (
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
  `itemtype` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `properties_id` int(11) DEFAULT NULL,
  `value` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `translation` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `flagged` tinyint(1) DEFAULT NULL,
  `links_id` int(11) DEFAULT NULL,
  `links_tab` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `links_field` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `links_tagid` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_name` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_path` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_source` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_copyright` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_online` tinyint(1) DEFAULT NULL,
  `date_sort` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_value` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_add` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_start` double DEFAULT NULL,
  `date_end` double DEFAULT NULL,
  `source_autopsy` tinyint(1) DEFAULT NULL,
  `source_from` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_addition` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pos_x` int(11) DEFAULT NULL,
  `pos_y` int(11) DEFAULT NULL,
  `pos_z` int(11) DEFAULT NULL,
  `norm_iri` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `articles_id` int(11) DEFAULT NULL,
  `sections_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `state` (`deleted`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`),
  KEY `properties_id` (`properties_id`),
  KEY `orderidx` (`sortno`),
  KEY `sections_id` (`sections_id`),
  KEY `articles_id` (`articles_id`),
  KEY `published` (`published`),
  KEY `itemtype` (`itemtype`(255)),
  KEY `file_name` (`file_name`(1024)),
  KEY `file_path` (`file_path`(1024)),
  KEY `file_type` (`file_type`)
) ENGINE=InnoDB AUTO_INCREMENT=835 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `links`
--

DROP TABLE IF EXISTS `links`;
CREATE TABLE `links` (
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
  `root_tab` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_id` int(11) DEFAULT NULL,
  `from_tab` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_field` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_tagname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_tagid` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_id` int(11) DEFAULT NULL,
  `to_tab` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_field` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_tagid` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `state` (`deleted`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`),
  KEY `rootrecord_id` (`root_id`),
  KEY `rootrecord_tab` (`root_tab`(255)),
  KEY `linkrec_id` (`from_id`),
  KEY `linkrec_tab` (`from_tab`(255)),
  KEY `linkrec_feld` (`from_field`(255)),
  KEY `linkrec_subid` (`from_tagid`(255)),
  KEY `target_id` (`to_id`),
  KEY `target_tab` (`to_tab`(255)),
  KEY `target_feld` (`to_field`(255)),
  KEY `target_subid` (`to_tagid`(255)),
  KEY `published` (`published`),
  KEY `from_tagname` (`from_tagname`)
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `locktable`
--

DROP TABLE IF EXISTS `locktable`;
CREATE TABLE `locktable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lock_token` int(11) DEFAULT NULL,
  `lock_mode` int(11) DEFAULT NULL,
  `lock_table` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lock_segment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lock_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lockid` (`lock_token`),
  KEY `lockdatensatz` (`lock_id`),
  KEY `lockmode` (`lock_mode`),
  KEY `locksegment` (`lock_segment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dump completed on 2024-02-11 11:17:05
ALTER TABLE `locktable`
	ADD COLUMN `expires` DATETIME NULL DEFAULT NULL AFTER `lock_id`;


-- Table structure for table `meta`
--

DROP TABLE IF EXISTS `meta`;
CREATE TABLE `meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `created` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `name` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `state` (`deleted`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB AUTO_INCREMENT=269 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS `notes`;
CREATE TABLE `notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `version_id` int(11) DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  `published` int(11) DEFAULT NULL,
  `menu` tinyint(4) NOT NULL DEFAULT 1,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `notetype` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` char(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sortkey` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `format` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'markdown',
  `norm_iri` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `published` (`published`),
  KEY `norm_iri` (`norm_iri`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `version_id` int(11) DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  `published` int(11) DEFAULT NULL,
  `created` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `projecttype` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sortno` int(11) DEFAULT NULL,
  `name` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signature` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `norm_data` varchar(1500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `norm_iri` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `state` (`deleted`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`),
  KEY `published` (`published`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `properties`
--
DROP TABLE IF EXISTS `properties`;
CREATE TABLE `properties` (
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
  `sortkey` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `propertytype` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signature` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_name` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `properties_id` int(11) DEFAULT NULL,
  `lemma` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comment` mediumtext DEFAULT NULL,
  `content` mediumtext DEFAULT NULL,
  `elements` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keywords` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_from` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ishidden` tinyint(4) DEFAULT NULL,
  `iscategory` tinyint(4) DEFAULT NULL,
  `norm_type` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `norm_data` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `norm_iri` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `import_db` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `import_id` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `related_id` int(11) DEFAULT NULL,
  `mergedto_id` int(11) DEFAULT NULL,
  `splitfrom_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `lft` int(11) DEFAULT NULL,
  `rght` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `state` (`deleted`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`),
  KEY `orderidx` (`sortno`),
  KEY `parent_id` (`parent_id`),
  KEY `published` (`published`),
  KEY `related_id` (`related_id`),
  KEY `propertytype` (`propertytype`(255)),
  KEY `lemma` (`lemma`(1024)),
  KEY `tree` (`deleted`,`propertytype`,`level`,`lft`),
  KEY `lft` (`deleted`,`propertytype`,`lft`) USING BTREE,
  KEY `rght` (`deleted`,`propertytype`,`rght`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
CREATE TABLE `sections` (
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
  `sectiontype` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `number` int(11) DEFAULT NULL,
  `name` varchar(1500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alias` varchar(1500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comment` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `lft` int(11) DEFAULT NULL,
  `rght` int(11) DEFAULT NULL,
  `norm_iri` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `articles_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `state` (`deleted`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`),
  KEY `status` (`status`),
  KEY `articles_id` (`articles_id`),
  KEY `sectiontype` (`sectiontype`),
  KEY `published` (`published`)
) ENGINE=InnoDB AUTO_INCREMENT=444 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `token`
--

DROP TABLE IF EXISTS `token`;
CREATE TABLE `token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `created` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `usertoken` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sessiontoken` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `state` (`deleted`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`),
  KEY `userrole` (`usertoken`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `types`
--

DROP TABLE IF EXISTS `types`;
CREATE TABLE `types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `version_id` int(11) DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  `published` tinyint(4) DEFAULT NULL,
  `created` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `scope` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mode` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'default',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sortno` int(11) NOT NULL DEFAULT 0,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `caption` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `config` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `norm_iri` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sortno` (`sortno`),
  KEY `deleted` (`deleted`) USING BTREE,
  KEY `name` (`deleted`,`scope`,`name`) USING BTREE,
  KEY `mode` (`deleted`,`scope`,`name`,`mode`)
) ENGINE=InnoDB AUTO_INCREMENT=2992 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `version_id` int(11) DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  `created` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `usertype` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `acronym` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `userrole` int(11) DEFAULT NULL,
  `norm_iri` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `state` (`deleted`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`),
  KEY `userrole` (`userrole`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `types`
	ADD COLUMN `preset` VARCHAR(50) NOT NULL DEFAULT 'default' COLLATE 'utf8mb4_unicode_ci' AFTER `mode`,
	DROP INDEX `mode`,
	ADD INDEX `mode` (`deleted`, `scope`, `name`, `preset`) USING BTREE;

ALTER TABLE `notes` CHANGE COLUMN `format` `format` VARCHAR(15) NOT NULL DEFAULT 'html' COLLATE 'utf8mb4_unicode_ci' AFTER `content`;
ALTER TABLE `notes` CHANGE COLUMN `norm_iri` `norm_iri` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `format`;

