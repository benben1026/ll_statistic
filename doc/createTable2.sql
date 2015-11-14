DROP TABLE IF EXISTS `role_has_priv_read`;
DROP TABLE IF EXISTS `role_has_priv_write`;
DROP TABLE IF EXISTS `role_has_priv_admin`;
DROP TABLE IF EXISTS `role`;
DROP TABLE IF EXISTS `unit`;
DROP TABLE IF EXISTS `user`;
DROP TABLE IF EXISTS `resource`;


CREATE TABLE `user`(
	`id` BIGINT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(100) NOT NULL,
	`email` VARCHAR(100) NOT NULL,
	`password` VARCHAR(40) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE (`email`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `unit`(
	`id` BIGINT NOT NULL AUTO_INCREMENT,
	`pattern` VARCHAR(100) NOT NULL,
	`level` INT NOT NULL,
	`name` VARCHAR(100),
	PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;


/*For the priv in this table, 0 represent false, 1 represent true, 2 represent inherit*/
CREATE TABLE `role`(
	`id` BIGINT NOT NULL AUTO_INCREMENT,
	`user_id` BIGINT NOT NULL,
	`unit_id` BIGINT NOT NULL,
	`active` BOOLEAN NOT NULL,
	`read_priv` TINYINT(1) NOT NULL,
	`write_priv` TINYINT(1) NOT NULL,
	`admin_priv` TINYINT(1) NOT NULL,
	PRIMARY KEY (`id`),
	FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE,
	FOREIGN KEY (`unit_id`) REFERENCES `unit`(`id`) ON DELETE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `resource`(
	`id` BIGINT NOT NULL AUTO_INCREMENT,
	`resource_type` VARCHAR(300) NOT NULL,
	PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `role_has_priv_read`(
	`role_id` BIGINT NOT NULL,
	`course_id` BIGINT NOT NULL,
	`resource_id` BIGINT NOT NULL,
	FOREIGN KEY (`role_id`) REFERENCES `role`(`id`) ON DELETE CASCADE,
	FOREIGN KEY (`resource_id`) REFERENCES `resource`(`id`) ON DELETE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `role_has_priv_write`(
	`role_id` BIGINT NOT NULL,
	`course_id` BIGINT NOT NULL,
	`resource_id` BIGINT NOT NULL,
	FOREIGN KEY (`role_id`) REFERENCES `role`(`id`) ON DELETE CASCADE,
	FOREIGN KEY (`resource_id`) REFERENCES `resource`(`id`) ON DELETE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `role_has_priv_admin`(
	`role_id` BIGINT NOT NULL,
	`course_id` BIGINT NOT NULL,
	`resource_id` BIGINT NOT NULL,
	FOREIGN KEY (`role_id`) REFERENCES `role`(`id`) ON DELETE CASCADE,
	FOREIGN KEY (`resource_id`) REFERENCES `resource`(`id`) ON DELETE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ci_sessions` (
	`id` varchar(40) NOT NULL,
	`ip_address` varchar(45) NOT NULL,
	`timestamp` int(10) unsigned DEFAULT 0 NOT NULL,
	`data` blob NOT NULL,
	KEY `ci_sessions_timestamp` (`timestamp`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;