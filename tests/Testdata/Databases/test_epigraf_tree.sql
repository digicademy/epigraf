DROP TABLE IF EXISTS `tree`;
CREATE TABLE `tree`
(
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `parent_id` int(11) DEFAULT NULL,
    `deleted` int(11) NOT NULL DEFAULT 0,
    `version_id` int(11) DEFAULT NULL,
    `created` timestamp NULL DEFAULT '0000-00-00 00:00:00',
    `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `type` varchar(500) DEFAULT NULL COLLATE utf8mb4_unicode_ci,
    `content` text DEFAULT NULL COLLATE utf8mb4_unicode_ci,
    `level` int(11) DEFAULT NULL,
    `lft` int(11) UNSIGNED DEFAULT NULL,
    `rght` int(11) UNSIGNED DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tree` (`id`, `parent_id`, `deleted`, `version_id`, `created`, `modified`, `type`, `content`, `level`, `lft`, `rght`)
VALUES
(1, NULL, 0, NULL, '2023-01-01 00:00:00', '2023-01-01 00:00:00', 'fonttypes', 'Root A', 0, 1, 6),
(2, 1, 0, NULL, '2023-01-02 00:00:00', '2023-01-02 00:00:00', 'fonttypes', 'Child A1', 1, 2, 3),
(3, 1, 0, NULL, '2023-01-03 00:00:00', '2023-01-03 00:00:00', 'fonttypes', 'Child A2', 1, 4, 5),
(4, NULL, 0, NULL, '2023-01-04 00:00:00', '2023-01-04 00:00:00', 'fonttypes', 'Root B', 0, 7, 10),
(5, 4, 0, NULL, '2023-01-05 00:00:00', '2023-01-05 00:00:00', 'fonttypes', 'Child B1', 1, 8, 9);
