DROP TABLE IF EXISTS `versions`;
CREATE TABLE `versions`
(
    `id`          int(11) NOT NULL AUTO_INCREMENT,
    `deleted`     tinyint(4) NOT NULL DEFAULT 0,
    `version_id`  int(11) DEFAULT NULL,
    `created`     datetime DEFAULT NULL,
    `modified`    datetime DEFAULT NULL,
    `type`        varchar(50) NOT NULL,
    `content`     text DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `versions` (`id`, `deleted`, `version_id`, `created`, `modified`, `type`, `content`)
VALUES
(14, 0, NULL, '2022-02-02 10:10:10', '2022-02-02 12:12:12', 'record', 'errare humanum est ...'),
(24, 0, NULL, '2022-02-02 10:10:10', '2022-02-02 12:12:12', 'record', 'repetitio est mater lectionis ...');
