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
		<input type="hidden" id="firstname" value="<?php echo isset($firstname) ? $firstname : ""; ?>" />


	<div id="wrapper" style="background-color: #ebebeb">
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
        	<div class="navbar-default sidebar" role="navigation" style="background-color: #ebebeb;margin-top: 0px !important">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <!--li class="sidebar-search">
                            <div class="input-group custom-search-form">
                                <input type="text" class="form-control" placeholder="Search...">
                                <span class="input-group-btn">
	                                <button class="btn btn-default" type="button">
	                                    <i class="fa fa-search"></i>
	                                </button>
                            	</span>
                            </div>
                            </input-group>
                        </li-->
                       <!--  <li id="sidebar-teacher-view" style="display:none">
                        	<a href="#"><i class="fa fa-dashboard fa-fw"></i>Teacher View<span class="fa arrow"></span></a>
                            <ul id="moodle-course-list" class="nav nav-second-level">
                            </ul>
                        </li>
                        <li id="sidebar-student-view" style="display:none">
                        	<a href="#"><i class="fa fa-dashboard fa-fw"></i>Student View<span class="fa arrow"></span></a>
                            <ul id="moodle-course-list" class="nav nav-second-level">
                            </ul>
                        </li> -->

                        <!-- <li>
                            <a href="overview"><i class="fa fa-dashboard fa-fw"></i> Overview</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-dashboard fa-fw"></i>KEEP Moodle Courses<span class="fa arrow"></span></a>
                            <ul id="moodle-course-list" class="nav nav-second-level">
                            </ul>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-dashboard fa-fw"></i>KEEP Open edX Courses<span class="fa arrow"></span></a>
                            <ul id="edx-course-list" class="nav nav-second-level">
                            </ul>
                        </li> -->

                        <li id="sidebar-teacher-view" style="display:none">
                        	<a href="#"><i class="fa fa-dashboard fa-fw"></i>Teacher View<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                            	<li><a href="overviewTea">Overview</a></li>
                            </ul>
                        </li>

                        <li id="sidebar-student-view" style="display:none">
                            <a href="#"><i class="fa fa-dashboard fa-fw"></i>Student View<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                            	<li><a href="overviewStu">Overview</a></li>
                            </ul>
                        </li>
                        
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <script type="text/javascript">        
			function processCourseList(data){
				var numOfTeach = 0;
				var numOfStudy = 0;
				var courseList = {'teach': [], 'study': []};
				for(var lrs in data){
					for(var i = 0; i < data[lrs]['total_results']; i++){
						if(data[lrs]['results'][i]['role_name'] == 'student'){
							numOfStudy ++;
							courseList['study'].push({'course_id': data[lrs]['results'][i]['course_id'], 'course_name': data[lrs]['results'][i]['course_name'], 'platform': lrs});
						}else{
							numOfTeach ++;
							courseList['teach'].push({'course_id': data[lrs]['results'][i]['course_id'], 'course_name': data[lrs]['results'][i]['course_name'], 'platform': lrs});
						}
					}
				}
				//console.log(JSON.stringify(courseList));
				if(numOfTeach != 0){
					for(var i = 0; i < numOfTeach; i++){
						$('#sidebar-teacher-view ul').append('<li><a href="courseDetail?courseId=' + courseList['teach'][i]['course_id'] + '&platform=' + courseList['teach'][i]['platform'] + '">' + courseList['teach'][i]['course_name'] + ' (' + courseList['teach'][i]['platform'] + ')</a></li>');
					}
					$('#sidebar-teacher-view').show();
				}
				if(numOfStudy != 0){
					for(var i = 0; i < numOfStudy; i++){
						$('#sidebar-student-view ul').append('<li><a href="courseDetail?courseId=' + courseList['study'][i]['course_id'] + '&platform=' + courseList['study'][i]['platform'] + '">' + courseList['study'][i]['course_name'] + ' (' + courseList['study'][i]['platform'] + ')</a></li>');
					}
					$('#sidebar-student-view').show();
				}
			}

        	$.ajax({
				url: '../course/courseList',
				type: 'GET',
				dataType: 'json',
				success: function(data){
					if(!data['ok']){
						console.log('fail to get course infomation');
						return;
					}

					// $('#moodle-course-num').html(data['data']['moodle']['total_results']);
					// $('#edx-course-num').html(data['data']['edx']['total_results']);
					// for(var i = 0; i < data['data']['moodle']['total_results']; i++){
					// 	$('#moodle-course-list').append($('<li><a href="../courseDetail?courseId=' + data['data']['moodle']['results'][i]['course_id'] + '">' + data['data']['moodle']['results'][i]['course_name'] + '</a></li>'))
					// }
					// for(var i = 0; i < data['data']['edx']['total_results']; i++){
					// 	$('#edx-course-list').append($('<li><a href="../courseDetail?courseId=' + data['data']['edx']['results'][i]['course_id'] + '">' + data['data']['edx']['results'][i]['course_name'] + '</a></li>'))
					// }
					processCourseList(data['data']);
				},
				error: function(){

				}
			});

        </script>


