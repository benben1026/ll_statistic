# Group is reserved word in mysql, i changed to category
DROP TABLE dash_engage;
DROP TABLE dash_statement;


CREATE TABLE dash_statement
(
`statement_id` int NOT NULL AUTO_INCREMENT,
`name` varchar(100) NOT NULL,
`category` varchar(100) NOT NULL,
`updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`statement_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE dash_engage
(
`engagement_id` int NOT NULL AUTO_INCREMENT,
`course_id` varchar(200) NOT NULL,
`platform` varchar(50) NOT NULL,
`statement_id` int NOT NULL,
`statement_count` int NOT NULL,
`statistic_date` DATE NOT NULL,
`updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`engagement_id`),
FOREIGN KEY (`statement_id`) REFERENCES `dash_statement`(`statement_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
