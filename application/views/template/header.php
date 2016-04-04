<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
    	<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title><?php echo $title; ?></title>
		<link rel="stylesheet" type="text/css" href="<?= base_url() ?>public/bower_components/bootstrap/dist/css/bootstrap.css">
	    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>public/bower_components/components-font-awesome/css/font-awesome.css">
	    <link rel="stylesheet" type="text/css" href="https://keep.edu.hk/keeplogo/keeplogo.css">
	    <link rel="stylesheet" type="text/css" href="https://keep.edu.hk/keepmenu/keepmenu.css">
	    <link rel="stylesheet" type="text/css" href="https://keep.edu.hk/keepfooter/keepfooter.css">
	    <script type="text/javascript" src="<?= base_url() ?>public/bower_components/jquery/dist/jquery.js"></script>
	    <script type="text/javascript" src="<?= base_url() ?>public/bower_components/handlebars/handlebars.js"></script>
	    <script type="text/javascript" src="<?= base_url() ?>public/bower_components/devbridge-autocomplete/dist/jquery.autocomplete.js"></script>


		<script type="text/javascript" src="<?= base_url() ?>/public/js/echarts.min.js"></script>
		<script type="text/javascript" src="https://keep.edu.hk/keepmenu/keepmenu.js"></script>
		<script type="text/javascript" src="https://keep.edu.hk/keepfooter/keepfooter.js"></script>

		<!-- Bootstrap Core JavaScript -->
	<!-- 	<script type="text/javascript" src="<?= base_url() ?>/public/js/bootstrap.min.js"></script> -->

		<!-- Load DataTable -->
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/t/dt/dt-1.10.11/datatables.min.css"/>
		<script type="text/javascript" src="https://cdn.datatables.net/t/dt/dt-1.10.11/datatables.min.js"></script>

		<!-- Load SB Dashboard -->
		<!-- Bootstrap Core CSS -->
	    <!-- <link href="<?= base_url() ?>public/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"> -->
	    <!-- MetisMenu CSS -->
	    <link href="<?= base_url() ?>public/bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
	    <!-- Morris Charts CSS -->
	   <!--  <link href="<?= base_url() ?>public/bower_components/morrisjs/morris.css" rel="stylesheet"> -->
	    <!-- Custom Fonts -->
	    <link href="<?= base_url() ?>public/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	    <!-- Timeline CSS -->
	    <link href="<?= base_url() ?>public/sb-dist/css/timeline.css" rel="stylesheet">
	    <!-- Custom CSS -->
	    <link href="<?= base_url() ?>public/sb-dist/css/sb-admin-2.css" rel="stylesheet">

	    <!-- jQuery -->
	    <!-- <script src="../bower_components/jquery/dist/jquery.min.js"></script> -->
	    <!-- Bootstrap Core JavaScript -->
		<script src="<?= base_url() ?>public/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
	    <!-- Metis Menu Plugin JavaScript -->
	    <script src="<?= base_url() ?>public/bower_components/metisMenu/dist/metisMenu.min.js"></script>
	    <!-- Morris Charts JavaScript -->
	    <script src="<?= base_url() ?>public/bower_components/raphael/raphael-min.js"></script>
	   <!--  <script src="<?= base_url() ?>public/bower_components/morrisjs/morris.min.js"></script> -->
	   <!--  <script src="<?= base_url() ?>public/js/morris-data.js"></script> -->
	    <!-- Custom Theme JavaScript -->
	    <script src="<?= base_url() ?>public/sb-dist/js/sb-admin-2.js"></script>
	    <!-- End Load SB Dashboard -->

	</head>
	<body style="background-color: #ccc; min-height:100%">
		<input type="hidden" id="firstname" value="<?php echo isset($firstname) ? $firstname : ""; ?>" />


	<div id="wrapper">
	<!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
        	<nav id="keepmenu"></nav>
        <script type="text/javascript">
			$(document).ready(function(){
		    	//The samlUserData come from the IDP’s attributes
		    	var name = $('#firstname').val();
		    	var loginURL = "/index.php/saml2Controller/login";
		    	var logoutURL = "/index.php/saml2Controller/logout";
		   	 
		    	// -- KEEP Menu --
		    	if(name == ''){
					keepmenu('KEEP', null, '', loginURL, logoutURL);
		        }else{
		        	keepmenu('KEEP', null, name, loginURL, logoutURL);
		        }
		    	//keepmenu('KEEP', null, name, loginURL, logoutURL);
			});
		</script>
        	<div class="navbar-default sidebar" role="navigation" style="margin-top: 0px !important">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li class="sidebar-search">
                            <div class="input-group custom-search-form">
                                <input type="text" class="form-control" placeholder="Search...">
                                <span class="input-group-btn">
	                                <button class="btn btn-default" type="button">
	                                    <i class="fa fa-search"></i>
	                                </button>
                            	</span>
                            </div>
                            <!-- /input-group -->
                        </li>
                        <li>
                            <a href="overview"><i class="fa fa-dashboard fa-fw"></i> Overview</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-dashboard fa-fw"></i>KEEP Moodle Courses<span class="fa arrow"></span></a>
                            <ul id="moodle-course-list" class="nav nav-second-level">
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-dashboard fa-fw"></i>KEEP Open edX Courses<span class="fa arrow"></span></a>
                            <ul id="edx-course-list" class="nav nav-second-level">
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>


