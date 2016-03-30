<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $title; ?></title>
		<link rel="stylesheet" type="text/css" href="<?= base_url() ?>public/bower_components/bootstrap/dist/css/bootstrap.css">
	    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>public/keepfooter/example/keeplogo/keeplogo.css">
	    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>public/keepfooter/dist/keepfooter.css">
	    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>public/bower_components/components-font-awesome/css/font-awesome.css">
	    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>public/keepmenu/dist/keepmenu.css">
	    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/public/css/jquery-ui.min.css">
	    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/public/css/jquery-ui.structure.min.css">
	    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/public/css/jquery-ui.theme.min.css">
	    <script type="text/javascript" src="<?= base_url() ?>public/bower_components/jquery/dist/jquery.js"></script>
	    <script type="text/javascript" src="<?= base_url() ?>public/bower_components/handlebars/handlebars.js"></script>
	    <!--<script type="text/javascript" src="<?= base_url() ?>public/keepfooter/dist/keepfooter.js"></script>-->
	    <script type="text/javascript" src="<?= base_url() ?>public/bower_components/devbridge-autocomplete/dist/jquery.autocomplete.js"></script>
	    <!--<script type="text/javascript" src="<?= base_url() ?>public/keepmenu/dist/keepmenu.js"></script>-->
		<!-- Bootstrap Core JavaScript -->
		<script type="text/javascript" src="<?= base_url() ?>/public/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="<?= base_url() ?>/public/js/jquery-ui.min.js"></script>
		<script type="text/javascript" src="<?= base_url() ?>/public/js/echarts.min.js"></script>
		<script type="text/javascript" src="https://keep.edu.hk/keepmenu/keepmenu.js"></script>
		<script type="text/javascript" src="https://keep.edu.hk/keepfooter/keepfooter.js"></script>
		<script type="text/javascript" src="<?= base_url() ?>/public/js/plugins/morris/morris.min.js"></script>
		<script type="text/javascript" src="<?= base_url() ?>/public/js/plugins/morris/raphael.min.js"></script>

		<!-- Load DataTable -->
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/t/dt/dt-1.10.11/datatables.min.css"/>
		<script type="text/javascript" src="https://cdn.datatables.net/t/dt/dt-1.10.11/datatables.min.js"></script>
	</head>
	<body style="background-color: #ccc; min-height:100%">
		<input type="hidden" id="firstname" value="<?php echo isset($firstname) ? $firstname : ""; ?>" />
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
	   <!--  <script>
	        var mission = { title: "Mission", url: "/mission" };
	        var partners = { title: "Partners", url: "/partners" };
	        var team = { title: "Team", url: "/team" };
	        var about = { title: "About", url: "about", submenu: [mission, partners, team] }; 
	        var news = { title: "News", url: "news" };
	        var services = { title: "Services", url: "services" };
	        var contact = { title: "Contact", url: "contact" };
	        var menu = [about, news, services, contact];

	        // When logined
	        //keepmenu('KEEP', menu, 'Jane', 'loginUrl', 'logoutUrl', null, 'q', 'collection2', 'en');
	        // When not logined
	        if($('#username').val() == ''){
				keepmenu('KEEP', menu, '', 'login', 'logout', 'searchUrl');
	        }else{
	        	keepmenu('KEEP', menu, $('#username').val(), 'login', 'logout', null, 'q', 'collection2', 'en');
	        }
	        
	    </script> -->

