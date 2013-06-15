<?php render('_header',array('title'=>$title))?>

<?php
	if(isset($_REQUEST['timespan'])){
		if(!empty($_REQUEST['timespan'])){ 
			$timeFrame = $_REQUEST['timespan'];
		}
		else{$timeFrame = '4';}
	}
	else{$timeFrame = '4';}
	
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
							return Highcharts.dateFormat('%b %Y', this.value);
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
				text: ' '
			},
			min:0
			
		},
		tooltip: {
			 formatter: function() {
				  var s = '<b>'+ this.series.name +'</b><br/>'+ Highcharts.dateFormat('%B %d,%Y', this.x) +'='+ addCommas(this.y) +'';
				  return s;
			 }
		  },

      series: [
	  	<?php
				$newOptions = array();
		
					$getValue = $barcontent;
					
					$countData = count($getValue);
					if($countData != 0){
						foreach($getValue as $value){
							$month = $value->created_on;
							$customerType = $value->customer_type;
							$ticketCount = $value->ticket_count;
							
							$newOptions[$customerType][$month] = $ticketCount;

						}
					}
					
					$countListings = count($newOptions);
					$i = 0;
					foreach($newOptions as $keyListings => $valueListings){
						$i = $i + 1;
						echo"{";
							$countListings2 = count($valueListings);
							echo "name:'".$keyListings."',";
							if($keyListings == "free landlord"){ echo "color: '#fd5959',"; }
							if($keyListings == "free agency"){ echo "color: '#a00202',"; }
							if($keyListings == "paid landlord"){ echo "color: '#5c7efe',"; }
							if($keyListings == "paid agency"){ echo "color: '#022195',"; }
							
							echo "data: [";
							$a = 0;
								foreach($valueListings as $keyDate => $valueListings ){
									$a = $a + 1;
									
									$arrayValues = explode("-",$keyDate);
									$getmonth = $arrayValues[1] - 1;
									$reindex = $arrayValues[0].",".$getmonth.",".$arrayValues[2];
									
									echo "[Date.UTC(".$reindex."),".$valueListings."]"; 
									
									if($a != $countListings2){ echo",";}			
								}
								$a++;
							echo"]";
						echo"}";
						
						if($i != $countListings){ echo",";}
						
					}
					
					$i++;
			?>
		]
		
    }); // end new Highcharts
});// end function


</script>


<?php		
	/*echo"<pre>";
		print_r($barcontent);
	echo"</pre>";*/
?>

<div class="align_center">
	<div class="manualmerges_title"> <?php echo $title; ?> </div>
</div>

<div class="align_right">
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="form_select" id="form_submit" >
		<input type="hidden" value="ticketscustomertype" name="page" />
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
	</form>
</div>

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

if(isset($_REQUEST['timespan'])){
	if(!empty($_REQUEST['timespan'])){ 
		if (array_key_exists($_REQUEST['timespan'],$arrayDate)){
			$timeSpan = $arrayDate[$_REQUEST['timespan']];
		}
		else{ $timeSpan= "28"; }
	}
	else{ $timeSpan= "28"; }
}
else{ $timeSpan= "28"; }
?>
<pre>
SELECT day_customer_type.created_on,
       day_customer_type.customer_type,
       (
        SELECT /*'feed sync' AS "source", */
               COUNT(*)/IF(DATEDIFF(SYSDATE(),day_customer_type.created_on) >= 7, 7, DATEDIFF(SYSDATE(),day_customer_type.created_on)) ticket_count
          FROM janak.assistly_cases ca
         WHERE ca.rentjuice_customer_type = day_customer_type.customer_type
           AND day_customer_type.created_on <= DATE(ca.created_at) AND DATE(ca.created_at) <= DATE_ADD(day_customer_type.created_on, INTERVAL 6 DAY) 
       ) ticket_count
  FROM  
       (SELECT *
          FROM
               (SELECT DATE_SUB(DATE(SYSDATE()),INTERVAL id DAY) created_on
                  FROM janak.counter_records
                 WHERE id <= <?php echo $timeSpan; ?>) days,
               ( SELECT DISTINCT(rentjuice_customer_type) customer_type
                   FROM janak.assistly_cases
                  WHERE INSTR(rentjuice_customer_type,'free') <> 0
                     OR INSTR(rentjuice_customer_type,'paid') <> 0) customer_type
       ) day_customer_type                   
 ORDER BY day_customer_type.created_on 
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

