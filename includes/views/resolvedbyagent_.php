<?php render('_header')?>
<?php

	$timeFrame = "";
	
	if(isset($_REQUEST['resolvedbyagent_time'])){
		$timeFrame = $_REQUEST['resolvedbyagent_time'];
	}
	else{
		$timeFrame = '2';
	}
	if(isset($_REQUEST['resolvedbyagent_type'])){
		$ticketType = $_REQUEST['resolvedbyagent_type'];
	}
	else{
		$ticketType = '1';
	}
	
		
?>
<div class="body-wrapper">
		<div class="" style="width:85%; margin:0 auto;">
			<div class="main_content_temp">

<script type="text/javascript">

$(function () {

	var isLoading = false,
    $button = $('.select_time');
    $button.change(function() {
        if (!isLoading) {
            chart.showLoading();
        } else {
            chart.hideLoading();
        }
        isLoading = !isLoading;
    });
	//chart initialization
	Highcharts.setOptions({
		lang: {
			loading: 'Waiting for Data'
		}
	});
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
			<?php
				if(isset($_REQUEST['resolvedbyagent_time'])){
					if($_REQUEST['resolvedbyagent_time'] == 1){
						echo",tickInterval:24 * 3600 * 1000	";
					}
					if($_REQUEST['resolvedbyagent_time'] == 2){
						echo",tickInterval:2 * 24 * 3600 * 1000	";
					}
					if($_REQUEST['resolvedbyagent_time'] == 3){
						echo",tickInterval:3 * 24 * 3600 * 1000	";
					}
					if($_REQUEST['resolvedbyagent_time'] == 4){
						echo",tickInterval:5 * 24 * 3600 * 1000	";
					}
					if($_REQUEST['resolvedbyagent_time'] > '5'){
						echo",tickInterval:90 * 24 * 3600 * 1000	";
					}
				}
				else{
					echo",tickInterval:2 * 24 * 3600 * 1000	";
				}
			?>
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
							//$resolved = trim($value->resolved);
							//$countdata = trim($value->countdata);
							
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
									
									echo "[Date.UTC(".$reindex."),".$valueListings."]"; 
									
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
	<div class="manualmerges_title">Resolved by Agent</div>
	<br />
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


<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" >
	
	<div class="align_right">
		<input type="hidden" value="resolvedbyagent" name="page" />
		<span>Select Time Frame: </span>
		<select name="resolvedbyagent_time" onchange="submit();" class="select_time">
			<option value="1" <?php if($timeFrame == '1'){ echo'selected'; }else{ echo''; }  ?>>1 Week</option>
			<option value="2" <?php if($timeFrame == '2'){ echo'selected'; }else{ echo''; }  ?>>2 Weeks</option>
			<option value="3" <?php if($timeFrame == '3'){ echo'selected'; }else{ echo''; }  ?>>3 Weeks</option>
			<option value="4" <?php if($timeFrame == '4'){ echo'selected'; }else{ echo''; }  ?>>4 Weeks</option>
			<option value="5" <?php if($timeFrame == '5'){ echo'selected'; }else{ echo''; }  ?>>2 Months</option>
			<option value="6" <?php if($timeFrame == '6'){ echo'selected'; }else{ echo''; }  ?>>3 Months</option>
			<option value="7" <?php if($timeFrame == '7'){ echo'selected'; }else{ echo''; }  ?>>4 Months</option>
			<option value="8" <?php if($timeFrame == '8'){ echo'selected'; }else{ echo''; }  ?>>5 Months</option>
			<option value="9" <?php if($timeFrame == '9'){ echo'selected'; }else{ echo''; }  ?>>6 Months</option>
			<!--<option value="10" <?php //if($timeFrame == '10'){ echo'selected'; }else{ echo''; }  ?>>All Time</option>-->
		</select>
		&nbsp;&nbsp;&nbsp;&nbsp;
		<span>Ticket Type: </span>
		<select name="resolvedbyagent_type" onchange="submit();" class="select_time">
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
<br />
<div class="align_right"><a class='inline' href="#inline_content">Show SQL Query</a></div>
<div style="display:none">
<div id='inline_content' style='padding:10px; background:#fff;'>
<pre>
<?php
	$arrayDate = array(
						'1'=>'7',
						'2'=>'14',
						'3'=>'21',
						'4'=>'28',
						'5'=>'60',
						'6'=>'90',
						'7'=>'120',
						'8'=>'150',
						'9'=>'180',
						'10'=>'alltime'
						);
				
	if(isset($_REQUEST['resolvedbyagent_time'])){
	
		if (array_key_exists($_REQUEST['resolvedbyagent_time'],$arrayDate)){
			$date = $arrayDate[$_REQUEST['resolvedbyagent_time']];
		}
		else{
			$date = "14";
		}
	
	}
	else{
		$date = "14";
	}
	
	if($date != "10"){
			$strQuery = "AND DATE_ADD(resolved_at, INTERVAL ".$date." DAY) > SYSDATE()";
			$strQuery2 = " WHERE id <=".$date."";
	}
	else{
		$strQuery ="";
		$strQuery2 ="";
	}
?>
SELECT user_days.username, user_days.resolved_at, IFNULL(ticket_count,0) ticket_count
FROM
   (
	SELECT *
	  FROM
		   (SELECT username
			  FROM janak.assistly_cases
			 WHERE username IS NOT NULL
			 <?php echo $strQuery; ?>
			 GROUP BY 1
		   ) users,
		   (SELECT DATE_SUB(DATE(SYSDATE()),INTERVAL id DAY) resolved_at
			  FROM janak.counter_records
			  <?php echo $strQuery2; ?>
		   ) days
   ) user_days
	LEFT OUTER JOIN
   (
	SELECT username, DATE(resolved_at) resolved_at, COUNT(*) ticket_count
	  FROM janak.assistly_cases
	 WHERE username IS NOT NULL
	 <?php echo $strQuery; ?>
	 <?php echo $strQueryType; ?>
	 GROUP BY 1,2
	
   ) tickets
   ON
	  user_days.resolved_at = tickets.resolved_at
	  AND user_days.username = tickets.username
	  ORDER BY user_days.resolved_at ASC

------------------------------------------------------------------------------

Agents Total Counts for Tickets

SELECT username, COUNT(*)
 FROM assistly_cases
WHERE username IS NOT NULL
AND DATE_ADD(resolved_at, INTERVAL <?php echo  $date; ?> DAY) > SYSDATE()
GROUP BY 1
ORDER BY 2 DESC
</pre>
</div>
</div>

			</div>
			
		</div>
</div>
<?php render('_footer')?>

