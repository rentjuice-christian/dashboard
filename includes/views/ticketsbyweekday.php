<?php render('_header')?>

<div class="body-wrapper">
		<div class="centered">
			<div class="main_content_temp">

<script type="text/javascript">

$(function () {

	
	//chart initialization
	
	Highcharts.setOptions({
		lang: {
			loading: 'Waiting for Data'
		}
	});
	
	// create the chart
    var chart = new Highcharts.Chart({
        chart: {
            renderTo: 'container',
			type:'column',
			backgroundColor: '#F7F5F0'
        },
		loading: {
            labelStyle: {
                color: 'white',
				top: '45%'
            },
            style: {
                backgroundColor: 'gray'
            }
        },
		title: {
                text: ' '
        },
        xAxis: {
			title: {
				text: ''
			},
			categories: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday','Sunday']
        },
		yAxis: {

			title: {
				text: ''
			},
			min:0
		},
		tooltip: {
			 formatter: function() {
				 return  this.x +' = '+ this.y;
			 }
		  },

        series: [{
			name:'Cases Count',
			pointWidth: 20,
             data: [
					<?php
					
							$getValue = $barcontent;
							$countData = count($getValue);
							if($getValue != 0){
								$i = 0;
								foreach($getValue as $value){
									$i = $i + 1;
									//echo "['".$value->dayname."', ".$value->count."]";
									echo $value->count;
									if($i != $countData){ echo",";}
								}
								$i++;
							}
					?>
				  ]
		}]
		
    }); // end new Highcharts
});// end function


</script>

<div class="align_center">
	<div class="manualmerges_title"> Tickets by Weekday (All Time)</div>
	
</div>

<div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
<div class="align_right show_query">
	<a class='inline' href="#inline_content"><img src="assets/images/show_query.png" alt="show query" /></a>
</div>
<div style="display:none">
<div id='inline_content' style='padding:10px; background:#fff;'>
<pre>
SELECT WEEKDAY(created_at), DAYNAME(created_at), COUNT(*)
  FROM janak.assistly_cases
 GROUP BY 1
 ORDER BY 1
</pre>
</div>
</div>

			</div>
			
		</div>
</div>
<?php 
	$start = array('start_time'=>$start_time);
	render('_footer',$start)
?>

