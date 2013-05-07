<?php render('_header')?>
<?php
	$getValue = $barcontent;
	$getValueSuperTag = $sub_selectsupertag;
	$countData = count($getValue);
	$countTicket = 0;
	$timeFrame = "";
	$timeValue = array('1'=>'In the last week',
					   '2'=>'In the last two weeks',
					   '3'=>'In the last three weeks',
					   '4'=>'In the last four weeks',
					   '5'=>'In the last 1 month',
					   '6'=>'In the last 2 months',
					   '7'=>'In the last 3 months',
					   '8'=>'In the last 4 months',
					   '9'=>'Since the dawn of the third age of mankind...'
					   );
	
	if(isset($_REQUEST['ticketsinboundbyuser_time'])){ $timeFrame = $_REQUEST['ticketsinboundbyuser_time']; }
	else{ $timeFrame = '4'; }
	
	if(isset($_REQUEST['tickettype'])){ $ticketType = $_REQUEST['tickettype']; }
	else{ $ticketType = '1'; }
	
	if(isset($_REQUEST['selectsupertag'])){ $superTags = urldecode($_REQUEST['selectsupertag']); }
	else{ $superTags = 'allsupertags'; }
	
	if(isset($_REQUEST['selecttags'])){ $tagUsageLabel = urldecode($_REQUEST['selecttags']); }
	else{ $tagUsageLabel = 'alltags'; }
	
?>
<div class="body-wrapper">
		<div class="centered">
			
<?php
	/*echo"<pre>";
		print_r($barcontent);
	echo"</pre>";*/
?>

<div class="align_center">
	<div class="manualmerges_title">Tickets Inbound for Top 100 Users</div>
</div>
<div style="margin-top:10px; padding:5px 0; border-top:1px solid #CCCCCC;">
	<div class="align_left left">
		<div class="bold" style="font-size:16px;">
			<?php
				foreach($timeValue as $key=>$value){ if($timeFrame == $key){	echo $value; break;	} }	
			?>
		</div>
		<div>
			The top <b><?php echo  $sub_barcontent[0]->user_count; ?> </b> users accounted for 
			<b><?php echo  round($sub_barcontent[0]->ticket_count / ($sub_barcontent[0]->ticket_count + $sub_barcontent2[0]->ticket_count) * 100); ?>% </b>
			of our tickets
		</div>
		<div>
			 The other <b><?php  echo  $sub_barcontent2[0]->user_count; ?> </b>
			 users accounted for <b><?php echo  round($sub_barcontent2[0]->ticket_count / ($sub_barcontent[0]->ticket_count + $sub_barcontent2[0]->ticket_count) * 100); ?>% </b>
			 of our tickets
		</div>
	</div>
	<div class="right" >
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" style="margin-bottom:10px;" class="form_select" id="form_submit">
		<div class="align_right">
			<input type="hidden" value="ticketsinboundbyuser" name="page" />
			
			<div style="padding-top:5px;">
			<span>Select Super Tag: </span>
			<select name="selectsupertag" onchange="submit();" class="select_time">
				<?php
					foreach($getValueSuperTag as $valueSuperTag){
						if(!empty($valueSuperTag->super_tag)){
							?>
									<option value="<?php echo urlencode($valueSuperTag->super_tag); ?>" <?php if($superTags == $valueSuperTag->super_tag){ echo"selected";} else{ echo""; } ?>>
							<?php
									echo $valueSuperTag->super_tag." (".$valueSuperTag->super_tag_count.")"; 
							?>
									</option>
							<?php
						}
					}
				?>
				<option value="allsupertags" <?php if($superTags =="allsupertags"){ echo"selected";} else{ echo""; } ?>>All Super Tags</option>
			</select>
			</div>
			<div style="padding-top:5px;">
			<span>Select Tags: </span>
			<select name="selecttags" onchange="submit();" class="select_time">
				
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
				<option value="alltags" <?php if($tagUsageLabel =="alltags"){ echo"selected";} ?>>All Tags</option>
			</select>
			</div>
			<div style="padding-top:5px;">
			<span>Select Time Frame: </span>
			<select name="ticketsinboundbyuser_time" onchange="submit();" class="select_time">
				<option value="1" <?php if($timeFrame == '1'){ echo'selected'; }else{ echo''; }  ?>>1 Week</option>
				<option value="2" <?php if($timeFrame == '2'){ echo'selected'; }else{ echo''; }  ?>>2 Weeks</option>
				<option value="3" <?php if($timeFrame == '3'){ echo'selected'; }else{ echo''; }  ?>>3 Weeks</option>
				<option value="4" <?php if($timeFrame == '4'){ echo'selected'; }else{ echo''; }  ?>>4 Weeks</option>
				<option value="5" <?php if($timeFrame == '5'){ echo'selected'; }else{ echo''; }  ?>>1 Month</option>
				<option value="6" <?php if($timeFrame == '6'){ echo'selected'; }else{ echo''; }  ?>>2 Months</option>
				<option value="7" <?php if($timeFrame == '7'){ echo'selected'; }else{ echo''; }  ?>>3 Months</option>
				<option value="8" <?php if($timeFrame == '8'){ echo'selected'; }else{ echo''; }  ?>>4 Months</option>
				<option value="9" <?php if($timeFrame == 'alltime' || $timeFrame == '8'){ echo'selected'; }else{ echo''; }  ?>>All Time</option>
			</select>
			</div>
			<div style="padding-top:5px;">
			<span>Ticket Type: </span>
			<select name="tickettype" onchange="submit();" class="select_time">
				<option value="1" <?php if($ticketType == '1'){ echo'selected'; }else{ echo''; }  ?>>All Tickets</option>
				<option value="2" <?php if($ticketType == '2'){ echo'selected'; }else{ echo''; }  ?>>Emails</option>
				<option value="3" <?php if($ticketType == '3'){ echo'selected'; }else{ echo''; }  ?>>Phone Calls</option>
				<option value="4" <?php if($ticketType == '4'){ echo'selected'; }else{ echo''; }  ?>>Chats</option>
				<option value="5" <?php if($ticketType == '5'){ echo'selected'; }else{ echo''; }  ?>>Twitter</option>
				<option value="6" <?php if($ticketType == '6'){ echo'selected'; }else{ echo''; }  ?>>QNA</option>
			</select>
			</div>
		</div>
	</form>
	</div>
	<div class="clear"></div>
