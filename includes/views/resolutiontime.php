<?php render('_header',array('title'=>'Average time to Resolve a Ticket'))?>
<?php

	$timeFrame = "";
	$dateMove = "";
	
	if(isset($_REQUEST['timespan'])){
	
		if(!empty($_REQUEST['timespan'])){
			$timeFrame = $_REQUEST['timespan']; 
		}
		else{$timeFrame = '5';}
		
	}
	else{ $timeFrame = '5'; }
	
	if(isset($_REQUEST['movingaverage'])){
			
		if (!empty($_REQUEST['movingaverage'])){
			if( $_REQUEST['movingaverage'] < 30 ){
				$dateMove = $_REQUEST['movingaverage'];
			}
			else{ $dateMove = "7"; }
		}
		else{ $dateMove = "7"; }
		
	}
	else{ $dateMove = "7"; }
	
	if(isset($_REQUEST['groupby'])){
	
		if(!empty($_REQUEST['groupby'])){
			if($_REQUEST['groupby'] == "datecreated"){$groupBy = "created_at"; }
			else if($_REQUEST['groupby'] == "dateresolved"){$groupBy = "resolved_at";}
			else{$groupBy = 'created_at';}
		}
		else{$groupBy = 'created_at';}
		
	}
	else{ $groupBy = 'created_at'; }

?>
<div class="body-wrapper">
		<div class="centered">
			<div class="main_content_temp">

<script type="text/javascript">

