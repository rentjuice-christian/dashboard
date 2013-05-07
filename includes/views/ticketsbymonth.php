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
			type:'line',
			backgroundColor: '#F7F5F0'
        },
		loading: {
            labelStyle: {color: 'white',top: '45%'},
            style: {backgroundColor: 'gray'}
        },
		title: {
                text: ' '
        },
        xAxis: {
			title: {
				text: ''
			},
			type: 'datetime',
		  	labels: {
						formatter: function() {
							return Highcharts.dateFormat('%b', this.value);
						}
					}	
        },
		yAxis: {
			title: {
				text: ''
			},
			min:0
			
		},
		tooltip: {
			 formatter: function() {
				  return 'Tickets for the month of <b>'+ Highcharts.dateFormat('%B', this.x) +' '+ this.series.name +'</b> = <b>'+ this.y+'</b>';
			 }
		  },

        series: [
			<?php
			
					$newOptions = array();
					
					$getValue = $barcontent;
					
					$countData = count($getValue);
					if($getValue != 0){
						foreach($getValue as $value){
							$year = $value->year;
							$month = $value->month;
							$count = $value->count;		
							$newOptions[$year][$month] = $count;
						}
					}
					
					$countOptions = count($newOptions);
					$i = 0;
					foreach($newOptions as $keyYear => $valueYear){
						$i = $i + 1;
						echo"{";
							$countYear = count($valueYear);
							echo"name:'".$keyYear."',";
							echo"data: [";
							$a = 0;
								foreach($valueYear as $key => $value){
									$a = $a + 1;
									 echo "[Date.UTC(1970,".($key - 1).", 01),".$value."]";
									 // echo "[".$key.",".$value."]";
									 if($a != $countYear){ echo",";}			
								}
								$a++;
							echo"]";
						echo"}";
						
						if($i != $countOptions){ echo",";}
						
					}
					
					$i++;
			
			?>
		]
		
    }); // end new Highcharts
});// end function


</script>

<div class="align_center">
	<div class="manualmerges_title"> Tickets by Month </div>
</div>

<div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
<div class="align_right show_query">
	<a class='inline' href="#inline_content"><img src="assets/images/show_query.png" alt="show query" /></a>
</div>
<div style="display:none">
<div id='inline_content' style='padding:10px; background:#fff;'>
<pre>
SELECT YEAR(created_at), MONTH(created_at), MONTHNAME(created_at), COUNT(*)
  FROM janak.assistly_cases
 GROUP BY 1,2
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

