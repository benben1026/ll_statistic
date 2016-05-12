<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$active_group = 'default';

$config['stu_engage_classify'][$active_group] = array(
	'View Courseware' => array(
		array('viewed', 'a courseware page'),
		array('viewed', 'a URL'),
		array('viewed', 'a courseware asset'),
	),
	'Assignment' => array(
		array('viewed', 'an assignmnet'),
		array('submitted', 'an assignmnet'),
	),
	'Assessment' => array(
		array('submitted', 'a peer assessment'),
		array('attempted', 'a peer assessment training'),
	),
	'Quiz' => array(
		array('viewed', 'a quiz'),
		array('started', 'a quiz'),
		array('attempted', 'a problem'),
		array('completed', 'a problem'),
		array('reviewed', 'a quiz'),
		array('deleted', 'a quiz'),
	),
	'Video' => array(
		array('loaded', 'a video'),
		array('started playing', 'a video'),
		array('paused playing', 'a video'),
		array('stopped playing', 'a video'),
		array('seeked', 'a video'),
		array('showed', 'a video transcript'),
		array('hid', 'a video transcript'),
	),
	'Note' => array(
		array('created', 'a note'),
		array('viewed', 'a note'),
		array('edited', 'a note'),
		array('searched', 'a note'),
		array('deleted', 'a note'),
	),
	'Create Post' => array(
		array('created', 'a discussion thread'),
	),
	'View Post' => array(
		array('viewed', 'a discussion thread'),
	),
	'Respond to Post' => array(
		array('responded to', 'a discussion thread'),
		array('responded to', 'a discussion response'),
	),
	'Vote Post' => array(
		array('up voted', 'a discussion thread'),
		array('down voted', 'a discussion thread'),
		array('up voted', 'a discussion response'),
		array('down voted', 'a discussion response'),
	),
	'Other interaction with Forum' => array(
		array('updated', 'a discussion thread'),
		array('updated', 'a discussion response'),
		array('deleted', 'a discussion response'),
		array('deleted', 'a discussion thread'),
		array('followed', 'a discussion thread'),
		array('stopped following', 'a discussion thread'),
		array('flagged', 'an inappropriate discussion thread'),
		array('unflagged', 'a discussion thread'),
		array('flagged as inappropriate', 'an inappropriate discussion response'),
		array('unflagged', 'a discussion responsed'),
	),
);