</div>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#ticketsinboundbyuser').dataTable( {
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"bSort":false,
			"iDisplayLength": 100
		} );
		
	   $('#ticketsinboundbyuser a.tooltip').each(function()
	   {
		  $(this).qtip({
				content: $(this).find('.preview'),
				show: { delay: 0 },
				position: {
					corner: {
						tooltip: 'topMiddle',
						target: 'bottomMiddle'
					}
				},
				style: {
					tip: true, // Give it a speech bubble tip with automatic corner detection
					name: 'dark'
				}
		  });
		   $(this).qtip({
				content: $(this).find('.resolvedby'),
				show: { delay: 0 },
				position: {
					corner: {
						tooltip: 'topMiddle',
						target: 'bottomMiddle'
					}
				},
				style: {
					tip: true, // Give it a speech bubble tip with automatic corner detection
					name: 'dark'
				}
		  });
	   });
		
	} );
	
	
</script>
<div style="margin:0 auto; width:75%">
<table cellpadding="0" cellspacing="0" border="0" class="display" id="ticketsinboundbyuser">
	<thead>
		<tr>
			<th width="9%">&nbsp;</th>
			<th><div class="bold">Username</div></th>
			<th><div class="bold">Office Name</div></th>
			<th><div class="bold">Ticket Count</div></th>
			<th><div class="bold">Handle Time</div></th>
		</tr>
	</thead>
	<tbody>
	
	<?php
			//$getValue = $barcontent;
			//$countData = count($getValue);
			if($getValue != 0){
				$i = 0;
				foreach($getValue as $value){
				$i = $i + 1;
				 if($i%2 == 0){ $class = 'even';  }
				 else{ $class = 'odd'; }

				?>
					<tr class="<?php echo $class; ?>">
						<td style="border-left:1px solid #cccccc;">
							<a href="http://clients.rentjuice.com/dashboard/index.php?page=ticketsearchforusers&username=<?php echo urlencode($value->username); ?>&ticketsearch_time=8" class="tooltip" target="_blank">
								<img src="assets/images/user_ticket.png" alt="user tickets" />
								<div class="preview" style="display:none;">
								See all tickets of <?php echo $value->username; ?>
								</div>
							</a>
							&nbsp;
							<a href="http://clients.rentjuice.com/dashboard/index.php?page=resolvedbysupertag&users=<?php echo urlencode($value->custom_user_id); ?>&offices=<?php echo urlencode($value->office_id); ?>&movingaverage=7&timespan=<?php echo $timeFrame; ?>&type=1" class="tooltip" target="_blank">
								<img src="assets/images/chart_bar.png" alt="user tickets" width="14" />
								<div class="resolvedby" style="display:none;">
									Resolved by Super Tag
								</div>
							</a>
						</td>
						<td  style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; ">
							<a href="http://radmin.rentjuice.com/user:<?php echo urlencode($value->custom_user_id); ?>" target="_blank">
								<?php echo $value->username; ?>
							</a>
						</td>
						<td  style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; ">
							<a href="http://radmin.rentjuice.com/customer:<?php echo urlencode($value->office_id); ?>" target="_blank">
								<?php echo $value->officename; ?>
							</a>
						</td>
						<td class="align_right"  style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; ">
							<a href="index.php?page=ticketsearchforusers&username=<?php echo urlencode($value->username); ?>&ticketsearch_time=<?php echo $timeFrame; ?>">
								<?php echo $value->ticket_count; ?>
							</a>
						</td>
						<td class="align_right"  style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; ">
							<?php 
								if(!empty($value->handle_time_total)){
									//echo number_format($value->handle_time_total); 
									if($value->handle_time_total < 60){
										echo $value->handle_time_total." minutes"; 
									}
									else if($value->handle_time_total < 1440){
										if(floor($value->handle_time_total/60) == 1){
											echo floor($value->handle_time_total/60)." hour";
										}
										else{
											echo floor($value->handle_time_total/60)." hours";
										}
									}
									else if($value->handle_time_total > 1440){
										if(floor($value->handle_time_total/1440) == 1){
											echo floor($value->handle_time_total/1440)." day";
										}
										else{
											echo floor($value->handle_time_total/1440)." days";
										}
									}
									
								}
							?>
						</td>
					</tr>
				<?php
				}
				$i++;
			}
		?>

	</tbody>
	
