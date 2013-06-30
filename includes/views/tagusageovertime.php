<?php render('_header',array('title'=>'Tag Usage Over Time'))?>
<?php

	$tagUsageLabel = "";
	$timeFrame = "";
	
	if(isset($_REQUEST['tagusage_label'])){
		
		if($_REQUEST['tagusage_label'] == "alltags"){ $tagUsageLabel ="All Tags";}
		else{ $tagUsageLabel = urldecode($_REQUEST['tagusage_label']); }
		
	}
	else{ $tagUsageLabel = 'Data Entry'; }
	
	if(isset($_REQUEST['tagusage_time'])){
	
		if(!empty($_REQUEST['tagusage_time'])){
			$timeFrame = $_REQUEST['tagusage_time']; 
		}
		else{$timeFrame = '4';}
		
	}
	else{ $timeFrame = '4'; }
	
	if(isset($_REQUEST['moving_average'])){
			
		if (!empty($_REQUEST['moving_average'])){
			if( $_REQUEST['moving_average'] < 30 ){
				$dateMove = $_REQUEST['moving_average'];
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
			type : 'datetime',
			labels: {
				formatter: function() {
					return Highcharts.dateFormat('%b %d, %Y', this.value);
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
			<?php if($timeFrame == '12'){ ?> , tickInterval:90 * 24 * 3600 * 1000 <?php } ?>   

        },
		yAxis: {
			title: {
				text: ''
			},
			min:0,
			allowDecimals: false
			
		},
		tooltip: {
			 formatter: function() {
				  return '<b>'+ this.series.name +'</b><br/>'+
				  Highcharts.dateFormat('%B %d,%Y', this.x) +' = '+ this.y;
			 }
		  },

        series: [{
			name:'<?php echo $tagUsageLabel; ?>',
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
									
									echo "[Date.UTC(".$reindex."),".round($value->ticket_count,1)."]";
									
									//$newOptions["test"][$reindex ] = $value->ticket_count;

									if($i != $countData){ echo",";}
									
								}
								$i++;
							}
							
							//$result = array_map('array_sum', $newOptions); // total the sum of the reult query
							
					?>
				  ]
		}]
    }); // end new Highcharts
});// end function


</script>


<div class="align_center">
	<div class="manualmerges_title">Tag Usage Over Time</div>
</div>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="form_select" id="form_submit" >
	<div class="align_right">
		<input type="hidden" value="tagusageovertime" name="page" />
		
		<span>Moving Average</span>
		<select name="moving_average" onchange="submit();" class="select_time">
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
		&nbsp;&nbsp;&nbsp;&nbsp;
		<span>Select Tags: </span>
		<select name="tagusage_label" onchange="submit();" class="select_time">
			
			<?php
				if(count($sub_barcontent) != 0){
					foreach($sub_barcontent as $valuesub){
				?>
						<option value="<?php echo $valuesub->label_name; ?>" <?php if($tagUsageLabel == $valuesub->label_name){ echo'selected'; }else{ echo''; }  ?>>
							<?php echo $valuesub->label_name." (".$valuesub->count_tag.")" ?>
						</option>
				<?php
					}
				}
				else{
				?>
					<option value="Data Entry">No Available Data</option>
				<?php
				}
			?>
			<option value="alltags" <?php if($tagUsageLabel =="All Tags"){ echo"selected";} ?>>All Tags</option>
		</select>
		&nbsp;&nbsp;&nbsp;
		<span>Select Time Frame: </span>
		<select name="tagusage_time" onchange="submit();" class="select_time">
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



<?php
	if(count($barcontent) == 0){
?>
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
<?php
	$arrayDate = array('1'=>'7','2'=>'14','3'=>'21','4'=>'28','5'=>'30','6'=>'60','7'=>'90','8'=>'120','9'=>'150','10'=>'180','11'=>'360','12'=>'720');
	if(isset($_REQUEST['tagusage_time'])){
		if(!empty($_REQUEST['tagusage_time'])){ 
			if (array_key_exists($_REQUEST['tagusage_time'],$arrayDate)){
				$tagTime = $arrayDate[$_REQUEST['tagusage_time']];
			}
			else{ $tagTime= "28"; }
		}
		else{ $tagTime= "28"; }
	}
	else{ $tagTime= "28"; }
?>
<pre>
<?php
if($tagUsageLabel !="All Tags"){
?>
SELECT days.created_at,
       (
        SELECT COUNT(*)/IF(DATEDIFF(SYSDATE(),days.created_at) >= <?php echo $dateMove; ?>, <?php echo $dateMove; ?>, DATEDIFF(SYSDATE(),days.created_at))
          FROM janak.assistly_case_labels cl, janak.assistly_cases c
         WHERE days.created_at <= DATE(c.created_at) AND DATE(c.created_at) <= DATE_ADD(days.created_at, INTERVAL <?php echo ($dateMove - 1); ?> DAY)        
           AND cl.case_id = c.id
           AND label_name = '<?php echo  $tagUsageLabel; ?>'
       ) ticket_count
  FROM
       (SELECT DATE_SUB(DATE(SYSDATE()),INTERVAL id DAY) created_at
          FROM janak.counter_records
         WHERE id <= <?php echo $tagTime; ?>) days
 ORDER BY days.created_at
<?php 
}
else{
?>
SELECT days.created_at,
       (
        SELECT COUNT(*)/IF(DATEDIFF(SYSDATE(),days.created_at) >= <?php echo $dateMove; ?>, <?php echo $dateMove; ?>, DATEDIFF(SYSDATE(),days.created_at))
          FROM janak.assistly_cases c
         WHERE days.created_at <= DATE(c.created_at) AND DATE(c.created_at) <= DATE_ADD(days.created_at, INTERVAL <?php echo ($dateMove - 1); ?> DAY)        
       ) ticket_count
  FROM
       (SELECT DATE_SUB(DATE(SYSDATE()),INTERVAL id DAY) created_at
          FROM janak.counter_records
         WHERE id <= <?php echo $tagTime; ?>) days
 ORDER BY days.created_at
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

