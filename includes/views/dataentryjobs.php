<?php render('_header')?>
<?php

	$timeFrame = "";
	
	if(isset($_REQUEST['dataentryjobs_time'])){
		$timeFrame = $_REQUEST['dataentryjobs_time'];
	}
	else{
		$timeFrame = '1';
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
		
		//plotOptions: {
//            series: {
//                marker: {
//                    enabled: false,
//					lineColor: null,
//                    states: {
//                        hover: {
//                            enabled: true,
//							fillColor: 'white',
//							lineWidth: 2
//                        }
//                    }
//                }
//            }
//        },
		
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
			<?php
				if(isset($_REQUEST['dataentryjobs_time'])){
					if($_REQUEST['dataentryjobs_time'] == 1){
						echo",tickInterval:24 * 3600 * 1000	";
					}
					else if($_REQUEST['dataentryjobs_time'] == 2){
						echo",tickInterval:2 * 24 * 3600 * 1000	";
					}
					else if($_REQUEST['dataentryjobs_time'] == 3){
						echo",tickInterval:5 * 24 * 3600 * 1000	";
					}
					else if($_REQUEST['dataentryjobs_time'] == '4'){
						echo",tickInterval:7 * 24 * 3600 * 1000	";
					}
					else if($_REQUEST['dataentryjobs_time'] == '5'){
						echo",tickInterval:14 * 24 * 3600 * 1000	";
					}
					else if($_REQUEST['dataentryjobs_time'] == '6'){
						echo",tickInterval:14 * 24 * 3600 * 1000	";
					}
					else if($_REQUEST['dataentryjobs_time'] == '7'){
						echo",tickInterval:21 * 24 * 3600 * 1000	";
					}
					else if($_REQUEST['dataentryjobs_time'] == '8'){
						echo",tickInterval:60 * 24 * 3600 * 1000	";
					}
					else{
						echo",tickInterval:24 * 3600 * 1000	";
					}
				}
				else{
					echo",tickInterval:24 * 3600 * 1000	";
				}
			?>
			
        },
		yAxis: {
			title: {
				text: ' '
			},
			min:0
			
		},
		tooltip: {
			 formatter: function() {
				  var s = '<b>'+ this.series.name +'</b><br/>'+ Highcharts.dateFormat('%b %d,%Y', this.x) +'='+ Math.round(this.y) +'';
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
							$the_date = $value->the_date;
							$filters = $value->filters;
							$threads_preferred = $value->threads_preferred;
							$threads_auto = $value->threads_auto;
							$total = $value->total;
							
								$newOptions['Filters'][$the_date] = $filters;
								$newOptions['Threads Preferred'][$the_date] = $threads_preferred;
								$newOptions['Threads Auto'][$the_date] = $threads_auto;
								$newOptions['Other Threads'][$the_date] = $total - ($filters + $threads_preferred + $threads_auto);
						}
					}
					
					$countJobs = count($newOptions);
					$i = 0;
					foreach($newOptions as $keyJobs => $valueJobs){
						$i = $i + 1;
						echo"{";
							$countListings = count($valueJobs);
							echo"name:'".$keyJobs."',";
							echo"data: [";
							$a = 0;
								foreach($valueJobs as $keyDate => $valueListings ){
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
	echo"</pre>";*/
	
?>
<div class="align_center">
	<div class="manualmerges_title">Data Entry Jobs</div>
	
</div>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="form_select" id="form_submit" >
	<div class="align_right">
		<input type="hidden" value="dataentryjobs" name="page" />
		<span>Select Time Frame: </span>
		<select name="dataentryjobs_time" onchange="submit();" class="select_time">
			<option value="1" <?php if($timeFrame == '1'){ echo'selected'; }else{ echo''; }  ?>>1 Week</option>
			<option value="2" <?php if($timeFrame == '2'){ echo'selected'; }else{ echo''; }  ?>>2 Weeks</option>
			<option value="3" <?php if($timeFrame == '3'){ echo'selected'; }else{ echo''; }  ?>>3 Weeks</option>
			<option value="4" <?php if($timeFrame == '4'){ echo'selected'; }else{ echo''; }  ?>>1 Month</option>
			<option value="5" <?php if($timeFrame == '5'){ echo'selected'; }else{ echo''; }  ?>>2 Months</option>
			<option value="6" <?php if($timeFrame == '6'){ echo'selected'; }else{ echo''; }  ?>>3 Months</option>
			<option value="7" <?php if($timeFrame == '7'){ echo'selected'; }else{ echo''; }  ?>>4 Months</option>
			<option value="8" <?php if($timeFrame == '8'){ echo'selected'; }else{ echo''; }  ?>>All Time</option>
		</select>
	</div>
</form>
	
<div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
<div class="align_right show_query">
	<a class='inline' href="#inline_content"><img src="assets/images/show_query.png" alt="show query" /></a>
</div>
<div style="display:none">
<div id='inline_content' style='padding:10px; background:#fff;'>
<pre>
<?php

if(!empty($timeFrame)){
	$arrayDate = array('1'=>'1 WEEK','2'=>'2 WEEK','3'=>'3 WEEK','4'=>'1 MONTH','5'=>'2 MONTH','6'=>'3 MONTH','7'=>'4 MONTH','8'=>'alltime',);
	
	if (array_key_exists($timeFrame,$arrayDate)){
		if($timeFrame != "8"){
			$strQuery = "AND DATE_ADD(t.created_on, INTERVAL ".$arrayDate[$timeFrame].") > SYSDATE()";
			$strQuery2 = "WHERE DATE_ADD(t.created_on, INTERVAL ".$arrayDate[$timeFrame].") > SYSDATE()";
		}
		else{
			$strQuery ="";
			$strQuery2 ="";
		}
	}
	else{
		$strQuery = "AND DATE_ADD(t.created_on, INTERVAL 1 WEEK) > SYSDATE()";
		$strQuery2 = "WHERE DATE_ADD(t.created_on, INTERVAL 1 WEEK) > SYSDATE()";
	}
}
else{
	$strQuery = "AND DATE_ADD(t.created_on, INTERVAL 1 WEEK) > SYSDATE()";
	$strQuery2 = "WHERE DATE_ADD(t.created_on, INTERVAL 1 WEEK) > SYSDATE()";
}


?>
SELECT the_date,
       SUM(the_count) AS filters,
       SUM(the_count2) AS threads_preferred,
       SUM(the_count3) AS threads_auto,
       SUM(the_count4) AS total
  FROM (
        SELECT LEFT(t.created_on, 10) AS the_date,
               0 AS the_count,
               0 AS the_count2,
               0 AS the_count3,
               COUNT(t.id) AS the_count4,
               'total' AS src
          FROM dataentry.threads t
               INNER JOIN rentjuice.offices o ON o.id = t.office_id
         WHERE o.region_id = 2
           AND t.created_on > "2012-03-01 00:00:00"
           <?php echo $strQuery; ?> 
           GROUP BY 1

         UNION
        
        SELECT LEFT(e.received_on, 10) AS the_date,
               COUNT(e.id) AS the_count,
               0 AS the_count2,
               0 AS the_count3,
               0 AS the_count4,
               'filters' AS src
          FROM dataentry.emails e
               INNER JOIN dataentry.threads t ON t.id = e.thread_id
                      AND e.content_office_id > 0
                      AND t.created_on > "2012-03-01 00:00:00"
               INNER JOIN rentjuice.offices o ON o.id = t.office_id
                      AND o.region_id = 2
          <?php echo $strQuery2; ?> 
          GROUP BY 1

         UNION
        
        SELECT LEFT(t.created_on, 10) AS the_date,
               0 AS the_count,
               COUNT(t.id) AS the_count2,
               0 AS the_count3,
               0 AS the_count4,
               'threads_preferred' AS src 
          FROM dataentry.threads t
               INNER JOIN rentjuice.offices o ON o.id = t.office_id
                      AND o.region_id = 2
         WHERE t.preferred = 1
            <?php echo $strQuery; ?> 
         GROUP BY 1
        
         UNION

        SELECT LEFT(t.created_on, 10) AS the_date,
               0 AS the_count, 0 AS
               the_count2,
               COUNT(t.id) AS the_count3,
               0 AS the_count4,
               'threads_auto' AS src
          FROM dataentry.threads t
               INNER JOIN rentjuice.offices o ON o.id = t.office_id
                      AND o.region_id = 2
         WHERE t.auto_job = 1
            <?php echo $strQuery; ?> 
         GROUP BY 1
        
         ) AS tmp

 GROUP BY the_date


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

