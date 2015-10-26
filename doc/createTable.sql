DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`(
	`id` BIGINT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(100) NOT NULL,
	`email` VARCHAR(100) NOT NULL,
	`password` VARCHAR(40) NOT NULL,
	PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `unit`;
CREATE TABLE `unit`(
	`id` BIGINT NOT NULL AUTO_INCREMENT,
	`pattern` VARCHAR(100) NOT NULL,
	`name` VARCHAR(100),
	PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `role`;
CREATE TABLE `role`(
	`id` BIGINT NOT NULL AUTO_INCREMENT,
	`userId` BIGINT NOT NULL,
	`unitId` BIGINT NOT NULL,
	`resourceId` BIGINT NOT NULL,
	PRIMARY KEY (`id`),
	FOREIGN KEY (`userId`) REFERENCES `user`(`id`) ON DELETE CASCADE,
	FOREIGN KEY (`unitId`) REFERENCES `unit`(`id`) ON DELETE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `ci_sessions` (
        `id` varchar(40) NOT NULL,
        `ip_address` varchar(45) NOT NULL,
        `timestamp` int(10) unsigned DEFAULT 0 NOT NULL,
        `data` blob NOT NULL,
        KEY `ci_sessions_timestamp` (`timestamp`)
);