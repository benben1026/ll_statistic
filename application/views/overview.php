<?php   
    $this->load->view("charts/courseOverview.php");
    $this->load->view("charts/overview-topViewForum.php");
    $this->load->view("charts/overview-topReplyForum.php");
    if ($role != 'student') {
    	$this->load->view("charts/overview-fileViewing.php");
    }
?>