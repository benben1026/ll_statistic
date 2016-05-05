<input type="hidden" id="courseId" value="<?php echo $_GET['courseId']; ?>" />
<input type="hidden" id="platform" value="<?php echo $_GET['platform']; ?>" />
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">KEEPER Course Detail - <?php echo $course_name; ?></h1>
        </div>
    </div>
<?php
	include_once "charts/tea_course_detail/courseBasic.php";
	include_once "charts/tea_course_detail/courseStudentVitality.php";
	include_once "charts/tea_course_detail/courseEngagement.php";
	include_once "charts/tea_course_detail/courseQuiz.php";
	include_once "charts/tea_course_detail/fake_asg_dis.php";
	include_once "charts/tea_course_detail/courseFileView.php";
	include_once "charts/stu_course_detail/forum.php";
	include_once "charts/tea_course_detail/courseEnrollDrop.php";

?>
</div>