</table>


<div class="align_right show_query"><a class='inline' href="#inline_content"><img src="assets/images/show_query.png" alt="show query" /></a></div>

</div>

<div style="display:none">
	<div id='inline_content' style='padding:10px; background:#fff;'>

<?php
	$arrayDate = array('1'=>'7','2'=>'14','3'=>'21','4'=>'28','5'=>'30','6'=>'60','7'=>'90','8'=>'120','9'=>'alltime');
	$arrayType = array('1'=>'alltickets','2'=>'email','3'=>'phone','4'=>'chat','5'=>'tweet','6'=>'qna');
	
	if(isset($timeFrame)){
	
		if (array_key_exists($timeFrame,$arrayDate)){ $date = $arrayDate[$timeFrame]; }
		else{ $date = "28";	}
	
	}
	else{ $date = "28"; }
				
	if(isset($ticketType)){
	
		if (array_key_exists($ticketType,$arrayType)){
			$type = $arrayType[$ticketType];
		}
		else{ $type = "alltickets"; }
	}
	else{ $type = "alltickets"; }
	
	if(isset($superTags)){
	
		if(!empty($superTags)){ $supertag = urldecode($superTags); }
		else{ $supertag = "allsupertags"; }
	
	}
	else{ $supertag = "allsupertags"; }
	
	if(isset($tagUsageLabel)){
		if (!empty($tagUsageLabel)){ $tags = urldecode($tagUsageLabel); }
		else{ $tags = "alltags"; }
	}
	else{ $tags = "alltags"; }	

	if($tags != "alltags"){ 
		$strTag = " AND ca.id = cl.case_id AND cl.label_name = '".$tags."'"; 
		$strTag2 = " ,janak.assistly_case_labels cl";
	}
	else{ $strTag =""; 	}
?>	

<pre>
SELECT cu.custom_user_id, CONCAT(u.first_name,' ',u.last_name) username, o.name officename, o.id office_id, COUNT(*) ticket_count, SUM(TIMESTAMPDIFF(MINUTE,first_opened_at,resolved_at)) handle_time_total
  FROM janak.assistly_cases ca, janak.assistly_customers cu, rentjuice.users u, rentjuice.offices o <?php if($tags !="alltags"){ ?> ,janak.assistly_case_labels cl <?php } ?> 
 WHERE ca.customer_id = cu.id
   AND u.id = cu.custom_user_id
   AND u.office_id = o.id
   <?php if($date !="alltime"){ ?>
AND DATE_ADD(ca.created_at, INTERVAL <?php echo $date; ?> DAY) >= SYSDATE()
<?php } ?>
<?php if($type !="alltickets"){ ?>
   AND ca.channel = '<?php echo $type; ?>'
<?php } ?>
<?php if($supertag !="allsupertags"){ ?>
   AND ca.super_tag = '<?php echo $supertag; ?>'
<?php } ?>
<?php if($tags !="alltags"){ ?>
   AND ca.id = cl.case_id 
   AND cl.label_name = '<?php echo $tags;  ?>'
<?php } ?>
 GROUP BY 1
 ORDER BY COUNT(*) DESC
 LIMIT 100
</pre>
	</div>
</div>
	
		</div>
</div>
<?php 

	$start = array('start_time'=>$start_time,'start_width'=>'56%');
	render('_footer',$start)
?>

