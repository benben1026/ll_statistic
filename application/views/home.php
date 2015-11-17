

<input id="username" type="text" placeholder="Username"/>
<button id="username-btn" onclick="clickSubmit()">Submit</button>
<h1>Your activites</h1>
<div class="chart-container">
	<div id="flot-placeholder"></div>
	<p id="choices" style="float:right; width:185px;"></p>
</div>

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
<script type="text/javascript" src="<?= base_url() ?>/public/js/jquery.js"></script>
<script type="text/javascript" src="<?= base_url() ?>/public/js/jquery.js"></script>

<!-- Bootstrap Core JavaScript -->
<script type="text/javascript" src="<?= base_url() ?>/public/js/bootstrap.min.js"></script>

<!-- Flot Charts JavaScript -->
<!--[if lte IE 8]><script src="js/excanvas.min.js"></script><![endif]-->
<script type="text/javascript" src="<?= base_url() ?>/public/js/plugins/flot/jquery.flot.js"></script>
<script type="text/javascript" src="<?= base_url() ?>/public/js/plugins/flot/jquery.flot.symbol.js"></script>
<script type="text/javascript" src="<?= base_url() ?>/public/js/plugins/flot/jquery.flot.time.js"></script>
<script type="text/javascript" src="<?= base_url() ?>/public/js/plugins/flot/jquery.flot.axislabels.js"></script>

<script type="text/javascript" src="<?= base_url() ?>/public/js/home.js"></script>