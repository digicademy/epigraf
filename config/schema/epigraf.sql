CREATE TABLE IF NOT EXISTS `databanks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `version` varchar(5) NOT NULL,
  `category` varchar(200) DEFAULT NULL,
  `description` mediumtext DEFAULT NULL,
  `published` tinyint(4) DEFAULT NULL,
  `iriprefix` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `docs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  `version_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `published` tinyint(4) NOT NULL DEFAULT 0,
  `menu` tinyint(4) NOT NULL DEFAULT 1,
  `segment` char(10) DEFAULT 'wiki',
  `sortkey` varchar(50) DEFAULT '',
  `name` varchar(200) DEFAULT NULL,
  `category` varchar(300) DEFAULT NULL,
  `content` mediumtext DEFAULT NULL,
  `format` varchar(50) NOT NULL DEFAULT 'html',
  `norm_iri` varchar(1500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `norm_iri` (`norm_iri`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `published` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `name` varchar(500) NOT NULL,
  `description` text DEFAULT NULL,
  `config` longtext DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `root` varchar(100) DEFAULT 'root',
  `path` varchar(500) DEFAULT NULL,
  `isfolder` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `published` (`published`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `jobs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `typ` varchar(100) NOT NULL,
  `status` varchar(50) NOT NULL,
  `progress` int(11) NOT NULL DEFAULT 0,
  `progressmax` int(11) NOT NULL DEFAULT 0,
  `config` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_session` int(11) DEFAULT NULL,
  `user_role` varchar(50) DEFAULT NULL,
  `user_request` varchar(50) DEFAULT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_name` varchar(50) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `permission_type` varchar(200) DEFAULT NULL,
  `permission_name` varchar(200) DEFAULT NULL,
  `permission_expires` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `entity_type_entity_id_permission_type` (`entity_type`,`entity_id`,`permission_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `pipelines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  `published` tinyint(4) DEFAULT NULL,
  `version_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `norm_iri` varchar(1500) NOT NULL,
  `description` text DEFAULT NULL,
  `tasks` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `lastaction` datetime DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `acronym` varchar(10) DEFAULT NULL,
  `iri` varchar(255) DEFAULT NULL,
  `norm_iri` varchar(255) DEFAULT NULL,
  `contact` text DEFAULT NULL,
  `accesstoken` varchar(255) DEFAULT NULL,
  `role` varchar(50) NOT NULL,
  `databank_id` int(11) DEFAULT NULL,
  `pipeline_article_id` int(11) DEFAULT NULL,
  `pipeline_book_id` int(11) DEFAULT NULL,
  `settings` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `iri` (`norm_iri`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