$(function () {
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
			<?php if($timeFrame == '1'){ ?> , tickInterval:24 * 3600 * 1000 <?php } ?>
			<?php if($timeFrame == '2'){ ?> , tickInterval:2 * 24 * 3600 * 1000 <?php } ?>
			<?php if($timeFrame == '3'){ ?> , tickInterval:3 * 24 * 3600 * 1000 <?php } ?> 
			<?php if($timeFrame == '4'){ ?> , tickInterval:5 * 24 * 3600 * 1000 <?php } ?>
			<?php if($timeFrame == '5'){ ?> , tickInterval:5 * 24 * 3600 * 1000 <?php } ?> 
			<?php if($timeFrame == '6'){ ?> , tickInterval:5 * 24 * 3600 * 1000 <?php } ?> 
			<?php if($timeFrame == '7'){ ?> , tickInterval:20 * 24 * 3600 * 1000 <?php } ?>  
			<?php if($timeFrame == '8'){ ?> , tickInterval:30 * 24 * 3600 * 1000 <?php } ?>  
			<?php if($timeFrame == '9'){ ?> , tickInterval:30 * 24 * 3600 * 1000 <?php } ?>  
			<?php if($timeFrame == '10'){ ?> , tickInterval:30 * 24 * 3600 * 1000 <?php } ?> 
			<?php if($timeFrame == '11'){ ?> , tickInterval:90 * 24 * 3600 * 1000 <?php } ?>  
			<?php if($timeFrame == '12'){ ?> , tickInterval:90 * 24 * 3600 * 1000 <?php } ?>   

			
        },
		yAxis: {
			title: {
				text: 'Hours'
			},
			min:0
			
		},
		tooltip: {
			 formatter: function() {
				  var s = '<b>'+ this.series.name +'</b><br/>'+ Highcharts.dateFormat('%b %d,%Y', this.x) +'='+ this.y +'';
				  return s;
			 }
		  },

      series: [{
			name:'Resolution Time',
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
									
									echo "[Date.UTC(".$reindex."),".round($value->hours_to_resolve,1)."]";

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
 if(!empty($error_message)){
	render('error',array('error_message'=>$error_message));
 }
 else{
?>
<div class="align_center">
	<div class="manualmerges_title">Average time to Resolve a Ticket</div>
</div>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="form_select" id="form_submit" >
	<div class="align_right">
		<input type="hidden" value="resolutiontime" name="page" />
		
		<span>Group By</span>
		<select name="groupby" onchange="submit();" class="select_time">
			<option value="dateresolved" <?php if($groupBy == 'resolved_at'){ echo'selected'; }else{ echo''; }  ?>>Date Resolved</option>
			<option value="datecreated" <?php if($groupBy == 'created_at'){ echo'selected'; }else{ echo''; }  ?>>Date Created</option>
		</select>
		&nbsp;&nbsp;&nbsp;
		<span>Moving Average</span>
		<select name="movingaverage" onchange="submit();" class="select_time">
			<?php
				for($i=1; $i <= 28; $i++){
					if($dateMove == $i){ $selected = "selected"; }
					else{ $selected = "";  }
					echo "<option value=\"".$i."\" ".$selected.">".$i;
					 if($i == 1){ echo" Day";} else{ echo" Days"; }
					echo"</option>";
				}
			?>
		</select>
		&nbsp;&nbsp;&nbsp;
		<span>Select Time Frame: </span>
		<select name="timespan" onchange="submit();" class="select_time">
			<option value="1" <?php if($timeFrame == '1'){ echo'selected'; }else{ echo''; }  ?>>1 Week</option>
			<option value="2" <?php if($timeFrame == '2'){ echo'selected'; }else{ echo''; }  ?>>2 Weeks</option>
			<option value="3" <?php if($timeFrame == '3'){ echo'selected'; }else{ echo''; }  ?>>3 Weeks</option>
			<option value="4" <?php if($timeFrame == '4'){ echo'selected'; }else{ echo''; }  ?>>4 Weeks</option>
			<option value="5" <?php if($timeFrame == '5'){ echo'selected'; }else{ echo''; }  ?>>1 Month</option>
			<option value="6" <?php if($timeFrame == '6'){ echo'selected'; }else{ echo''; }  ?>>2 Months</option>
			<option value="7" <?php if($timeFrame == '7'){ echo'selected'; }else{ echo''; }  ?>>3 Months</option>
			<option value="8" <?php if($timeFrame == '8'){ echo'selected'; }else{ echo''; }  ?>>4 Months</option>
			<option value="9" <?php if($timeFrame == '9'){ echo'selected'; }else{ echo''; }  ?>>5 Months</option>
			<option value="10" <?php if($timeFrame == '10'){ echo'selected'; }else{ echo''; }  ?>>6 Months</option>]
			<option value="11" <?php if($timeFrame == '11'){ echo'selected'; }else{ echo''; }  ?>>1 Year</option>
			<option value="12" <?php if($timeFrame == '12'){ echo'selected'; }else{ echo''; }  ?>>2 Years</option>
		</select>
	</div>
</form>

	<?php if(count($barcontent) == 0){ ?>
		<div class="content_noavail">No Available Data</div>
	<?php }else{ ?>
		<div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
	<?php } ?>

<?php } ?>

<div class="align_right show_query">
	<a class='inline' href="#inline_content"><img src="assets/images/show_query.png" alt="show query" /></a>
</div>
<div style="display:none">
<div id='inline_content' style='padding:10px; background:#fff;'>
<?php
	$arrayDate = array('1'=>'7','2'=>'14','3'=>'21','4'=>'28','5'=>'30','6'=>'60','7'=>'90','8'=>'120','9'=>'150','10'=>'180','11'=>'360','12'=>'720');
	
	if (array_key_exists($timeFrame,$arrayDate)){
		$tagTime = $arrayDate[$timeFrame];
	}
	else{ $tagTime= "30"; }
		
?>
<pre>
SELECT days.created_at,

       /* In order to get a moving average, we can't just take the average of resolution times for tickets
          in the last 7 days. This is because an individual ticket is weighted more heavily if it is 1 of 2 tickets
          on a paricular day, or 1 of 100. So we need to first calculate the daily values and then average those values
          to get the moving average value.
       */
       (
        SELECT AVG(daily_resolution_time) moving_average
          FROM (
                SELECT AVG(TIMESTAMPDIFF(MINUTE, created_at, resolved_at)/60) daily_resolution_time, <?php echo $groupBy;  ?>   
                  FROM janak.assistly_cases
                 GROUP BY DATE(<?php echo $groupBy;  ?>)
               ) daily_times
         WHERE days.created_at <= DATE(<?php echo $groupBy;  ?> ) AND DATE(<?php echo $groupBy;  ?> ) <= DATE_ADD(days.created_at, INTERVAL <?php echo $dateMove;  ?> DAY)                       
       ) hours_to_resolve
  FROM
       (SELECT DATE_SUB(DATE(SYSDATE()),INTERVAL id DAY) created_at
          FROM janak.counter_records
         WHERE id <= <?php echo $tagTime;  ?>) days
 ORDER BY days.created_at DESC
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

