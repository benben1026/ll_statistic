<?php 
    $this->load->view('_partial/head');
    $this->load->view('_partial/navbar_side_menu');
?>
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><?php echo $title; ?></h3>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <?php echo $content; ?>
    </div>

<?php $this->load->view('_partial/footer'); ?>