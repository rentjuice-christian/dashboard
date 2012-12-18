<?php render('_header')?>
<?php

	$timeFrame = "";
	
	if(isset($_REQUEST['time_frame'])){
		$timeFrame = $_REQUEST['time_frame'];
	}
	else{
		$timeFrame = '14';
	}
		
?>

<div class="body-wrapper">
<div class="centered">
<div class="main_content_temp">

<script type="text/javascript">

$(function () {

	//var isLoading = false,
//    $button = $('.select_time');
//    $button.change(function() {
//        if (!isLoading) {
//            chart.showLoading();
//        } else {
//            chart.hideLoading();
//        }
//        isLoading = !isLoading;
//    });
//	Highcharts.setOptions({
//		lang: {
//			loading: 'Waiting for Data'
//		}
//	});
	
	// create the chart
    var chart = new Highcharts.Chart({
        chart: {
            renderTo: 'container',
			type:'line',
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
		plotOptions: {
            series: {
                marker: {
                    enabled: false,
					lineColor: null,
                    states: {
                        hover: {
                            enabled: true,
							fillColor: 'white',
							lineWidth: 2
                        }
                    }
                }
            }
        },
        xAxis: {
			title: {
				text: ''
			},
			type : 'datetime',
			labels: {
				formatter: function() {
					
					<?php 
						
						if($timeFrame == '7' || $timeFrame == '14' ){?> return Highcharts.dateFormat('%b %d ,%Y', this.value); <?php } 
						else{?> return Highcharts.dateFormat('%b %Y', this.value); <?php }
					?>
				}
			}
			
			//tickInterval: 7 * 24 * 3600 * 1000	
			<?php if($timeFrame == '7'){ ?> , tickInterval:24 * 3600 * 1000 <?php } ?>  // interval by one day
			<?php if($timeFrame == '14'){ ?> , tickInterval:3 * 24 * 3600 * 1000 <?php } ?>  // interval by one day
        },
		yAxis: {

			title: {
				text: ''
			},
			min:0
		},
		tooltip: {
			  formatter: function() {
			  		<?php
						if($timeFrame == '7days' || $timeFrame == '14days' ){
					?>
					return '<b>'+ this.series.name +'</b><br/>'+
				    Highcharts.dateFormat('%B %d - %A', this.x) +' = '+ this.y;
					<?php	
						}
						else{
					?>
					return '<b>'+ this.series.name +'</b><br/>'+
				    Highcharts.dateFormat('%B %d,%Y', this.x) +' = '+ this.y;
					<?php
						}
					?>
			 }
		  },

        series: [{
			name:'Daily Tickets',
            data: [
					<?php
					
							$getValue = $barcontent;
							$countData = count($getValue);
							
							if($countData != 0){
								$i = 0;
								foreach($getValue as $value){
									$i = $i + 1;
									$arrayValues = explode("-",$value->created_at);
									$getmonth = $arrayValues[1] - 1;
									$reindex = $arrayValues[0].",".$getmonth.",".$arrayValues[2];
									
									echo "[Date.UTC(".$reindex."),".$value->average_tickets."]";

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

<?php
	/*echo"<pre>";
	print_r($barcontent);
	echo"</pre>";*/
	
?>

<div class="align_center">
	<div class="manualmerges_title">Daily New Tickets</div>
</div>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="form_select" id="form_submit">
<div class="align_right">
	<input type="hidden" value="dailynewtickets" name="page" />
	<span>Time Frame: </span>
	<select name="time_frame" onchange="submit();" class="select_time">
		<option value="7" <?php if($timeFrame == '7'){ echo'selected'; }else{ echo''; }  ?>>Last 7 Days</option>
		<option value="14" <?php if($timeFrame == '14'){ echo'selected'; }else{ echo''; }  ?>>Last 14 Days</option>
		<option value="alltime" <?php if($timeFrame == 'alltime'){ echo'selected'; }else{ echo''; }  ?> >All Time</option>
	</select>
</div>
</form>
<?php
	if(count($barcontent) == 0){
?>
	<br />
	<div class="content_noavail">No Available Data</div>
<?php
	}
	else{
?>
	<div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
<?php	
	}
?>
<div class="align_right show_query">
	<a class='inline' href="#inline_content"><img src="assets/images/show_query.png" alt="show query" /></a>
</div>
<div style="display:none">
<div id='inline_content' style='padding:10px; background:#fff;'>
<pre>
<?php 
	if($timeFrame == '7'){
	?>
SELECT DATE(created_at) as created_at, COUNT(*) as average_tickets
FROM janak.assistly_cases
WHERE DATE_ADD(created_at, INTERVAL 7 DAY) >= SYSDATE()
GROUP BY 1
ORDER BY 1
	<?php
	}
	else if($timeFrame == '14'){
	?>
SELECT DATE(created_at) as created_at, COUNT(*) as average_tickets
FROM janak.assistly_cases
WHERE DATE_ADD(created_at, INTERVAL 14 DAY) >= SYSDATE()
GROUP BY 1
ORDER BY 1
	<?php
	}
	else{
	?>

SELECT all_data.created_at,
  (SELECT COUNT(*)/COUNT(DISTINCT(DATE(created_at)))
     FROM janak.assistly_cases
    WHERE all_data.created_at <= DATE(created_at) AND DATE(created_at) <= DATE_ADD(all_data.created_at, INTERVAL 7 DAY)) average_tickets
FROM
(SELECT DATE(created_at) created_at, COUNT(*)
FROM janak.assistly_cases
GROUP BY 1
ORDER BY 1 ) all_data

	<?php
	}
?>
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

