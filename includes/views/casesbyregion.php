<?php render('_header')?>
<?php
	$timeFrame = "";
	$dateMove = "";
	$arrayRegion = array('1'=>'5','2'=>'10','3'=>'20','viewall'=>'viewall');
	$regionQuery= "";
	
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
	
	if(isset($_REQUEST['regions'])){
		if(!empty($_REQUEST['regions'])){ 
			if (array_key_exists($_REQUEST['regions'],$arrayRegion)){
				$regionQuery = $arrayRegion[$_REQUEST['regions']];
			}
			else{ $regionQuery= "5"; }
		}
		else{ $regionQuery= "5"; }
	}
	else{ $regionQuery= "5"; }
	

	
?>
<div class="body-wrapper">
		<div class="" style="width:87%; margin:0 auto;">
			<div class="main_content_temp">

<script type="text/javascript">

$(function () {

	//var isLoading = false,
//    $button = $('.select_time');
//    $button.change(function() {
//        if (!isLoading) { chart.showLoading(); } 
//		else { chart.hideLoading();  }
//        isLoading = !isLoading;
//    });
//	//chart initialization
//	Highcharts.setOptions({
//		lang: {	loading: 'Waiting for Data' }
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
		legend: {
			
			layout: 'vertical',
			align: 'right',
			verticalAlign: 'top',
			x: 0,
			y: 30,
			backgroundColor: '#FFFFFF',
			borderWidth: 1,
			maxHeight:450
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
				text: ''
			},
			min:0
			
		},
		tooltip: {
			 formatter: function() {
				  return '<b>'+ this.series.name +'</b><br/>Incoming Cases in '+ Highcharts.dateFormat('%b %d,%Y', this.x) +'='+ this.y +'';
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
							$region = $value->region;
							$ticket_count2 = $value->ticket_count2;
							
							$newOptions[$region][$created_at] = $ticket_count2;
						}
					}
					
					$countJobs = count($newOptions);
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
		print_r($result2);
	echo"</pre>";*/
	
?>

<div class="align_center">
	<div class="manualmerges_title">Incoming Cases by Region</div>

</div>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="form_select" id="form_submit" >
	
	<div class="align_right">
		<input type="hidden" value="casesbyregion" name="page" />
		
		<span>Regions</span>
		<select name="regions" onchange="submit();" class="select_time">
			<option value="1" <?php if($regionQuery == "5"){ echo "selected";} else{ echo''; } ?>>Top 5 Regions</option>
			<option value="2" <?php if($regionQuery == "10"){ echo "selected";} else{ echo''; } ?>>Top 10 Regions</option>
			<option value="3" <?php if($regionQuery == "20"){ echo "selected";} else{ echo''; } ?>>Top 20 Regions</option>
			<option value="viewall" <?php if($regionQuery == "viewall"){ echo "selected";} else{ echo''; } ?>>View All</option>
		</select>
		&nbsp;&nbsp;&nbsp;&nbsp;
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
		&nbsp;&nbsp;&nbsp;&nbsp;
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
			<option value="10" <?php if($timeFrame == '10'){ echo'selected'; }else{ echo''; }  ?>>6 Months</option>
			<option value="11" <?php if($timeFrame == '11'){ echo'selected'; }else{ echo''; }  ?>>1 Year</option>
			<option value="12" <?php if($timeFrame == '12'){ echo'selected'; }else{ echo''; }  ?>>2 Years</option>
		</select>

		
	</div>
</form>




<?php if($countListings == 0){ ?>
	<div style="min-width: 400px; height: 250px; padding-top:150px; margin: 10px auto 0; text-align:center; font-size:24px; border:1px solid #CCCCCC">No Available Data</div>
<?php 
}
else{
?>
	<div id="container" style="min-width: 400px; height: 550px; margin: 0 auto"></div>
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
<?php
if($regionQuery =="viewall"){
?>
SELECT region_days.region,
       region_days.created_at,
       
        /* The complex divide statement is so that moving averages work at the end of the data set
           We either want to divide by the number of days for the moving average, for example 7 for a 7 day moving average.
           or we want to divide by the number of days between now and the end of the data set       
        */
       (
          SELECT COUNT(*)/IF(DATEDIFF(SYSDATE(),region_days.created_at) >= <?php echo $dateMove; ?>, <?php echo $dateMove; ?>, DATEDIFF(SYSDATE(),region_days.created_at))
            FROM janak.assistly_cases ca, janak.assistly_customers cu, rentjuice.offices o, rentjuice.regions r
           WHERE ca.customer_id = cu.id
             AND cu.custom_office_id = o.id
             AND o.region_id = r.id             
             AND r.name = region_days.region
             AND region_days.created_at <= DATE(created_at) AND DATE(created_at) <= DATE_ADD(region_days.created_at, INTERVAL <?php echo ($dateMove - 1); ?> DAY)          
       ) ticket_count2
  FROM
       (
        SELECT *
          FROM
               (          
                SELECT DISTINCT(r.name) region
                  FROM janak.assistly_cases ca, janak.assistly_customers cu, rentjuice.offices o, rentjuice.regions r
                 WHERE ca.customer_id = cu.id
                   AND cu.custom_office_id = o.id
                   AND o.region_id = r.id
                   AND DATE_ADD(created_at, INTERVAL <?php echo $tagTime; ?> DAY) > SYSDATE()
               ) regions,
               (SELECT DATE_SUB(DATE(SYSDATE()),INTERVAL id DAY) created_at
                  FROM janak.counter_records
                 WHERE id <= <?php echo $tagTime; ?>) days
       ) region_days
 ORDER BY region_days.created_at, region_days.region
<?php
}
else{
?>
SELECT region_days.region,
       region_days.created_at,
       
        /* The complex divide statement is so that moving averages work at the end of the data set
           We either want to divide by the number of days for the moving average, for example 7 for a 7 day moving average.
           or we want to divide by the number of days between now and the end of the data set       
        */
       (
          SELECT COUNT(*)/IF(DATEDIFF(SYSDATE(),region_days.created_at) >=  <?php echo $dateMove; ?>,  <?php echo $dateMove; ?>, DATEDIFF(SYSDATE(),region_days.created_at))
            FROM janak.assistly_cases ca, janak.assistly_customers cu, rentjuice.offices o, rentjuice.regions r
           WHERE ca.customer_id = cu.id
             AND cu.custom_office_id = o.id
             AND o.region_id = r.id             
             AND r.name = region_days.region
             AND region_days.created_at <= DATE(created_at) AND DATE(created_at) <= DATE_ADD(region_days.created_at, INTERVAL <?php echo ($dateMove - 1); ?> DAY)          
       ) ticket_count2
  FROM
       (
        SELECT *
          FROM
               (          
                SELECT r.name region, COUNT(*) ticket_count
                  FROM janak.assistly_cases ca, janak.assistly_customers cu, rentjuice.offices o, rentjuice.regions r
                 WHERE ca.customer_id = cu.id
                   AND cu.custom_office_id = o.id
                   AND o.region_id = r.id
                   AND DATE_ADD(created_at, INTERVAL  <?php echo $tagTime; ?> DAY) > SYSDATE()
                 GROUP BY 1
                 ORDER BY 2 DESC
                 LIMIT <?php echo $regionQuery; ?> 
                ) regions,
               (SELECT DATE_SUB(DATE(SYSDATE()),INTERVAL id DAY) created_at
                  FROM janak.counter_records
                 WHERE id <=  <?php echo $tagTime; ?>) days
       ) region_days
 ORDER BY region_days.created_at, region_days.region

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
	$start = array('start_time'=>$start_time,'start_width'=>'wide');
	render('_footer',$start)
?>

