<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
    	<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Welcome to KEEPER</title>
		<link rel="stylesheet" type="text/css" href="<?= base_url() ?>public/bower_components/bootstrap/dist/css/bootstrap.css">
	    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>public/bower_components/components-font-awesome/css/font-awesome.css">
		<link rel="stylesheet" type="text/css" href="https://keep.edu.hk/keeplogo/keeplogo.css">
	    <link rel="stylesheet" type="text/css" href="https://keep.edu.hk/keepmenu/keepmenu.css">
	    <link rel="stylesheet" type="text/css" href="https://keep.edu.hk/keepfooter/keepfooter.css">
	    <script type="text/javascript" src="<?= base_url() ?>public/bower_components/jquery/dist/jquery.js"></script>
	    <script type="text/javascript" src="<?= base_url() ?>public/bower_components/handlebars/handlebars.js"></script>
	    <script type="text/javascript" src="<?= base_url() ?>public/bower_components/devbridge-autocomplete/dist/jquery.autocomplete.js"></script>
		<script type="text/javascript" src="https://keep.edu.hk/keepmenu/keepmenu.js"></script>
		<script type="text/javascript" src="https://keep.edu.hk/keepfooter/keepfooter.js"></script>

		<!-- Load SB Dashboard -->
		<!-- Bootstrap Core CSS -->
	    <!-- <link href="<?= base_url() ?>public/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"> -->
	    <!-- MetisMenu CSS -->
	    <link href="<?= base_url() ?>public/bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
	    <!-- Morris Charts CSS -->
	    <link href="<?= base_url() ?>public/bower_components/morrisjs/morris.css" rel="stylesheet">
	    <!-- Custom Fonts -->
	    <link href="<?= base_url() ?>public/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	    <!-- Timeline CSS -->
	    <link href="<?= base_url() ?>public/sb-dist/css/timeline.css" rel="stylesheet">
	    <!-- Custom CSS -->
	    <link href="<?= base_url() ?>public/sb-dist/css/sb-admin-2.css" rel="stylesheet">

	    <!-- Bootstrap Core JavaScript -->
		<script src="<?= base_url() ?>public/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
	    <!-- Metis Menu Plugin JavaScript -->
	    <script src="<?= base_url() ?>public/bower_components/metisMenu/dist/metisMenu.min.js"></script>
	    <!-- Morris Charts JavaScript -->
	    <script src="<?= base_url() ?>public/bower_components/raphael/raphael-min.js"></script>
	    <script src="<?= base_url() ?>public/bower_components/morrisjs/morris.min.js"></script>
	   <!--  <script src="<?= base_url() ?>public/js/morris-data.js"></script> -->
	    <!-- Custom Theme JavaScript -->
	    <script src="<?= base_url() ?>public/sb-dist/js/sb-admin-2.js"></script>
	</head>
	<body style="background-color: #ccc; min-height:100%">
		<div id="wrapper">
		<!-- Navigation -->
	        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
	        	<nav id="keepmenu"></nav>
	        <script type="text/javascript">
				$(document).ready(function(){
			    	//The samlUserData come from the IDP’s attributes
			    	var loginURL = "/index.php/saml2Controller/login";
			    	var logoutURL = "/index.php/saml2Controller/logout";
			   	 	keepmenu('KEEP', null, '', loginURL, logoutURL);
				});
			</script>