<?php render('_header')?>
<?php

	$timeFrame = "";
	
	if(isset($_REQUEST['turnaround_time'])){ $timeFrame = $_REQUEST['turnaround_time']; }
	else{ $timeFrame = '2'; }
		
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
//	//chart initialization
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
            labelStyle: {color: 'white',top: '45%'},
            style: {backgroundColor: 'gray'}
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
			type: 'datetime',
		  	labels: {
						formatter: function() {
							return Highcharts.dateFormat('%b %d,%Y', this.value);
						}
			}
			<?php
				if(isset($_REQUEST['turnaround_time'])){
					if($_REQUEST['turnaround_time'] == 1){ echo",tickInterval:24 * 3600 * 1000	"; }
					if($_REQUEST['turnaround_time'] == 2){ echo",tickInterval:2 * 24 * 3600 * 1000	"; }
					if($_REQUEST['turnaround_time'] == 3){ echo",tickInterval:4 * 24 * 3600 * 1000	"; }
					if($_REQUEST['turnaround_time'] == 4){ echo",tickInterval:5 * 24 * 3600 * 1000	"; }
					if($_REQUEST['turnaround_time'] == 5){ echo",tickInterval:7 * 24 * 3600 * 1000	"; }
					if($_REQUEST['turnaround_time'] == 6){ echo",tickInterval:7 * 24 * 3600 * 1000	"; }
					if($_REQUEST['turnaround_time'] == 7){ echo",tickInterval:15 * 24 * 3600 * 1000	"; }
					if($_REQUEST['turnaround_time'] == 'alltime'){ echo",tickInterval:90 * 24 * 3600 * 1000	"; }
				}
				else{
					echo",tickInterval:2 * 24 * 3600 * 1000	";
				}
			?>
			
        },
		yAxis: {
			title: {
				text: 'Hours'
			},
			min:0
			
		},
		tooltip: {
			 formatter: function() {
				  var s = '<b>'+ this.series.name +'</b><br/>Turnaround Time on '+ Highcharts.dateFormat('%b %d,%Y', this.x) +'='+ Math.round(this.y) +'';
				  if(this.y > 2){
				  	s +=' hours';
				  }
				  else{
				  	s +=' hour';
				  }
				  return s;
			 }
		  },

        series: [
			<?php
			
					$newOptions = array();
					
					$getValue = $barcontent;
					
					$countData = count($getValue);
					if($getValue != 0){
						foreach($getValue as $value){
							$region = $value->region_name;
							$day_complete = $value->day_completed;
							$hour_turn_around = $value->hour_turn_around;	
							if(!empty($day_complete)){
								$newOptions[$region][$day_complete] = $hour_turn_around; // re array by region
							}
							
						}
					}
					
					$countRegion = count($newOptions);
					$i = 0;
					foreach($newOptions as $keyRegion => $valueRegion){
						$i = $i + 1;
						echo"{";
							$countListings = count($valueRegion);
							echo"name:'".$keyRegion."',";
							echo"data: [";
							$a = 0;
								foreach($valueRegion as $keyDate => $valueListings ){
									$a = $a + 1;
									
									$arrayValues = explode("-",$keyDate);
									$getmonth = $arrayValues[1] - 1;
									$reindex = $arrayValues[0].",".$getmonth.",".$arrayValues[2];
									
									echo "[Date.UTC(".$reindex."),".$valueListings."]"; 
									
									if($a != $countListings){ echo",";}			
								}
								$a++;
							echo"]";
						echo"}";
						
						if($i != $countRegion){ echo",";}
						
					}
					
					$i++;
			
			?>
		]
		
    }); // end new Highcharts
});// end function


</script>

<div class="align_center">
	<div class="manualmerges_title">Turnaround Time by Market</div>
	
</div>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="form_select" id="form_submit" >
	<div class="align_right">
		<input type="hidden" value="turnaroundtimebymarket" name="page" />
		<span>Select Time Frame: </span>
		<select name="turnaround_time" onchange="submit();" class="select_time">
			<option value="1" <?php if($timeFrame == '1'){ echo'selected'; }else{ echo''; }  ?>>1 Week</option>
			<option value="2" <?php if($timeFrame == '2'){ echo'selected'; }else{ echo''; }  ?>>2 Weeks</option>
			<option value="3" <?php if($timeFrame == '3'){ echo'selected'; }else{ echo''; }  ?>>3 Weeks</option>
			<option value="4" <?php if($timeFrame == '4'){ echo'selected'; }else{ echo''; }  ?>>4 Weeks</option>
			<option value="5" <?php if($timeFrame == '5'){ echo'selected'; }else{ echo''; }  ?>>2 Months</option>
			<option value="6" <?php if($timeFrame == '6'){ echo'selected'; }else{ echo''; }  ?>>3 Months</option>
			<option value="7" <?php if($timeFrame == '7'){ echo'selected'; }else{ echo''; }  ?>>4 Months</option>
			<option value="alltime" <?php if($timeFrame == 'alltime'){ echo'selected'; }else{ echo''; }  ?>>All Time</option>
		</select>
	</div>
</form>

<div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
<div class="align_right show_query">
	<a class='inline' href="#inline_content"><img src="assets/images/show_query.png" alt="show query" /></a>
</div>
<div style="display:none">
<div id='inline_content' style='padding:10px; background:#fff;'>
<?php
	$arrayDate = array('1'=>'7','2'=>'14','3'=>'21','4'=>'28','5'=>'30','6'=>'60','7'=>'90','alltime'=>'alltime');
	if(isset($_REQUEST['turnaround_time'])){
		if(!empty($_REQUEST['turnaround_time'])){ 
			if (array_key_exists($_REQUEST['turnaround_time'],$arrayDate)){
				$timeFrame2 = $arrayDate[$_REQUEST['turnaround_time']];
			}
			else{ $timeFrame2 = "14"; }
		}
		else{ $timeFrame2 = "14"; }
	}
	else{ $timeFrame2 = "14"; }
?>
<pre>
SELECT
    DATE(t.applied_on) AS day_completed ,
    r.name AS region_name ,
    COUNT(l.id) AS total_listings ,
    AVG(TIMESTAMPDIFF(HOUR , t.created_on , t.applied_on)) AS hour_turn_around
FROM rentjuice.offices o
INNER JOIN rentjuice.regions r ON o.region_id = r.id
INNER JOIN dataentry.threads t ON o.id = t.office_id
    AND t.completed = 1
    AND t.deleted = 0
    AND t.completed_reason IS NULL
    <?php if($timeFrame2 !="alltime"){ ?>AND DATE(t.applied_on) >= DATE_SUB(CURDATE() , INTERVAL <?php echo $timeFrame2; ?> DAY)<?php } ?> 
        INNER JOIN(SELECT MAX( id ) AS session_id , thread_id
        FROM dataentry.thread_sessions ts
        GROUP BY thread_id) AS max_sessions
        ON max_sessions.thread_id = t.id INNER JOIN dataentry.updates l
        ON l.session_id = max_sessions.session_id
WHERE o.dataentry = 1
GROUP BY o.region_id , MONTH(t.applied_on), DAY(t.applied_on)
ORDER BY
    MONTH( t.applied_on ) DESC ,
    DAY(t.applied_on) DESC , o.region_id ASC
</pre>
</div>
</div>

<?php
	//echo"<pre>";
		//print_r($barcontent);
		//print_r($newOptions);
	//echo"</pre>";
?>

			</div>
			
		</div>
</div>
<?php 
	$start = array('start_time'=>$start_time);
	render('_footer',$start)
?>

