-- MariaDB dump 10.19  Distrib 10.11.6-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: test_sql    Database: test_newprojects
-- ------------------------------------------------------
-- Server version	10.11.6-MariaDB-1:10.11.6+maria~ubu2204

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
-- Table structure for table `articles`
--

DROP TABLE IF EXISTS `articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `articletype` varchar(50) DEFAULT NULL,
  `signature` varchar(1500) DEFAULT NULL,
  `name` varchar(1500) DEFAULT NULL,
  `sortno` int(11) DEFAULT NULL,
  `status` varchar(1500) DEFAULT NULL,
  `norm_data` varchar(1500) DEFAULT NULL,
  `norm_iri` varchar(1500) DEFAULT NULL,
  `norm_type` varchar(1500) DEFAULT NULL,
  `lastopen_id` int(11) DEFAULT NULL,
  `lastopen_tab` varchar(500) DEFAULT NULL,
  `lastopen_field` varchar(500) DEFAULT NULL,
  `lastopen_tagid` varchar(500) DEFAULT NULL,
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `articles`
--

LOCK TABLES `articles` WRITE;
/*!40000 ALTER TABLE `articles` DISABLE KEYS */;
/*!40000 ALTER TABLE `articles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `name` varchar(500) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `root` varchar(100) DEFAULT 'root',
  `path` varchar(500) DEFAULT NULL,
  `isfolder` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `published` (`published`),
  KEY `name` (`name`(250)),
  KEY `type` (`type`),
  KEY `path` (`path`(250)),
  KEY `root` (`root`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `files`
--

LOCK TABLES `files` WRITE;
/*!40000 ALTER TABLE `files` DISABLE KEYS */;
/*!40000 ALTER TABLE `files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `footnotes`
--

DROP TABLE IF EXISTS `footnotes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `name` varchar(200) DEFAULT NULL,
  `segment` text DEFAULT NULL,
  `content` mediumtext DEFAULT NULL,
  `root_id` int(11) DEFAULT NULL,
  `root_tab` varchar(500) DEFAULT NULL,
  `from_id` int(11) DEFAULT NULL,
  `from_tab` varchar(500) DEFAULT NULL,
  `from_field` varchar(500) DEFAULT NULL,
  `from_tagname` varchar(50) DEFAULT NULL,
  `from_tagid` varchar(500) DEFAULT NULL,
  `from_sort` int(11) DEFAULT NULL,
  `norm_iri` varchar(1500) DEFAULT NULL,
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `footnotes`
--

LOCK TABLES `footnotes` WRITE;
/*!40000 ALTER TABLE `footnotes` DISABLE KEYS */;
/*!40000 ALTER TABLE `footnotes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grains`
--

DROP TABLE IF EXISTS `grains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `graintype` varchar(500) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `file_name` varchar(1500) DEFAULT NULL,
  `file_type` varchar(10) DEFAULT NULL,
  `file_path` varchar(1500) DEFAULT NULL,
  `file_source` varchar(1500) DEFAULT NULL,
  `file_copyright` text DEFAULT NULL,
  `links_tab` varchar(50) DEFAULT NULL,
  `links_id` int(11) DEFAULT NULL,
  `properties_id` int(11) DEFAULT NULL,
  `norm_iri` varchar(1500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deleted` (`deleted`),
  KEY `properties_id` (`properties_id`),
  KEY `sortno` (`sortno`),
  KEY `itemtype` (`graintype`(255)) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grains`
--

LOCK TABLES `grains` WRITE;
/*!40000 ALTER TABLE `grains` DISABLE KEYS */;
/*!40000 ALTER TABLE `grains` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `itemtype` varchar(500) DEFAULT NULL,
  `properties_id` int(11) DEFAULT NULL,
  `value` varchar(1500) DEFAULT NULL,
  `content` mediumtext DEFAULT NULL,
  `translation` text DEFAULT NULL,
  `flagged` tinyint(1) DEFAULT NULL,
  `links_id` int(11) DEFAULT NULL,
  `links_tab` varchar(500) DEFAULT NULL,
  `links_field` varchar(500) DEFAULT NULL,
  `links_tagid` varchar(500) DEFAULT NULL,
  `file_name` varchar(1500) DEFAULT NULL,
  `file_type` varchar(10) DEFAULT NULL,
  `file_path` varchar(1500) DEFAULT NULL,
  `file_source` varchar(1500) DEFAULT NULL,
  `file_copyright` text DEFAULT NULL,
  `file_meta` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `file_online` tinyint(1) DEFAULT NULL,
  `date_sort` varchar(1500) DEFAULT NULL,
  `date_value` varchar(1500) DEFAULT NULL,
  `date_add` text DEFAULT NULL,
  `date_start` double DEFAULT NULL,
  `date_end` double DEFAULT NULL,
  `source_autopsy` tinyint(1) DEFAULT NULL,
  `source_from` text DEFAULT NULL,
  `source_addition` text DEFAULT NULL,
  `pos_x` int(11) DEFAULT NULL,
  `pos_y` int(11) DEFAULT NULL,
  `pos_z` int(11) DEFAULT NULL,
  `norm_iri` varchar(1500) DEFAULT NULL,
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
  KEY `file_name` (`file_name`(768)),
  KEY `file_path` (`file_path`(768)),
  KEY `file_type` (`file_type`),
  KEY `deleted` (`deleted`) USING BTREE,
  KEY `itemtype` (`deleted`,`itemtype`(255)) USING BTREE,
  KEY `countbyarticle` (`deleted`,`itemtype`,`articles_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `links`
--

DROP TABLE IF EXISTS `links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `root_tab` varchar(500) DEFAULT NULL,
  `from_id` int(11) DEFAULT NULL,
  `from_tab` varchar(500) DEFAULT NULL,
  `from_field` varchar(500) DEFAULT NULL,
  `from_tagname` varchar(50) DEFAULT NULL,
  `from_tagid` varchar(500) DEFAULT NULL,
  `to_id` int(11) DEFAULT NULL,
  `to_tab` varchar(500) DEFAULT NULL,
  `to_field` varchar(500) DEFAULT NULL,
  `to_tagid` varchar(500) DEFAULT NULL,
  `norm_iri` varchar(1500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`),
  KEY `rootrecord_id` (`root_id`),
  KEY `rootrecord_tab` (`root_tab`(255)),
  KEY `published` (`published`),
  KEY `from_tagname` (`from_tagname`),
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `links`
--

LOCK TABLES `links` WRITE;
/*!40000 ALTER TABLE `links` DISABLE KEYS */;
/*!40000 ALTER TABLE `links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `locktable`
--

DROP TABLE IF EXISTS `locktable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `locktable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lock_token` int(11) DEFAULT NULL,
  `lock_mode` int(11) DEFAULT NULL,
  `lock_table` varchar(1500) DEFAULT NULL,
  `lock_segment` varchar(255) DEFAULT NULL,
  `lock_id` int(11) DEFAULT NULL,
  `expires` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lockid` (`lock_token`),
  KEY `lockdatensatz` (`lock_id`),
  KEY `lockmode` (`lock_mode`),
  KEY `locksegment` (`lock_segment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `locktable`
--

LOCK TABLES `locktable` WRITE;
/*!40000 ALTER TABLE `locktable` DISABLE KEYS */;
/*!40000 ALTER TABLE `locktable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meta`
--

DROP TABLE IF EXISTS `meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `created` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `name` varchar(1500) DEFAULT NULL,
  `value` varchar(1500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`),
  KEY `deleted` (`deleted`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meta`
--

LOCK TABLES `meta` WRITE;
/*!40000 ALTER TABLE `meta` DISABLE KEYS */;
INSERT INTO `meta` VALUES
(1,0,'0000-00-00 00:00:00','2024-11-09 21:49:17',NULL,NULL,'db_version','4.5'),
(2,0,'0000-00-00 00:00:00','2024-11-09 21:49:17',NULL,NULL,'db_name','Epigraf');
/*!40000 ALTER TABLE `meta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS `notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `notetype` varchar(50) DEFAULT NULL,
  `name` char(200) DEFAULT NULL,
  `category` varchar(300) DEFAULT NULL,
  `sortkey` varchar(50) DEFAULT NULL,
  `content` mediumtext DEFAULT NULL,
  `format` varchar(15) NOT NULL DEFAULT 'html',
  `norm_iri` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `published` (`published`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notes`
--

LOCK TABLES `notes` WRITE;
/*!40000 ALTER TABLE `notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `projecttype` varchar(50) DEFAULT NULL,
  `sortno` int(11) DEFAULT NULL,
  `name` varchar(1500) DEFAULT NULL,
  `signature` varchar(1500) DEFAULT NULL,
  `description` mediumtext DEFAULT NULL,
  `norm_data` varchar(1500) DEFAULT NULL,
  `norm_iri` varchar(1500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`),
  KEY `published` (`published`),
  KEY `deleted` (`deleted`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `properties`
--

DROP TABLE IF EXISTS `properties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `sortkey` varchar(1500) DEFAULT NULL,
  `propertytype` varchar(500) DEFAULT NULL,
  `signature` varchar(1500) DEFAULT NULL,
  `file_name` varchar(1500) DEFAULT NULL,
  `properties_id` int(11) DEFAULT NULL,
  `lemma` varchar(1500) DEFAULT NULL,
  `name` varchar(1500) DEFAULT NULL,
  `unit` varchar(500) DEFAULT NULL,
  `comment` mediumtext DEFAULT NULL,
  `content` mediumtext DEFAULT NULL,
  `elements` text DEFAULT NULL,
  `keywords` varchar(1500) DEFAULT NULL,
  `source_from` text DEFAULT NULL,
  `ishidden` tinyint(4) DEFAULT NULL,
  `iscategory` tinyint(4) DEFAULT NULL,
  `norm_type` varchar(150) DEFAULT NULL,
  `norm_data` varchar(1500) DEFAULT NULL,
  `norm_iri` varchar(1500) DEFAULT NULL,
  `import_db` varchar(1500) DEFAULT NULL,
  `import_id` varchar(1500) DEFAULT NULL,
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
  KEY `deleted` (`deleted`) USING BTREE,
  KEY `lemma` (`lemma`(768)),
  KEY `tree` (`deleted`,`propertytype`,`level`,`lft`),
  KEY `lft` (`deleted`,`propertytype`,`lft`) USING BTREE,
  KEY `rght` (`deleted`,`propertytype`,`rght`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `properties`
--

LOCK TABLES `properties` WRITE;
/*!40000 ALTER TABLE `properties` DISABLE KEYS */;
/*!40000 ALTER TABLE `properties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `sectiontype` varchar(200) NOT NULL DEFAULT '',
  `number` int(11) DEFAULT NULL,
  `name` varchar(1500) DEFAULT NULL,
  `alias` varchar(1500) DEFAULT NULL,
  `comment` mediumtext DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `lft` int(11) DEFAULT NULL,
  `rght` int(11) DEFAULT NULL,
  `norm_iri` varchar(1500) DEFAULT NULL,
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sections`
--

LOCK TABLES `sections` WRITE;
/*!40000 ALTER TABLE `sections` DISABLE KEYS */;
/*!40000 ALTER TABLE `sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `token`
--

DROP TABLE IF EXISTS `token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `created` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `usertoken` varchar(255) DEFAULT NULL,
  `sessiontoken` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`),
  KEY `userrole` (`usertoken`),
  KEY `deleted` (`deleted`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `token`
--

LOCK TABLES `token` WRITE;
/*!40000 ALTER TABLE `token` DISABLE KEYS */;
/*!40000 ALTER TABLE `token` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `types`
--

DROP TABLE IF EXISTS `types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `scope` varchar(50) DEFAULT NULL,
  `mode` varchar(50) NOT NULL DEFAULT 'default',
  `preset` varchar(50) NOT NULL DEFAULT 'default',
  `name` varchar(100) DEFAULT NULL,
  `sortno` int(11) NOT NULL DEFAULT 0,
  `category` varchar(100) DEFAULT NULL,
  `caption` varchar(200) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `config` mediumtext DEFAULT NULL,
  `norm_iri` varchar(1500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sortno` (`sortno`),
  KEY `deleted` (`deleted`) USING BTREE,
  KEY `name` (`deleted`,`scope`,`name`) USING BTREE,
  KEY `mode` (`deleted`,`scope`,`name`,`preset`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `types`
--

LOCK TABLES `types` WRITE;
/*!40000 ALTER TABLE `types` DISABLE KEYS */;
/*!40000 ALTER TABLE `types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `version_id` int(11) DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  `created` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `usertype` varchar(50) DEFAULT NULL,
  `name` varchar(1500) DEFAULT NULL,
  `acronym` varchar(1500) DEFAULT NULL,
  `userrole` int(11) DEFAULT NULL,
  `norm_iri` varchar(1500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`),
  KEY `userrole` (`userrole`),
  KEY `deleted` (`deleted`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-11-09 22:49:17
