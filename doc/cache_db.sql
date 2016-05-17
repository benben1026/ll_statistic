# Group is reserved word in mysql, i changed to category
CREATE TABLE dash_statement
(
`id` int NOT NULL AUTO_INCREMENT,
`name` varchar(100) NOT NULL,
`category` varchar(100) NOT NULL,
`status` INT( 11 ) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'expired or not',
PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE dash_engage
(
`course_id` int NOT NULL,
`platform` varchar(50) NOT NULL,
`statement_id` int NOT NULL,
`count` int NOT NULL,
`timestamp` timestamp NOT NULL,
PRIMARY KEY (`course_id`,`platform`),
FOREIGN KEY (`statement_id`) REFERENCES `dash_statement`(`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
