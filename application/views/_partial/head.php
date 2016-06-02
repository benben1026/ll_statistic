<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
    	<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>KEEPDashboard | <?php echo $title; ?></title>
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
	    <link href="<?= base_url() ?>public/bower_components/morrisjs/morris.css" rel="stylesheet">
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
	    <script src="<?= base_url() ?>public/bower_components/morrisjs/morris.min.js"></script>
	   <!--  <script src="<?= base_url() ?>public/js/morris-data.js"></script> -->
	    <!-- Custom Theme JavaScript -->
	    <script src="<?= base_url() ?>public/sb-dist/js/sb-admin-2.js"></script>
	    <!-- End Load SB Dashboard -->

	    <!-- jQuery UI -->
	    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>public/js/jquery-ui-1.11.4/jquery-ui.min.css">
	    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>public/js/jquery-ui-1.11.4/jquery-ui.theme.min.css">
	    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>public/js/jquery-ui-1.11.4/jquery-ui.structure.min.css">
	    <script type="text/javascript" src="<?= base_url() ?>public/js/jquery-ui-1.11.4/jquery-ui.min.js"></script>
		
		<!-- Custom CSS -->
		<link href="//fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700,900,200italic,300italic,400italic,600italic,700italic,900italic" rel="stylesheet" type="text/css">
		<link rel="stylesheet" type="text/css" href="<?= css_url() ?>keeper.css">

	</head>
	<body style="background-color: #ffffff; min-height:100%">