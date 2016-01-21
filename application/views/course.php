<div class="container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<h1>Your activites</h1>
			<div class="chart-container">
				<div id="flot-placeholder"></div>
				<p id="choices" style="float:right; width:185px;"></p>
			</div>
			<br />
		</div>
	</div>
	<div class="row" style="margin-bottom: 50px; margin-top:20px;">
		<div class="col-md-3 col-md-offset-1">
			<div class="form-group">
				<label for="courseId">Course ID:</label>
				<input type="number" class="form-control" id="courseId" value="2" />
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<label for="datepicker_from">From:</label>
				<input type="text" class="form-control" id="datepicker_from" value="2015/09/01" />
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<label for="datepicker_to">To:</label>
				<input type="text" class="form-control" id="datepicker_to" value="2015/10/31"/>
			</div>
		</div>
		<div class="col-md-2" style="padding-top:25px;">
			<button class="btn btn-default" onclick="init()">Update graph</button>
		</div>
	</div>
</div>


<script>
	$( "#datepicker_from" ).datepicker({
    	dateFormat: "yy/mm/dd",
    	defaultDate: +1
    });
    $( "#datepicker_to" ).datepicker({
    	dateFormat: "yy/mm/dd",
    	defaultDate: new Date()
    });
    $( "#datepicker_from" ).datepicker("setDate", -30);
    $( "#datepicker_to" ).datepicker("setDate", new Date());
  //$(function() {
    
    //$( "#datepicker_from" ).datepicker( "option", "dateFormat", "yy/mm/dd" );
    //$( "#datepicker_from" ).datepicker( "option", "defaultDate", -30 );
    //$( "#datepicker_to" ).datepicker( "option", "dateFormat", "yy/mm/dd" );
    //$( "#datepicker_to" ).datepicker( "option", "defaultDate", new Date() );
  //});
</script>

<style>
#flot-placeholder{
	float:left;
    width:600px;
    height:350px;
}
.chart-container{
	width: 850px;
	position: relative;
}
</style>
<!-- Flot Charts JavaScript -->
<!--[if lte IE 8]><script src="js/excanvas.min.js"></script><![endif]-->
<script type="text/javascript" src="<?= base_url() ?>/public/js/plugins/flot/jquery.flot.js"></script>
<script type="text/javascript" src="<?= base_url() ?>/public/js/plugins/flot/jquery.flot.symbol.js"></script>
<script type="text/javascript" src="<?= base_url() ?>/public/js/plugins/flot/jquery.flot.time.js"></script>
<script type="text/javascript" src="<?= base_url() ?>/public/js/plugins/flot/jquery.flot.axislabels.js"></script>

<script type="text/javascript" src="<?= base_url() ?>/public/js/course.js"></script>