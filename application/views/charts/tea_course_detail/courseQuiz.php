<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-green">
            <div class="panel-heading">
                <i class="fa fa-bar-chart-o fa-fw"></i> Quiz
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body" style="height: 400px;">
            	<img id="courseQuiz_loading" src="<?= base_url() ?>public/resource/loadbar.gif" style="width: 50%; height: 10%; margin-left: 25%; margin-top: 10%">
                <div id="courseQuiz" style="height: 380px; width:100%; display: none"></div>
            </div>
            <!-- /.panel-body -->
        </div>
	</div>
</div>

<script type="text/javascript">
    function draw_quiz(){
        $('#courseQuiz_loading').hide();
        $('#courseQuiz').show();
        new Morris.Line({
          // ID of the element in which to draw the chart.
          element: 'courseQuiz',
          // Chart data records -- each entry in this array corresponds to a point on
          // the chart.
          data: [
            {date: '2016-2-28', Attempt: 1, Complete: 0},
            {date: '2016-3-05', Attempt: 5, Complete: 0},
            {date: '2016-3-10', Attempt: 7, Complete: 1},
            {date: '2016-3-11', Attempt: 21, Complete: 30},
            {date: '2016-3-12', Attempt: 30, Complete: 54},
            {date: '2016-3-14', Attempt: 1, Complete: 0},
            {date: '2016-3-01', Attempt: 2, Complete: 0},
            {date: '2016-4-10', Attempt: 20, Complete: 17},
            {date: '2016-4-12', Attempt: 15, Complete: 10}
          ],
          // The name of the data record attribute that contains x-values.
          xkey: 'date',
          // A list of names of data record attributes that contain y-values.
          ykeys: ['Attempt', 'Complete'],
          // Labels for the ykeys -- will be displayed when you hover over the
          // chart.
          labels: ['Attempt', 'Complete'],
        });
    }
    setTimeout(draw_quiz, 2000);
</script>