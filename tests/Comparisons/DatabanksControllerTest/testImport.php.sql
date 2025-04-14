-- MariaDB dump 10.19  Distrib 10.11.6-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: test_sql    Database: test_projects
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
  KEY `state` (`deleted`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`),
  KEY `lastopen_id` (`lastopen_id`),
  KEY `lastopen_tab` (`lastopen_tab`(255)),
  KEY `lastopen_feld` (`lastopen_field`(255)),
  KEY `lastopen_subid` (`lastopen_tagid`(255)),
  KEY `published` (`published`),
  KEY `articletype` (`articletype`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `articles`
--

LOCK TABLES `articles` WRITE;
/*!40000 ALTER TABLE `articles` DISABLE KEYS */;
INSERT INTO `articles` VALUES
(1,5,0,NULL,NULL,NULL,'2007-08-20 20:00:00','2021-05-01 16:59:54',36,1,'epi-article','dbr.rerik.alt-gaarz.glocke1','',NULL,'neu angelegt',NULL,NULL,NULL,1,'articles','projekt',''),
(2,5,0,NULL,NULL,NULL,'2007-08-20 20:00:00','2021-05-01 16:59:54',36,1,'epi-article','dbr.rerik.alt-gaarz.glocke2','',NULL,'neu angelegt',NULL,NULL,NULL,2,'articles','projekt',''),
(3,5,0,NULL,NULL,NULL,'2007-08-20 20:00:00','2021-05-01 16:59:54',36,1,'epi-article','dbr.rerik.alt-gaarz.glocke3','',NULL,'neu angelegt',NULL,NULL,NULL,3,'articles','projekt',''),
(4,5,0,NULL,NULL,NULL,'2007-08-20 20:00:00','2021-05-01 16:59:54',36,1,'epi-article','dbr.rerik.alt-gaarz.gp-oertzen1','',NULL,'neu angelegt',NULL,NULL,NULL,4,'articles','projekt',''),
(5,5,0,NULL,NULL,NULL,'2007-08-20 20:00:00','2021-05-01 16:59:54',36,1,'epi-article','dbr.rerik.alt-gaarz.gp-oertzen2','Oertzen, Vicke von; Stralendorff, Adelheid von',NULL,'',NULL,NULL,NULL,5,'articles','projekt',''),
(6,5,0,NULL,NULL,NULL,'2007-08-20 20:00:00','2021-05-01 16:59:54',34,1,'epi-article','dbr.althof.woizlawa','Woizlawa',NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(7,5,0,NULL,NULL,NULL,'2007-08-14 20:00:00','2021-05-01 16:59:54',4,2,'epi-article','dbr.hornstorf.gp-johann','Johann, Pfarrer',NULL,'',NULL,NULL,NULL,7,'articles','status',''),
(8,5,0,NULL,NULL,NULL,'2007-06-27 20:00:00','2021-05-01 16:59:54',4,2,'epi-article','dbr.kavelstorf.gp-rüze','Rüze, Werner + Bertha',NULL,'',NULL,NULL,NULL,8,'items_bearbeitungen','uebersetzung',''),
(9,5,0,NULL,NULL,NULL,'2006-10-04 20:00:00','2021-05-01 16:59:54',36,3,'epi-article','dbr.klosterkirche.holztafel-biddet','Balthasar, Herzog; Erich, Herzog; Ursula, Herzogin',NULL,'',NULL,NULL,NULL,9,'articles','projekt','');
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
  `type` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `root` varchar(100) DEFAULT 'root',
  `path` varchar(500) DEFAULT NULL,
  `isfolder` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `published` (`published`),
  KEY `name` (`name`(250)),
  KEY `type` (`type`),
  KEY `path` (`path`(250))
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
  `name` varchar(200) NOT NULL DEFAULT '',
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
  `itemtype` varchar(500) DEFAULT NULL,
  `value` varchar(1500) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `file_name` varchar(1500) DEFAULT NULL,
  `file_type` varchar(10) DEFAULT NULL,
  `file_path` varchar(1500) DEFAULT NULL,
  `file_source` varchar(1500) DEFAULT NULL,
  `file_copyright` text DEFAULT NULL,
  `links_tab` varchar(50) DEFAULT NULL,
  `link_id` int(11) DEFAULT NULL,
  `properties_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deleted` (`deleted`),
  KEY `properties_id` (`properties_id`),
  KEY `sortno` (`sortno`),
  KEY `itemtype` (`itemtype`(255)) USING BTREE
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
  `norm_iri` varchar(50) DEFAULT NULL,
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
  KEY `file_name` (`file_name`(768)),
  KEY `file_path` (`file_path`(768)),
  KEY `file_type` (`file_type`)
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
  KEY `state` (`deleted`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB AUTO_INCREMENT=229 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meta`
--

LOCK TABLES `meta` WRITE;
/*!40000 ALTER TABLE `meta` DISABLE KEYS */;
INSERT INTO `meta` VALUES
(228,0,'2020-08-12 10:18:51','2020-08-12 10:18:51',NULL,NULL,'db_version','4.5');
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
  `notetype` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` char(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sortkey` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `format` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'html',
  `norm_iri` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `published` (`published`),
  KEY `norm_iri` (`norm_iri`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
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
  KEY `state` (`deleted`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`),
  KEY `published` (`published`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES
(1,0,NULL,NULL,NULL,'2021-06-14 18:42:26','2022-06-18 06:13:06',1,1,NULL,NULL,'Testprojekt','TP',NULL,NULL,'bookiri');
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
  KEY `state` (`deleted`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`),
  KEY `orderidx` (`sortno`),
  KEY `parent_id` (`parent_id`),
  KEY `published` (`published`),
  KEY `related_id` (`related_id`),
  KEY `propertytype` (`propertytype`(255)),
  KEY `lemma` (`lemma`(768)),
  KEY `tree` (`deleted`,`propertytype`,`level`,`lft`),
  KEY `lft` (`deleted`,`propertytype`,`lft`) USING BTREE,
  KEY `rght` (`deleted`,`propertytype`,`rght`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=23919 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `properties`
--

LOCK TABLES `properties` WRITE;
/*!40000 ALTER TABLE `properties` DISABLE KEYS */;
INSERT INTO `properties` VALUES
(23,0,NULL,NULL,NULL,'2008-05-05 16:50:11','2021-12-05 19:07:06',NULL,5,9,'0','alignments',NULL,NULL,NULL,'0','keine Angabe',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,9,10),
(29,0,NULL,NULL,NULL,'2008-05-05 16:50:11','2021-12-05 19:07:06',NULL,5,37,'circ','alignments',NULL,NULL,NULL,'circ','umlaufend',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,37,38),
(54,0,NULL,NULL,NULL,'2008-05-05 16:50:11','2021-12-05 19:07:10',NULL,5,5,'lig','ligatures',NULL,NULL,NULL,'lig','Ligatur',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,5,6),
(67,0,NULL,NULL,NULL,'2008-05-05 16:50:13','2021-12-05 19:07:12',NULL,5,1,'0','outputoptions',NULL,NULL,NULL,'0','mit Notizen',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,1,2),
(78,0,NULL,NULL,NULL,'2008-05-05 16:50:11','2021-12-05 19:07:07',NULL,5,11,'traditio4','conditions',NULL,NULL,NULL,'traditio4','(†) Objekt zerstört, aber Inschriftenteile im Original erhalten',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,11,12),
(105,0,NULL,NULL,NULL,'2008-05-05 16:50:13','2021-12-05 19:07:10',NULL,5,2,'1','indentations',NULL,NULL,NULL,'1','nicht eingerückt',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,3,4),
(107,0,NULL,NULL,NULL,'2008-05-05 16:50:13','2021-12-05 19:07:19',NULL,NULL,1,'subhl','verticalalignments',NULL,NULL,NULL,'subhl','unter der Oberlinie',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,1,2),
(125,0,NULL,NULL,NULL,'2008-05-05 16:50:12','2021-12-05 19:07:19',NULL,5,40,'Doppelpunkt','wordseparators',NULL,NULL,NULL,'Doppelpunkt','Doppelpunkt',NULL,'nach einer Raute # kann angegeben werden, welches Zeichen in der Transkription für diesen Worttrenner erscheinen soll; wird kein Zeichen angegeben, erfolgt die Ausgabe als Hochpunkt ·',NULL,NULL,'',NULL,0,0,NULL,'',NULL,'','',NULL,NULL,NULL,NULL,0,37,42),
(133,0,NULL,NULL,NULL,'2008-05-05 16:50:12','2021-12-05 19:07:19',NULL,5,92,'Hochpunkt','wordseparators',NULL,NULL,NULL,'Hochpunkt','Hochpunkt',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,89,90),
(137,0,NULL,NULL,NULL,'2008-05-05 16:50:12','2021-12-05 19:07:19',NULL,34,137,'Punkt#.','wordseparators',NULL,NULL,NULL,'Punkt#.','Punkt (Satzzeichen)',NULL,'nach einer Raute # kann angegeben werden, welches Zeichen in der Transkription für diesen Worttrenner erscheinen soll; wird kein Zeichen angegeben, erfolgt die Ausgabe als Hochpunkt ·',NULL,NULL,'',NULL,0,0,NULL,'',NULL,'','',NULL,NULL,NULL,NULL,0,131,132),
(253,0,NULL,NULL,NULL,'2008-05-05 16:50:12','2021-12-05 19:07:10',NULL,5,6,'nz','linebindings',NULL,NULL,NULL,'nz','neue Zeile',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,11,12),
(254,0,NULL,NULL,NULL,'2008-05-05 16:50:12','2021-12-05 19:07:10',NULL,5,7,'nf','linebindings',NULL,NULL,NULL,'nf','neues Inschriftenfeld',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,13,14),
(264,0,NULL,NULL,NULL,'2008-05-05 16:50:15','2021-12-05 19:07:12',NULL,5,37,'Hexameter','metres',NULL,NULL,NULL,'Hexameter','Hexameter',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,35,58),
(266,0,NULL,NULL,NULL,'2008-05-05 16:50:15','2021-12-05 19:07:12',NULL,5,55,'zweisilbig leoninisch gereimt','metres',NULL,NULL,NULL,'zweisilbig leoninisch gereimt','Hexameter, zweisilbig leoninisch gereimt',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,264,1,52,55),
(293,0,NULL,NULL,NULL,'2016-03-09 11:09:03','2022-01-24 22:30:03',5,5,43,NULL,'metres',NULL,NULL,NULL,'siehe',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,294,NULL,NULL,264,1,40,41),
(298,0,NULL,NULL,NULL,'2016-06-09 11:42:38','2022-01-24 22:30:03',5,5,8,NULL,'metres',NULL,NULL,NULL,'siehe',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,287,NULL,NULL,299,1,8,9),
(300,0,NULL,NULL,NULL,'2017-02-17 14:35:42','2022-01-24 22:30:03',5,5,14,NULL,'metres',NULL,NULL,NULL,'siehe',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,270,NULL,NULL,19303,1,14,15),
(329,0,NULL,NULL,NULL,'2008-05-05 16:50:13','2021-12-05 19:07:19',NULL,4,11,'Anrufung','texttypes',NULL,NULL,NULL,'Anrufung','Anrufung',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,11,14),
(341,0,NULL,NULL,NULL,'2008-05-05 16:50:13','2021-12-05 19:07:19',NULL,4,65,'Fertigungsdatum','texttypes',NULL,NULL,NULL,'Fertigungsdatum','Fertigungsdatum',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,63,64),
(346,0,NULL,NULL,NULL,'2008-05-05 16:50:14','2021-12-05 19:07:19',NULL,4,73,'Fürbitte','texttypes',NULL,NULL,NULL,'Fürbitte','Fürbitte',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,71,72),
(347,0,NULL,NULL,NULL,'2008-05-05 16:50:14','2021-12-05 19:07:19',NULL,4,22,'Aufforderung zur Fürbitte','texttypes',NULL,NULL,NULL,'Aufforderung zur Fürbitte','Aufforderung zur Fürbitte',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,21,22),
(350,0,NULL,NULL,NULL,'2008-05-05 16:50:14','2021-12-05 19:07:19',NULL,4,104,'Glockenspruch','texttypes',NULL,NULL,NULL,'Glockenspruch','Glockenspruch',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,99,100),
(368,0,NULL,NULL,NULL,'2008-05-05 16:50:14','2021-12-05 19:07:19',NULL,5,230,'Sterbevermerk','texttypes',NULL,NULL,NULL,'Sterbevermerk','Sterbevermerk',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,223,228),
(393,0,NULL,NULL,NULL,'2009-06-19 09:53:17','2022-01-24 22:30:03',4,4,77,NULL,'texttypes',NULL,NULL,NULL,'siehe',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,329,NULL,NULL,345,1,74,75),
(394,0,NULL,NULL,NULL,'2009-06-19 09:53:17','2022-01-24 22:30:03',4,4,79,NULL,'texttypes',NULL,NULL,NULL,'siehe',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,346,NULL,NULL,345,1,76,77),
(458,0,NULL,NULL,NULL,'2008-05-05 16:50:15','2021-12-05 19:07:08',NULL,5,56,'gotische Majuskel','fonttypes',NULL,NULL,NULL,'gotische Majuskel','gotische Majuskel',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,55,64),
(460,0,NULL,NULL,NULL,'2008-05-05 16:50:15','2021-12-05 19:07:08',NULL,5,66,'gotische Minuskel','fonttypes',NULL,NULL,NULL,'gotische Minuskel','gotische Minuskel',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,65,134),
(461,0,NULL,NULL,NULL,'2008-05-05 16:50:16','2021-12-05 19:07:08',NULL,5,91,'mit Versal','fonttypes',NULL,NULL,NULL,'mit Versal','gotische Minuskel mit Versal',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,460,1,90,91),
(1027,0,NULL,NULL,NULL,'2008-10-23 12:29:00','2022-01-24 22:30:03',4,33,2268,NULL,'epithets',NULL,NULL,NULL,'siehe',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1026,NULL,NULL,19414,2,2039,2040),
(1127,0,NULL,NULL,NULL,'2009-04-27 10:20:31','2022-01-24 22:30:03',4,33,3052,NULL,'epithets',NULL,NULL,NULL,'siehe',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1026,NULL,NULL,19518,2,2767,2768),
(7629,0,NULL,NULL,NULL,'2008-05-05 16:50:07','2021-12-05 19:07:12',NULL,5,185,'Glocke','objecttypes',NULL,NULL,NULL,'Glocke','Glocke',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,177,178),
(7631,0,NULL,NULL,NULL,'2008-05-05 16:50:07','2021-12-05 19:07:12',NULL,5,205,'Grabplatte','objecttypes',NULL,NULL,NULL,'Grabplatte','Grabplatte',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,197,248),
(7691,0,NULL,NULL,NULL,'2008-05-05 16:50:09','2021-12-05 19:07:12',NULL,5,628,'Ziegelstein','objecttypes',NULL,NULL,NULL,'Ziegelstein','Ziegelstein',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,607,608),
(7743,0,NULL,NULL,NULL,'2010-02-23 08:49:55','2021-12-05 19:07:12',15,5,295,'Holztafel','objecttypes',NULL,NULL,NULL,'Holztafel','Holztafel ',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,285,286),
(8124,1,NULL,NULL,NULL,'2008-05-05 16:50:22','2021-05-11 11:41:34',NULL,5,1,'Ldkr. Bad Doberan','locations',NULL,NULL,NULL,'Ldkr. Bad Doberan','Bad Doberan',NULL,NULL,NULL,NULL,'dbr',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,1,98),
(8125,0,NULL,NULL,NULL,'2008-05-05 16:50:23','2021-12-05 19:07:11',NULL,5,1171,'Rerik (Alt-Gaarz), Kirche','locations',NULL,NULL,NULL,'Rerik (Alt-Gaarz), Kirche','Bad Doberan, Rerik (Alt-Gaarz), Kirche',NULL,NULL,NULL,NULL,'dbr',NULL,0,0,NULL,'',NULL,'','',NULL,NULL,NULL,9197,1,1170,1171),
(8126,0,NULL,NULL,NULL,'2008-05-05 16:50:25','2021-12-05 19:07:11',NULL,5,989,'Althof, Klosterkapelle ','locations',NULL,NULL,NULL,'Althof, Klosterkapelle ','Bad Doberan, Althof, Klosterkapelle ',NULL,NULL,NULL,NULL,'',NULL,0,0,NULL,'',NULL,'','',NULL,NULL,NULL,9197,1,988,989),
(8127,0,NULL,NULL,NULL,'2008-05-05 16:50:25','2021-12-05 19:07:11',NULL,5,1111,'Hornstorf, Kirche','locations',NULL,NULL,NULL,'Hornstorf, Kirche','Bad Doberan, Hornstorf, Kirche',NULL,NULL,NULL,NULL,'',NULL,0,0,NULL,'',NULL,'','',NULL,NULL,NULL,9197,1,1110,1111),
(8128,0,NULL,NULL,NULL,'2008-05-05 16:50:26','2021-12-05 19:07:11',NULL,5,1119,'Kavelstorf, Kirche','locations',NULL,NULL,NULL,'Kavelstorf, Kirche','Bad Doberan, Kavelstorf, Kirche',NULL,NULL,NULL,NULL,'',NULL,0,0,NULL,'',NULL,'','',NULL,NULL,NULL,9197,1,1118,1119),
(8129,0,NULL,NULL,NULL,'2008-05-05 16:50:26','2021-12-05 19:07:11',NULL,5,1019,'Bad Doberan, Zisterzienserkloster, Kirche','locations',NULL,NULL,NULL,'Bad Doberan, Zisterzienserkloster, Kirche','Bad Doberan, Zisterzienserkloster, Kirche',NULL,NULL,NULL,NULL,'',NULL,0,0,NULL,'',NULL,'','',NULL,NULL,NULL,9197,1,1018,1019),
(10174,0,NULL,NULL,NULL,'2008-05-05 16:51:24','2021-12-05 19:07:12',NULL,5,26,'Holz','materials',NULL,NULL,NULL,'Holz','Holz',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,25,68),
(10255,0,NULL,NULL,NULL,'2012-02-22 09:25:04','2021-12-05 19:07:12',4,5,29,'bemalt','materials',NULL,NULL,NULL,'bemalt','Holz, bemalt ',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,10174,1,28,29),
(10426,0,NULL,NULL,NULL,'2008-05-05 16:50:09','2021-12-05 19:07:10',NULL,5,98,'deutsch','languages',NULL,NULL,NULL,'deutsch','deutsch',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,97,122),
(10434,0,NULL,NULL,NULL,'2008-05-05 16:50:10','2021-12-05 19:07:10',NULL,5,161,'lateinisch','languages',NULL,NULL,NULL,'lateinisch','lateinisch',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,139,144),
(10437,0,NULL,NULL,NULL,'2008-05-05 16:50:10','2021-12-05 19:07:10',NULL,5,120,'niederdeutsch','languages',NULL,NULL,NULL,'niederdeutsch','niederdeutsch',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,10426,1,112,121),
(10494,0,NULL,NULL,NULL,'2008-05-05 16:50:10','2021-12-05 19:07:19',NULL,5,36,'erhaben in vertiefter Zeile','techniques',NULL,NULL,NULL,'erhaben in vertiefter Zeile','erhaben in vertiefter Zeile',NULL,NULL,NULL,NULL,'',NULL,0,0,NULL,'',NULL,'','',NULL,NULL,NULL,23918,1,36,37),
(19899,0,NULL,NULL,NULL,'2017-11-07 10:45:29','2022-01-24 22:30:03',5,5,37,NULL,'wordseparators',NULL,NULL,NULL,'siehe',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,135,NULL,NULL,19900,1,34,35),
(19913,0,NULL,NULL,NULL,'2017-11-15 15:12:56','2022-01-24 22:30:03',5,5,2,NULL,'wordseparators',NULL,NULL,NULL,'siehe',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,135,NULL,NULL,19914,1,2,3),
(20852,0,NULL,NULL,NULL,NULL,'2021-12-05 19:07:07',NULL,5,6,'Beschreibung','captions',NULL,NULL,NULL,'Beschreibung','Beschreibung',NULL,NULL,NULL,NULL,'',NULL,0,0,NULL,'',NULL,'abschnitte.csv','6',NULL,NULL,NULL,NULL,0,11,12),
(20853,0,NULL,NULL,NULL,NULL,'2021-12-05 19:07:07',NULL,5,11,'Kommentar','captions',NULL,NULL,NULL,'Kommentar','Kommentar',NULL,NULL,NULL,NULL,'',NULL,0,0,NULL,'',NULL,'abschnitte.csv','11',NULL,NULL,NULL,NULL,0,21,22),
(20884,0,NULL,NULL,NULL,'2008-05-05 16:50:16','2021-12-05 19:07:10',NULL,4,1733,'MUB = Mecklenburgisches Urkundenbuch, hg. vom Verein für Mecklenburgische Geschichte und Altertumskunde, 25 Bde., Schwerin 1863–1936','literature',NULL,NULL,NULL,'MUB = Mecklenburgisches Urkundenbuch, hg. vom Verein für Mecklenburgische Geschichte und Altertumskunde, 25 Bde., Schwerin 1863–1936','MUB',NULL,NULL,NULL,NULL,'hwi',NULL,0,0,NULL,'',NULL,'literatur.csv','1198',NULL,NULL,NULL,22915,1,1728,1729),
(20890,0,NULL,NULL,NULL,'2008-05-05 16:50:17','2021-12-05 19:07:11',NULL,5,2169,'Schlie, Geschichts-Denkmäler = Friedrich Schlie, Die Kunst- und Geschichts-Denkmäler des Grossherzogthums Mecklenburg-Schwerin, 5 Bde., Schwerin 1896–1902','literature',NULL,NULL,NULL,'Schlie, Geschichts-Denkmäler = Friedrich Schlie, Die Kunst- und Geschichts-Denkmäler des Grossherzogthums Mecklenburg-Schwerin, 5 Bde., Schwerin 1896–1902','Schlie, Geschichts-Denkmäler',NULL,NULL,NULL,NULL,'hwi',NULL,0,0,NULL,'',NULL,'literatur.csv','1974',NULL,NULL,NULL,22915,1,2150,2151),
(21008,0,NULL,NULL,NULL,'2008-05-05 16:50:17','2021-12-05 19:07:10',NULL,5,1165,'Kühne, Doberan = Wilhelm Kühne, Die Kirche zu Doberan. Ein Führer durch ihre geschichtlichen und religiösen Denkmäler, 2. Aufl., Rostock 1938','literature',NULL,NULL,NULL,'Kühne, Doberan = Wilhelm Kühne, Die Kirche zu Doberan. Ein Führer durch ihre geschichtlichen und religiösen Denkmäler, 2. Aufl., Rostock 1938','Kühne, Doberan',NULL,NULL,NULL,NULL,'',NULL,0,0,NULL,'',NULL,'literatur.csv','3020',NULL,NULL,NULL,22915,1,1164,1165),
(21081,0,NULL,NULL,NULL,'2008-05-05 16:50:18','2021-12-05 19:07:10',NULL,5,1243,'Lisch, Alt-Doberan = Georg Christian Friedrich Lisch, Das Kloster Alt-Doberan zu Althof und Woizlava, des Obotriten-Königs Pribislav Gemahlin, in: Jahrbücher des Vereins für meklenburgische Geschichte und Alterthumskunde 2 (1837), S. 1–36','literature',NULL,NULL,NULL,'Lisch, Alt-Doberan = Georg Christian Friedrich Lisch, Das Kloster Alt-Doberan zu Althof und Woizlava, des Obotriten-Königs Pribislav Gemahlin, in: Jahrbücher des Vereins für meklenburgische Geschichte und Alterthumskunde 2 (1837), S. 1–36','Lisch, Alt-Doberan',NULL,NULL,NULL,NULL,'',NULL,0,0,NULL,'',NULL,'literatur.csv','3985',NULL,NULL,NULL,22915,1,1238,1239),
(21083,0,NULL,NULL,NULL,'2008-05-05 16:50:18','2021-12-05 19:07:10',NULL,5,1245,'Lisch, Alt-Gaarz = Georg Christian Friedrich Lisch, Die Kirche zu Alt-Gaarz, in: Jahrbücher des Vereins für meklenburgische Geschichte und Alterthumskunde 10 (1845), S. 311–313','literature',NULL,NULL,NULL,'Lisch, Alt-Gaarz = Georg Christian Friedrich Lisch, Die Kirche zu Alt-Gaarz, in: Jahrbücher des Vereins für meklenburgische Geschichte und Alterthumskunde 10 (1845), S. 311–313','Lisch, Alt-Gaarz',NULL,NULL,NULL,NULL,'',NULL,0,0,NULL,'',NULL,'literatur.csv','3808',NULL,NULL,NULL,22915,1,1240,1241),
(21204,0,NULL,NULL,NULL,'2008-06-17 07:32:48','2021-12-05 19:07:11',NULL,5,2529,'Voss, Doberan = Johannes Voss, Das Münster zu Bad Doberan. (…) mit Aufnahmen von Jutta Brüdern, München, Berlin 2008','literature',NULL,NULL,NULL,'Voss, Doberan = Johannes Voss, Das Münster zu Bad Doberan. (…) mit Aufnahmen von Jutta Brüdern, München, Berlin 2008','Voss, Doberan',NULL,NULL,NULL,NULL,'',NULL,0,0,NULL,'',NULL,'literatur.csv','4392',NULL,NULL,NULL,22915,1,2508,2509),
(21288,0,NULL,NULL,NULL,'2009-05-13 06:15:33','2021-12-05 19:07:10',1,5,1091,'Kratzke, Sepulkraldenkmäler','literature',NULL,NULL,NULL,'Kratzke, Sepulkraldenkmäler','Kratzke, Sepulkraldenkmäler',NULL,'#4654',NULL,NULL,'',NULL,0,0,NULL,'',NULL,'literatur.csv','4654',NULL,NULL,NULL,22915,1,1090,1091),
(21624,0,NULL,NULL,NULL,'2012-06-07 07:19:54','2021-12-05 19:07:11',15,5,2607,'Wigger, Althof = Friedrich Wigger, Ueber die Inschrift von Althof, in: Jahrbücher des Vereins für meklenburgische Geschichte und Alterthumskunde 15 (1850), S. 166f','literature',NULL,NULL,NULL,'Wigger, Althof = Friedrich Wigger, Ueber die Inschrift von Althof, in: Jahrbücher des Vereins für meklenburgische Geschichte und Alterthumskunde 15 (1850), S. 166f','Wigger, Althof',NULL,NULL,NULL,NULL,'',NULL,0,0,NULL,'',NULL,'literatur.csv','3972',NULL,NULL,NULL,22915,1,2586,2587),
(21625,0,NULL,NULL,NULL,'2012-06-07 12:08:43','2021-12-05 19:07:10',4,5,1713,'Minneker, Kloster = Ilka Minneker, Vom Kloster zur Residenz. Dynastische Memoria und Repräsentation im spätmittelalterlichen und frühneuzeitlichen Mecklenburg (Symbolische Kommunikation und gesellschaftliche Wertesysteme. Schriftenreihe des Sonderforschungsbereichs 496 18), Münster 2007','literature',NULL,NULL,NULL,'Minneker, Kloster = Ilka Minneker, Vom Kloster zur Residenz. Dynastische Memoria und Repräsentation im spätmittelalterlichen und frühneuzeitlichen Mecklenburg (Symbolische Kommunikation und gesellschaftliche Wertesysteme. Schriftenreihe des Sonderforschungsbereichs 496 18), Münster 2007','Minneker, Kloster',NULL,NULL,NULL,NULL,'',NULL,0,0,NULL,'',NULL,'literatur.csv','4452',NULL,NULL,NULL,22915,1,1708,1709),
(21910,0,NULL,NULL,NULL,'2016-05-03 11:16:51','2021-12-05 19:07:10',29,5,921,'Hübner, Glockenritzungen = Kurt Hübner, Die mittelalterlichen Glockenritzungen (Schriften zur Kunstgeschichte 12), Berlin 1968','literature',NULL,NULL,NULL,'Hübner, Glockenritzungen = Kurt Hübner, Die mittelalterlichen Glockenritzungen (Schriften zur Kunstgeschichte 12), Berlin 1968','Hübner, Glockenritzungen',NULL,NULL,NULL,NULL,'',NULL,0,0,NULL,'',NULL,'literatur.csv','2167',NULL,NULL,NULL,22915,1,920,921),
(22915,0,NULL,NULL,NULL,'2019-02-23 08:01:37','2021-12-05 19:07:11',5,5,1,'Gedruckte Quellen und Literatur','literature',NULL,NULL,NULL,'Gedruckte Quellen und Literatur','Literatur ',NULL,NULL,NULL,NULL,'',NULL,0,0,NULL,'',NULL,'','',NULL,NULL,NULL,NULL,0,1,2672),
(23918,0,NULL,NULL,NULL,'2020-06-02 11:37:17','2021-12-05 19:07:19',5,5,29,'gehauen','techniques',NULL,NULL,NULL,'gehauen','gehauen ',NULL,NULL,NULL,NULL,'',NULL,0,0,NULL,'',NULL,'','',NULL,NULL,NULL,NULL,0,29,46);
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
  KEY `state` (`deleted`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`),
  KEY `status` (`status`),
  KEY `articles_id` (`articles_id`),
  KEY `sectiontype` (`sectiontype`),
  KEY `published` (`published`)
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
-- Table structure for table `testtable`
--

DROP TABLE IF EXISTS `testtable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testtable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `published` int(11) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `name` varchar(1500) DEFAULT NULL,
  `shortname` varchar(1500) DEFAULT NULL,
  `book_number` int(11) DEFAULT NULL,
  `book_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `state` (`deleted`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`),
  KEY `published` (`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testtable`
--

LOCK TABLES `testtable` WRITE;
/*!40000 ALTER TABLE `testtable` DISABLE KEYS */;
/*!40000 ALTER TABLE `testtable` ENABLE KEYS */;
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
  KEY `state` (`deleted`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`),
  KEY `userrole` (`usertoken`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `token`
--

LOCK TABLES `token` WRITE;
/*!40000 ALTER TABLE `token` DISABLE KEYS */;
INSERT INTO `token` VALUES
(1,0,'2020-09-09 10:23:18','2020-10-29 12:00:58',1,1,'TESTTOKENAUTHOR','1234');
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
  `norm_iri` varchar(100) DEFAULT NULL,
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
  `norm_iri` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `state` (`deleted`),
  KEY `modified_by` (`modified_by`),
  KEY `created_by` (`created_by`),
  KEY `userrole` (`userrole`)
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

-- Dump completed on 2025-03-27 13:59:36
