<?php render('_header',array('title'=>$title))?>
<?php

	$timeFrame = "";
	$dateMove = "";
	
	if(isset($_REQUEST['timespan'])){
	
		if(!empty($_REQUEST['timespan'])){
			$timeFrame = $_REQUEST['timespan']; 
		}
		else{$timeFrame = '4';}
		
	}
	else{ $timeFrame = '4'; }
	
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
			type:'area',
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
			area: {
				stacking: 'normal',
				lineColor: '#666666',
				lineWidth: 1,
				marker: {
					lineWidth: 1,
					lineColor: '#666666',
					enabled: false,
					states: {
						hover: {
							enabled: true,
							fillColor: 'white',
							lineWidth: 2
						}
					}
				}
			} // and area
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
			<?php if($timeFrame == '11'){ ?> , tickInterval:60 * 24 * 3600 * 1000 <?php } ?>  
			<?php if($timeFrame == '12'){ ?> , tickInterval:60 * 24 * 3600 * 1000 <?php } ?>   

			
        },
		yAxis: {
			title: {
				text: ' '
			},
			min:0
			
		},
		tooltip: {
			 formatter: function() {
				  var s = '<b>'+ this.series.name +'</b><br/>'+ Highcharts.dateFormat('%b %d,%Y', this.x) +'='+ this.y +'';
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
							$created_at = $value->created_at;
							$office_type = $value->channel;
							$ticket_count2 = $value->interaction_count;
							
							$newOptions[$office_type][$created_at] = $ticket_count2;
						}
					}
					
					$countJobs = count($newOptions);
					$i = 0;
					foreach($newOptions as $keyOfficeType => $valueOfficeType){
						$i = $i + 1;
						echo"{";
							$countListings = count($valueOfficeType);
							echo"name:'".$keyOfficeType."',";
							echo"data: [";
							$a = 0;
								foreach($valueOfficeType as $keyDate => $valueListings ){
									$a = $a + 1;
									
									$arrayValues = explode("-",$keyDate);
									$getmonth = $arrayValues[1] - 1;
									$reindex = $arrayValues[0].",".$getmonth.",".$arrayValues[2];
									
									echo "[Date.UTC(".$reindex."),".round($valueListings,1)."]"; 
									
									if($a != $countListings){ echo",";}			
								}
								$a++;
							echo"]";
						echo"}";
						
						if($i != $countJobs){ echo",";}
						
					}
					
					$i++;
			?>
		]
		
    }); // end new Highcharts
});// end function


</script>

<?php		
	/*echo"<pre>";
		print_r($newOptions);
	echo"</pre>";	*/
?>
<div class="align_center">
	<div class="manualmerges_title"><?php echo $title; ?> </div>
</div>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="form_select" id="form_submit" >
	<div class="align_right">
		<input type="hidden" value="interbychannels" name="page" />
		
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
	else{ $tagTime= "28"; }
		
?>
<pre>
SELECT
   channel_days.created_at,
   channel_days.channel,
   (
    SELECT COUNT(*)/IF(DATEDIFF(SYSDATE(),channel_days.created_at) >= <?php echo $dateMove; ?>, <?php echo $dateMove; ?>, DATEDIFF(SYSDATE(),channel_days.created_at))
      FROM assistly_interactions i
     WHERE channel_days.created_at <= DATE(i.created_at) AND DATE(i.created_at) <= DATE_ADD(channel_days.created_at, INTERVAL <?php echo ( $dateMove - 1 ); ?> DAY)        
       AND i.channel = channel_days.channel
    ) interaction_count
FROM
   (
    SELECT *
      FROM
           (SELECT channel
                FROM janak.assistly_interactions
               WHERE DATE_ADD(created_at, INTERVAL <?php echo $tagTime; ?> DAY) > SYSDATE()
               GROUP BY 1
           ) channels,
             (SELECT DATE_SUB(DATE(SYSDATE()),INTERVAL id DAY) created_at
                  FROM janak.counter_records
                 WHERE id <= <?php echo $tagTime; ?>) days
   ) channel_days
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

