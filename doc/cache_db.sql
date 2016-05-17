# Group is reserved word in mysql, i changed to category
CREATE TABLE dash_statement
(
id int NOT NULL,
name varchar(100) NOT NULL,
category varchar(100) NOT NULL,
PRIMARY KEY (id)
);

CREATE TABLE dash_engage
(
course_id int NOT NULL,
platform varchar(50) NOT NULL,
statement_id int NOT NULL,
count int NOT NULL,
timestamp timestamp NOT NULL,
FOREIGN KEY (statement_id) REFERENCES dash_statement(id)
);
