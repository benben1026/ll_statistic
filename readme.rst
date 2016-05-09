###################
What is KEEPER
###################

KEEPER is one of application on KEEP platform and is committed to provide
users a unifying page for visualizing statistical data and analytical report
from a variety of online course platforms.


*******************
Server Requirements
*******************

- Apache 2.2 or newer
- PHP version 5.5 or newer
- MySQL version 5.6 or newer

************
Installation
************

1.	Deploy the source code from this git
2.	Create database in MySQL and set database name, username and password.
3.	Create session table in the database using following SQL`::`
	`|`CREATE TABLE IF NOT EXISTS `ci_sessions` (
	`|`	`id` varchar(40) NOT NULL,
	`|`	`ip_address` varchar(45) NOT NULL,
	`|`	`timestamp` int(10) unsigned NOT NULL DEFAULT '0',
	`|`	`data` blob NOT NULL,
	`|`	KEY `ci_sessions_timestamp` (`timestamp`)
	`|`) ENGINE=InnoDB DEFAULT CHARSET=utf8;
4.	Configure database connection information
	The configuration file can be found in ./application/config/database.php

*************
Configuration
*************

In this application, it need to retrieve data from two servers one is Learning Locker and the other is the server providing course information. Both of the servers need KEYs while retrieving the data. So we need to configure the KEYs in KEEPER.

Configure Learning Locker domain & credential
Open ./application/models/Datamodel.php
$pipeline_url stores the domain name of Learning Locker.
$auth stores all the credential of each LRS. 
The KEY can be found in learning locker. Login in Learning Locker with admin account. Go to the LRS that you want to connect with. Click on Manage Clients on the right sidebar. You can find a pair of username and password. Concatenate username and password with colon, like [username]:[password], and encode it with base 64. Then you can get the KEY.

Configure Course API domain & credential
Open ./application/models/Courseinfomodel.php
You will find the domain and credential are stored in the $config.
