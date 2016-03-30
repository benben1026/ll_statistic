<div id="engagement"></div>
<script type="text/javascript">

	function getData(){
		$.ajax({
			url: 'https://stat.benjamin-zhou.com/index.php/learninglocker/test',
			type: 'GET',
			dataType: 'json',
			success: function(data){
				for(var result in data){
					if(result['ok'] == 1){
						
					}
				}
			},
			error: function(){

			}
		});
	}

	function engagement() {
		Morris.Line({
			element: 'engagement',
			data: [
				{ y: '2015-01-31', a: 100, b: 90 },
				{ y: '2015-02-20', a: 75,  b: 65 },
				{ y: '2015-03-30', a: 50,  b: 40 },
				{ y: '2015-04-10', a: 75,  b: 65 },
				{ y: '2015-05-04', a: 50,  b: 40 },
				{ y: '2015-06-12', a: 75,  b: 65 },
				{ y: '2015-07-22', a: 100, b: 90 }
			],
			xkey: 'y',
			ykeys: ['a', 'b'],
			labels: ['Series A', 'Series B']
		});
	}

	$(document).ready(engagement);
</script>