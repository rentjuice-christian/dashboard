<?php render('_header',array('title'=>'Resolved by Super Tag'))?>
<?php
	
	$tagUsageLabel = "";
	$timeFrame = "";
	
	if(isset($_REQUEST['tags'])){
		if($_REQUEST['tags'] == "alltags"){ $tagUsageLabel ="All Tags";}
		else{ $tagUsageLabel = urldecode($_REQUEST['tags']); }
	}
	else{ $tagUsageLabel = 'All Tags'; }
	
	if(isset($_REQUEST['timespan'])){ $timeFrame = $_REQUEST['timespan']; }
	else{ $timeFrame = '5'; }
	
	if(isset($_REQUEST['movingaverage'])){
			
		if (!empty($_REQUEST['movingaverage'])){
			if( $_REQUEST['movingaverage'] < 30 ){	$dateMove = $_REQUEST['movingaverage'];	}
			else{ $dateMove = "7"; }
		}
		else{ $dateMove = "7"; }
		
	}
	else{ $dateMove = "7"; }
	
	if(isset($_REQUEST['type'])){ $ticketType = $_REQUEST['type']; }
	else{ $ticketType = '1'; }
	
	if(isset($_REQUEST['offices'])){ $officeNameValue = $_REQUEST['offices']; }
	else{ $officeNameValue = 'all'; }
	
	if(isset($_REQUEST['users'])){ $usersNameValue = $_REQUEST['users']; }
	else{ $usersNameValue = 'all'; }
	
	
		
?>
<div class="body-wrapper">
		<div class="" style="width:87%; margin:0 auto;">
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
				  return '<b>'+ this.series.name +'</b><br/>Total Ticket Count in '+ Highcharts.dateFormat('%b %d,%Y', this.x) +'='+ this.y +'';
			 }
		  },

        series: [
			<?php
			
					$newOptions = array();
					
					$getValue = $barcontent;
					
					$countData = count($getValue);
					if($getValue != 0){
						
						foreach($getValue as $value){
							$tag_name = trim($value->tag_name);
							$resolved = trim($value->resolved_at);
							$countdata = trim($value->ticket_count);
							if(!empty($resolved)){
								$newOptions[$tag_name][$resolved] =  $countdata; // re array by region
							}
							
						}
					}
					
					$countUser = count($newOptions);
					$i = 0;
					
					$result = array_map('array_sum', $newOptions); // total the sum of the reult query
					arsort($result); // sort it higher first
					
					$result2 = array_merge($result,  $newOptions); //merge array 
					
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
 if(!empty($error_message)){
	render('error',array('error_message'=>$error_message));
 }
 else{
?>

<div class="align_center">
	<div class="manualmerges_title">Resolved by Super Tag</div>
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
		<input type="hidden" value="resolvedbysupertag" name="page" />
		<span>Users</span>
		<select name="users" onchange="submit();" class="select_time" style="width:150px;">
			<option <?php if($usersNameValue == "all"){ echo "selected"; } else{ echo""; } ?> value="all">All Users</option>
			<?php
				$getValueUsers = $barcontent_users;
				$countDataUsers = count($getValueUsers);
				if($countDataUsers != 0){
					
					foreach($getValueUsers as $value){
						$userFirstName = trim($value->first_name);
						$userLastName = trim($value->last_name);
						$userID = trim($value->rentjuice_user_id);
						$userCount = trim($value->user_count);
					
			?>
					<option value="<?php echo $userID ; ?>" <?php if($usersNameValue == $userID){ echo "selected"; } else{ echo""; } ?>>
						<?php echo $userLastName.",".$userFirstName." (".$userCount.")"; ?>
					</option>
			<?php				
						
					}
				}
			?>
			
		</select>
		&nbsp;&nbsp;&nbsp;&nbsp;
		<span>Offices</span>
		<select name="offices" onchange="submit();" class="select_time" style="width:150px;">
			<option <?php if($officeNameValue == "all"){ echo "selected"; } else{ echo""; } ?> value="all">All Offices</option>
			<?php
				$getValueOffice = $barcontent_office;
				$countDataOffice = count($getValueOffice);
				if($countDataOffice != 0){
					
					foreach($getValueOffice as $value){
						$officeName = trim($value->name);
						$officeID = trim($value->rentjuice_office_id);
						$countOffice = trim($value->ticket_counts);
						if(!empty($officeName)){
			?>
					<option value="<?php echo $officeID ; ?>" <?php if($officeNameValue == $officeID){ echo "selected"; } else{ echo""; } ?>>
						<?php echo $officeName." (".$countOffice.")"; ?>
					</option>
			<?php				
						}
					}
				}
			?>
			
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
	<?php } ?>

<?php } ?>

<div class="align_right show_query">
	<a class='inline' href="#inline_content"><img src="assets/images/show_query.png" alt="show query" /></a>
</div>
<div style="display:none">
<div id='inline_content' style='padding:10px; background:#fff;'>
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

?>
<pre>
<?php
if($type == "alltickets"){
?>
SELECT tag_days.tag_name,
       tag_days.resolved_at,
       (
        SELECT COUNT(*)/IF(DATEDIFF(SYSDATE(),tag_days.resolved_at) >= <?php echo $dateMove; ?>, <?php echo $dateMove; ?>, DATEDIFF(SYSDATE(),tag_days.resolved_at))
          FROM janak.assistly_cases c
         WHERE c.super_tag = tag_days.tag_name
           AND tag_days.resolved_at <= DATE(resolved_at) AND DATE(resolved_at) <= DATE_ADD(tag_days.resolved_at, INTERVAL <?php echo ($dateMove - 1); ?> DAY) 
       ) ticket_count
  FROM  
       (
        SELECT *
          FROM
               (SELECT tag_name
                  FROM janak.super_tags                
               ) tags,
               (SELECT DATE_SUB(DATE(SYSDATE()),INTERVAL id DAY) resolved_at
                  FROM janak.counter_records
                 WHERE id <= <?php echo $timespan; ?> 
               ) days
       ) tag_days
 ORDER BY tag_days.resolved_at, tag_days.tag_name ASC 
<?php
}
else{
?>
SELECT tag_days.tag_name,
       tag_days.resolved_at,
       (
        SELECT COUNT(*)/IF(DATEDIFF(SYSDATE(),tag_days.resolved_at) >= <?php echo $dateMove; ?>, <?php echo $dateMove; ?>, DATEDIFF(SYSDATE(),tag_days.resolved_at))
          FROM janak.assistly_cases c
         WHERE c.super_tag = tag_days.tag_name
           AND c.channel = '<?php echo $type; ?>'
           AND tag_days.resolved_at <= DATE(resolved_at) AND DATE(resolved_at) <= DATE_ADD(tag_days.resolved_at, INTERVAL <?php echo ($dateMove - 1); ?> DAY) 
       ) ticket_count
  FROM  
       (
        SELECT *
          FROM
               (SELECT tag_name
                  FROM janak.super_tags                
               ) tags,
               (SELECT DATE_SUB(DATE(SYSDATE()),INTERVAL id DAY) resolved_at
                  FROM janak.counter_records
                  WHERE id <= <?php echo $timespan; ?> 
               ) days
       ) tag_days
 ORDER BY tag_days.resolved_at, tag_days.tag_name ASC 
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


