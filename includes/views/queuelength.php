<?php render('_header',array('title'=>$title))?>
<?php
	// get the post hours data
	$hoursPost = '';
	$selectedHours = '';
	$arrayWeeks = array('168','336','504','672');
	$weeks='';
	
	if(isset($_REQUEST['hours'])){
		$hoursPost = $_REQUEST['hours'];
		if(in_array($_REQUEST['hours'],$arrayWeeks)){
			if($_REQUEST['hours'] == 168 ){ $selectedHours = "1 Week of Data"; }
			if($_REQUEST['hours'] == 336 ){ $selectedHours = "2 Week of Data"; }
			if($_REQUEST['hours'] == 504 ){ $selectedHours = "3 Week of Data"; }
			if($_REQUEST['hours'] == 672 ){ $selectedHours = "4 Week of Data"; }
			$weeks ="true";
		}
		else{
			$selectedHours = $_POST['hours']." Hours of Data";
			$weeks ="false";
		}
		
	}
	else{
		$selectedHours = "72 Hours of Data";
		$hoursPost = 72;
		$weeks ="false";
	}
	
?>

<div class="body-wrapper">
		<div class="centered">
			<div class="main_content_temp">
			
			<script type="text/javascript">

$(function () {

	//var isLoading = false,
//    $button = $('.select_hours');
//    $button.change(function() {
//        if (!isLoading) {
//            chart.showLoading();
//        } else {
//            chart.hideLoading();
//        }
//        isLoading = !isLoading;
//    });
//	//chart initialization
//	
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
           type : 'datetime',

           labels: {
                formatter: function() {
                    return Highcharts.dateFormat('%m/%d/%Y <?php if($weeks=="false"){ echo"<br/> %l %p"; } ?>', this.value);
                }
            }
			<?php if($selectedHours == "4 Week of Data" || $selectedHours == "3 Week of Data"){ ?> ,tickInterval: 7 * 24 * 3600 * 1000  <?php } ?>   // one week

        },
		yAxis: {

			title: {
				text: 'Items in Queue'
			},
			min:0
		},
		tooltip: {
			 formatter: function() {
				  return '<b>'+ this.series.name +'</b><br/>'+
				   Highcharts.dateFormat('%m/%d/%Y : %l %p', this.x) +' = '+ this.y;
			 }
		  },

        series: [{
			name: '<?php echo $selectedHours; ?>',
			 pointWidth: 3,
            data: [
					<?php
					
							$getValue = $barcontent;
							$countData = count($getValue);
							if($countData != 0){
								$i = 0;
								foreach($getValue as $value){
									$i = $i + 1;
									$arrayValues = explode(",",$value->the_hour);
									$getmonth = $arrayValues[1] - 1;
									$reindex = $arrayValues[0].",".$getmonth.",".$arrayValues[2].",".$arrayValues[3]; // year,month,day,time
									
									echo "[Date.UTC(".$reindex."), ".$value->num_in_de_qa_at_hour."]";
									
									if($i != $countData){ echo",";}
								}
								$i++;
							}
					?>
				  ]
		}]
		
    }); // esnd new Highcharts
});// end function


</script>

<?php
/*echo"<pre>";
print_r($barcontent);
echo"</pre>";*/
?>

<div class="align_center">
	<div class="manualmerges_title"><?php echo $title; ?></div>
</div>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="form_select" id="form_submit">
<div class="align_right">
	<input type="hidden" value="queuelength" name="page" />
	<span>Select Hours: </span>
	<select name="hours" onchange="submit();" class="select_hours">
		<option value="24" <?php if($hoursPost == 24){ echo'selected'; }else{ echo''; }  ?>>24 hours</option>
		<option value="48" <?php if($hoursPost == 48){ echo'selected'; }else{ echo''; }  ?>>48 hours</option>
		<option value="72" <?php if($hoursPost == 72){ echo'selected'; }else{ echo''; }  ?> >72 hours</option>
		<option value="168" <?php if($hoursPost == 168){ echo'selected'; }else{ echo''; }  ?>>1 week</option>
		<option value="336" <?php if($hoursPost == 336){ echo'selected'; }else{ echo''; }  ?>>2 weeks</option>
		<option value="504" <?php if($hoursPost == 504){ echo'selected'; }else{ echo''; }  ?>>3 weeks</option>
		<option value="672" <?php if($hoursPost == 672){ echo'selected'; }else{ echo''; }  ?>>4 weeks</option>
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
SELECT h AS the_hour,
       SUM(IF(ts.de_qa_started, 1, 0)) AS num_in_de_qa_at_hour
  FROM
       (SELECT id, DATE_FORMAT(DATE_SUB(NOW(), INTERVAL id HOUR), "%Y-%m-%d %H:00:00") AS h
          FROM dataentry.threads
         WHERE id <= <?php echo $hoursPost; ?> 
         ORDER BY id) AS hours
        
        LEFT OUTER JOIN (       
        SELECT t.id,
               ts1.completed_on AS de_qa_started,
               ts2.created_on AS de_qa_finished
          FROM dataentry.threads t
               LEFT OUTER JOIN dataentry.thread_sessions ts1
                  ON ts1.thread_id = t.id
                  AND ts1.id = (SELECT id FROM dataentry.thread_sessions WHERE thread_id = t.id ORDER BY id ASC LIMIT  1)
               LEFT OUTER JOIN dataentry.thread_sessions ts2
                  ON ts2.thread_id = t.id
                  AND ts2.id = (SELECT id FROM dataentry.thread_sessions WHERE thread_id = t.id ORDER BY id ASC LIMIT  1, 1)
         WHERE deleted = 0
           AND t.created_on > DATE_SUB(NOW(), INTERVAL <?php echo $hoursPost; ?> HOUR)
         GROUP BY t.id
         ) AS ts
         ON hours.h > ts.de_qa_started AND (hours.h < ts.de_qa_finished OR ts.de_qa_finished IS NULL)
 GROUP BY hours.h
 ORDER BY hours.h DESC

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

