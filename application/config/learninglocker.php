<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// $config['learninglocker'] = array(
// 	'domain' => 'http://learnlock.benjamin-zhou.com/public/api/v1/statements/aggregate',
// 	'auth' => array(
// 		'AWS Moodle 2.8.7' => 'ZGM4MTk1ZGUzZmM4NTQ5NzU2N2MzNDdlMzk4YmJiMWU2MjhlNTQxNjo1MjE2NTEzYzY0ZGY1ZjBlMDhiMmI3NTQwNDAxZGFiOGQzN2M5ODFk', 
// 		'KEEP Open edX' => 'MTdkMjA5NWYzNDM0OTA0YTkzYzgwMWM0Y2NiMGVmZWRhMTVjZDk1MTphNDY3MDMwMDRiYjI5YzA4NDRkNDVhY2NlY2JjYjg3NmJmZGUwMTJk'
// 	)
// );

$config['learninglocker'] = array(
	'domain' => 'http://10.11.2.7/api/v1/statements/aggregate',
	//'domain' => 'http://10.11.2.7/data/xAPI/statements?limit=',
	'auth' => array(
		'KEEP Moodle Staging' => 'MGU3NmEyZGQxZjc4NmI5NzEzMjVkMmQ4YWUzY2FmMTI1NjczZDgyNTpjMTUxZDg2NWNlNGY1NjFiNWIyZDFlNTI5MmM3OTgyZDhiMDc3ZGVj', 
		'KEEP Open edX Staging' => 'ZmNlOWY0MWMwMWJmOTllOTg4YjFkNGZlYzhiZjlkNjYyZmFjODIzOTo1MzU5NzUxNzdlNTlhYWEzZmQ0MGIyNWFmMzI0YWZhNjAxMzdiNzcy'
	)
);