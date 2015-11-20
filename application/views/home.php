

<!-- <input id="username" type="text" placeholder="Username"/>
<button id="username-btn" onclick="clickSubmit()">Submit</button> -->

<input type="text" id="dateYFrom" value="2015" placeholder="From:Year">
<input type="text" id="dateMFrom" value="01" placeholder="From:Month">
<input type="text" id="dateDFrom" value="01" placeholder="From:Day">
<br/ >
<input type="text" id="dateYTo" value="2016" placeholder="To:Year">
<input type="text" id="dateMTo" value="01" placeholder="To:Month">
<input type="text" id="dateDTo" value="01" placeholder="To:Day">
<br/ >
<button onclick="init()">Submit</button>
<!-- <button id="lecmat-btn" onclick="clickSubmit('LecMat')">Lecture Material Stat</button>
<button id="assessment-btn" onclick="clickSubmit('Assessment')">Assesssment Stat</button>
<button id="login-btn" onclick="clickSubmit('Login')">Login Stat</button>
<button id="forum-btn" onclick="clickSubmit('Forum')">Forum Stat</button> -->
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
<!-- Flot Charts JavaScript -->
<!--[if lte IE 8]><script src="js/excanvas.min.js"></script><![endif]-->
<script type="text/javascript" src="<?= base_url() ?>/public/js/plugins/flot/jquery.flot.js"></script>
<script type="text/javascript" src="<?= base_url() ?>/public/js/plugins/flot/jquery.flot.symbol.js"></script>
<script type="text/javascript" src="<?= base_url() ?>/public/js/plugins/flot/jquery.flot.time.js"></script>
<script type="text/javascript" src="<?= base_url() ?>/public/js/plugins/flot/jquery.flot.axislabels.js"></script>

<script type="text/javascript" src="<?= base_url() ?>/public/js/home.js"></script>