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