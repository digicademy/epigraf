CREATE TABLE `testtable` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `deleted` int(11) NOT NULL DEFAULT 0,
                            `published` int(11) DEFAULT NULL,
                            `created` timestamp NULL DEFAULT NULL,
                            `modified` timestamp NOT NULL DEFAULT current_timestamp(),
                            `modified_by` int(11) DEFAULT NULL,
                            `created_by` int(11) DEFAULT NULL,
                            `name` varchar(1500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            `shortname` varchar(1500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            `book_number` int(11) DEFAULT NULL,
                            `book_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            PRIMARY KEY (`id`),
                            KEY `state` (`deleted`),
                            KEY `modified_by` (`modified_by`),
                            KEY `created_by` (`created_by`),
                            KEY `published` (`published`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
