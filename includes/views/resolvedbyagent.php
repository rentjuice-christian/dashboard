<?php render('_header',array('title'=>$title))?>
<?php
	
	$tagUsageLabel = "";
	$timeFrame = "";
	
	if(isset($_REQUEST['tags'])){
		if($_REQUEST['tags'] == "alltags"){ $tagUsageLabel ="All Tags";}
		else{ $tagUsageLabel = urldecode($_REQUEST['tags']); }
	}
	else{ $tagUsageLabel = 'All Tags'; }
	
	if(isset($_REQUEST['timespan'])){$timeFrame = $_REQUEST['timespan'];}
	else{$timeFrame = '5';}
	
	if(isset($_REQUEST['movingaverage'])){
			
		if (!empty($_REQUEST['movingaverage'])){
			if( $_REQUEST['movingaverage'] < 30 ){	$dateMove = $_REQUEST['movingaverage'];	}
			else{ $dateMove = "7"; }
		}
		else{ $dateMove = "7"; }
	}
	else{ $dateMove = "7"; }
	
	
	if(isset($_REQUEST['type'])){$ticketType = $_REQUEST['type'];}
	else{$ticketType = '1';}
	
		
?>
<div class="body-wrapper">
		<div class="" style="width:87%; margin:0 auto;">
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
						<?php //if($timeFrame > 4){ ?>
						enabled: false,
						<?php //} ?>
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
			borderWidth: 1
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
				  return '<b>'+ this.series.name +'</b><br/>Total Counts for Tickets in '+ Highcharts.dateFormat('%b %d,%Y', this.x) +'='+ this.y +'';
			 }
		  },

        series: [
			<?php
			
					$newOptions = array();
					
					$getValue = $barcontent;
					
					$countData = count($getValue);
					if($getValue != 0){
						
						foreach($getValue as $value){
							$username = trim($value->username);
							$resolved = trim($value->resolved_at);
							$countdata = trim($value->ticket_count);
							if(!empty($resolved)){
								$newOptions[$username][$resolved] =  $countdata; // re array by region
							}
							
						}
					}
					
					$countUser = count($newOptions);
					$i = 0;
					
					$result = array_map('array_sum', $newOptions);
					arsort($result);
					
					$result2 = array_merge($result,  $newOptions);
					
					foreach($result2 as $keyUser => $valueUser){
					
						if(array_sum($valueUser) != 0){
						$i = $i + 1;
						echo"{";
							$countListings = count($valueUser);
							echo"name:'".$keyUser."',";
							echo"data: [";
							$a = 0;
								foreach($valueUser as $keyDate => $valueListings ){
									$a = $a + 1;
									
									$arrayValues = explode("-",$keyDate);
									$getmonth = $arrayValues[1] - 1;
									$reindex = $arrayValues[0].",".$getmonth.",".$arrayValues[2];
									
									echo "[Date.UTC(".$reindex."),". round($valueListings,1)."]"; 
									
									if($a != $countListings){ echo",";}			
								}
								$a++;
							echo"]";
						echo"}";
						
						if($i != $countUser){ echo",";}
						}
						
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
	<div class="manualmerges_title"><?php echo $title; ?></div>
</div>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#example').dataTable( {
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"bSort":false,
			"iDisplayLength": 100
		} );
	} );
</script>


