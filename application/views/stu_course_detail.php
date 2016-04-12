<input type="hidden" id="courseId" value="<?php echo $_GET['courseId']; ?>" />
<input type="hidden" id="platform" value="<?php echo $_GET['platform']; ?>" />
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">KEEPER Course Detail - <?php echo $course_name; ?></h1>
        </div>
    </div>
<?php
	if($_GET['platform'] == 'moodle'){
		include_once "charts/stu_course_detail/asg_dis.php";
	}
	include_once "charts/stu_course_detail/engagement.php";
?>

</div>