<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="form_select" id="form_submit" >
	
	<div class="align_right">
		<input type="hidden" value="resolvedbyagent" name="page" />
		<span>Select Tags: </span>
		<select name="tags" onchange="submit();" class="select_time">
			
			<?php
				if(count($tag_barcontent) != 0){
					foreach($tag_barcontent as $valuesub){
				?>
						<option value="<?php echo urlencode($valuesub->label_name); ?>" <?php if($tagUsageLabel == $valuesub->label_name){ echo'selected'; }else{ echo''; }  ?>>
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
		&nbsp;&nbsp;&nbsp;&nbsp;
		<span>Ticket Type: </span>
		<select name="type" onchange="submit();" class="select_time">
			<option value="1" <?php if($ticketType == '1'){ echo'selected'; }else{ echo''; }  ?>>All Tickets</option>
			<option value="2" <?php if($ticketType == '2'){ echo'selected'; }else{ echo''; }  ?>>Emails</option>
			<option value="3" <?php if($ticketType == '3'){ echo'selected'; }else{ echo''; }  ?>>Phone Calls</option>
			<option value="4" <?php if($ticketType == '4'){ echo'selected'; }else{ echo''; }  ?>>Chats</option>
			<option value="5" <?php if($ticketType == '5'){ echo'selected'; }else{ echo''; }  ?>>Twitter</option>
			<option value="6" <?php if($ticketType == '6'){ echo'selected'; }else{ echo''; }  ?>>QNA</option>
		</select>
		
	</div>
</form>




<?php if($countListings == 0){ ?>
	<div style="min-width: 400px; height: 250px; padding-top:150px; margin: 10px auto 0; text-align:center; font-size:24px; border:1px solid #CCCCCC">No Available Data</div>
<?php 
}
else{
?>
	<div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="example" width="100%">
	<thead>
		<tr>
			<th><div class="bold">Agent Name</div></th>
			<th><div class="bold">Total Counts for Tickets</div></th>
		</tr>
	</thead>
	<tbody>
	
	<?php
			$getValue2 = $sub_barcontent;
			$countData2 = count($getValue2);
			
			if($countData2 != 0){
				$i = 0;
				foreach($getValue2 as $value){
				$i = $i + 1;
				 if($i%2 == 0){ $class = 'even';  }
				 else{ $class = 'odd'; }

				?>
			<tr class="<?php echo $class; ?>">
				<td style="border-left:1px solid #cccccc; border-bottom:1px solid #cccccc;">
						<?php echo $value->username; ?>
				</td>
				<td class="center"  style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; border-bottom:1px solid #cccccc; "><?php echo $value->countData; ?></td>
			</tr>
				<?php
				}
				$i++;
			}
		?>

	</tbody>
	<tfoot>
</table>
<?php } ?>

<div class="align_right show_query">
	<a class='inline' href="#inline_content"><img src="assets/images/show_query.png" alt="show query" /></a>
</div>
<div style="display:none">
<div id='inline_content' style='padding:10px; background:#fff;'>
<pre>
<?php
$arrayDate = array('1'=>'7','2'=>'14','3'=>'21','4'=>'28','5'=>'30','6'=>'60','7'=>'90','8'=>'120','9'=>'150','10'=>'180','11'=>'360','12'=>'720');
$arrayType = array('1'=>'alltickets','2'=>'email','3'=>'phone','4'=>'chat','5'=>'tweet','6'=>'qna');
if (array_key_exists($timeFrame,$arrayDate)){
	$timespan = $arrayDate[$timeFrame];
}
else{ $timespan= "30"; }
	
if (array_key_exists($ticketType,$arrayType)){
	$type = $arrayType[$ticketType];
}
else{ $type = "alltickets"; }

if($type != "alltickets"){ 	$strQueryType = "AND channel = '".$type."'";  }

	
if($tagUsageLabel !="All Tags" && $type != "alltickets" ){
?>
SELECT user_days.username,
       user_days.resolved_at,

       (
        SELECT COUNT(*)/IF(DATEDIFF(SYSDATE(),user_days.resolved_at) >= <?php echo $dateMove; ?>, <?php echo $dateMove; ?>, DATEDIFF(SYSDATE(),user_days.resolved_at))
          FROM janak.assistly_cases c, janak.assistly_case_labels cl
         WHERE username = user_days.username
           AND c.id = cl.case_id
           AND cl.label_name = '<?php echo $tagUsageLabel; ?>'
           AND c.channel = '<?php echo $type; ?>'
           AND user_days.resolved_at <= DATE(resolved_at) AND DATE(resolved_at) <= DATE_ADD(user_days.resolved_at, INTERVAL <?php echo ($dateMove - 1); ?> DAY)  
       ) ticket_count
  FROM   
       (
        SELECT *
          FROM
               (SELECT username
                  FROM janak.assistly_cases
                 WHERE username IS NOT NULL
                   AND DATE_ADD(resolved_at, INTERVAL <?php echo $timespan; ?> DAY) > SYSDATE()

                 GROUP BY 1
               ) users,
               (SELECT DATE_SUB(DATE(SYSDATE()),INTERVAL id DAY) resolved_at
                  FROM janak.counter_records
                 WHERE id <= <?php echo $timespan; ?>

               ) days
       ) user_days
 ORDER BY user_days.resolved_at, username ASC 
<?php
}
if($tagUsageLabel =="All Tags" && $type != "alltickets" ){
?>
SELECT user_days.username,
       user_days.resolved_at,

       (
        SELECT COUNT(*)/IF(DATEDIFF(SYSDATE(),user_days.resolved_at) >= <?php echo $dateMove; ?>, <?php echo $dateMove; ?>, DATEDIFF(SYSDATE(),user_days.resolved_at))
          FROM janak.assistly_cases c
         WHERE username = user_days.username
           AND c.channel = '<?php echo $type; ?>'
           AND user_days.resolved_at <= DATE(resolved_at) AND DATE(resolved_at) <= DATE_ADD(user_days.resolved_at, INTERVAL <?php echo ($dateMove - 1); ?> DAY)  
       ) ticket_count
  FROM   
       (
        SELECT *
          FROM
               (SELECT username
                  FROM janak.assistly_cases
                 WHERE username IS NOT NULL
                   AND DATE_ADD(resolved_at, INTERVAL <?php echo $timespan; ?> DAY) > SYSDATE()

                 GROUP BY 1
               ) users,
               (SELECT DATE_SUB(DATE(SYSDATE()),INTERVAL id DAY) resolved_at
                  FROM janak.counter_records
                 WHERE id <= <?php echo $timespan; ?>

               ) days
       ) user_days
 ORDER BY user_days.resolved_at, username ASC 
<?php	   
}
if($tagUsageLabel !="All Tags" && $type == "alltickets" ){
?>
SELECT user_days.username,
       user_days.resolved_at,
       (
        SELECT COUNT(*)/IF(DATEDIFF(SYSDATE(),user_days.resolved_at) >= <?php echo $dateMove; ?>, <?php echo $dateMove; ?>, DATEDIFF(SYSDATE(),user_days.resolved_at))
          FROM janak.assistly_cases c, janak.assistly_case_labels cl
         WHERE username = user_days.username
           AND c.id = cl.case_id
           AND cl.label_name = '<?php echo $tagUsageLabel; ?>'
           AND user_days.resolved_at <= DATE(resolved_at) AND DATE(resolved_at) <= DATE_ADD(user_days.resolved_at, INTERVAL <?php echo ($dateMove - 1); ?> DAY)  
       ) ticket_count
  FROM   
       (
        SELECT *
          FROM
               (SELECT username
                  FROM janak.assistly_cases
                 WHERE username IS NOT NULL
                   AND DATE_ADD(resolved_at, INTERVAL <?php echo $timespan; ?> DAY) > SYSDATE()
                 GROUP BY 1
               ) users,
               (SELECT DATE_SUB(DATE(SYSDATE()),INTERVAL id DAY) resolved_at
                  FROM janak.counter_records
                 WHERE id <= <?php echo $timespan; ?>
               ) days
       ) user_days
 ORDER BY user_days.resolved_at, username ASC 
<?php	   
}
if($tagUsageLabel =="All Tags" && $type == "alltickets" ){
?>
SELECT user_days.username,
       user_days.resolved_at,
       (
        SELECT COUNT(*)/IF(DATEDIFF(SYSDATE(),user_days.resolved_at) >= <?php echo $dateMove; ?>, <?php echo $dateMove; ?>, DATEDIFF(SYSDATE(),user_days.resolved_at))
          FROM janak.assistly_cases c
         WHERE username = user_days.username
           AND user_days.resolved_at <= DATE(resolved_at) AND DATE(resolved_at) <= DATE_ADD(user_days.resolved_at, INTERVAL <?php echo ($dateMove - 1); ?> DAY)  
       ) ticket_count
  FROM   
       (
        SELECT *
          FROM
               (SELECT username
                  FROM janak.assistly_cases
                 WHERE username IS NOT NULL
                   AND DATE_ADD(resolved_at, INTERVAL <?php echo $timespan; ?> DAY) > SYSDATE()
                 GROUP BY 1
               ) users,
               (SELECT DATE_SUB(DATE(SYSDATE()),INTERVAL id DAY) resolved_at
                  FROM janak.counter_records
                 WHERE id <= <?php echo $timespan; ?>
               ) days
       ) user_days
 ORDER BY user_days.resolved_at, username ASC 
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

