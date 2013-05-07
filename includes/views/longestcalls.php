<?php render('_header')?>
<?php

	if(isset($_REQUEST['timespan'])){$timeFrame = $_REQUEST['timespan'];}
	else{$timeFrame = '5';}
		
?>
<div class="body-wrapper">
		<div style="margin:0 auto; width:50%;">
			

<?php
	/*echo"<pre>";
		print_r($barcontent);
		//print_r($newOptions);
	echo"</pre>";*/
?>
<div class="align_center">
	<div class="manualmerges_title">Longest Calls</div>
</div>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#longestcalls').dataTable( {
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"bSort":false,
			"iDisplayLength": 100
		} );
	} );
</script>
<?php //echo memory_get_usage(); ?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="form_select" id="form_submit" >
	
	<div class="align_right">
		<input type="hidden" value="longestcalls" name="page" />

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

<table cellpadding="0" cellspacing="0" border="0" class="display" id="longestcalls" >
	<thead>
		<tr>
			<th><div class="bold">Zillow Person</div></th>
			<th><div class="bold">Call Duration</div></th>
			<th><div class="bold">Phone Number</div></th>
			<th><div class="bold">Salesforce</div></th>
		</tr>
	</thead>
	<tbody>
	
	<?php
			$getValue = $barcontent;
			$countData = count($getValue);
			if($countData != 0){
				$i = 0;
				foreach($getValue as $value){
				$i = $i + 1;
				 if($i%2 == 0){ $class = 'even';  }
				 else{ $class = 'odd'; }

				?>
					<tr class="<?php echo $class; ?>">
						<td style="border-left:1px solid #cccccc;">
								<?php echo $value->name ; ?>
						</td>
						<td class="align_right"  style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; ">
							<?php echo $value->fonality_uae_duration_custom; ?>
						</td>
						<td class="align_right"  style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; ">
							<?php 
								$num=$value->fonality_uae_phone_number_custom;
								//$formatted = "(".substr($num,0,3).") ".substr($num,3,3)."-".substr($num,6);
								//echo "<div>".$value->fonality_uae_phone_number_custom."</div>"; 
								if(strlen($num) == 10){
									echo "<div>".substr($num,-10,-7)."-".substr($num,-7,-4)."-".substr($num,-4)."</div>";
								}
								if(strlen($num) == 11){
									echo "<div>".substr($num,0,1)."-".substr($num,-10,-7)."-".substr($num,-7,-4)."-".substr($num,-4)."</div>";
								}
								
							?>
							
						</td>
						<td  style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; ">
							<div><a href="https://na10.salesforce.com/<?php echo $value-> account_id; ?>" target="_blank"><?php echo $value-> account_name; ?></a></div>
							<div><a href="https://na10.salesforce.com/<?php echo $value-> contact_id; ?>" target="_blank"><?php echo $value-> contact_name; ?></a></div>
							<div><a href="https://na10.salesforce.com/<?php echo $value-> lead_id; ?>" target="_blank"><?php echo $value-> lead_name; ?></a></div>
							<div><a href="https://na10.salesforce.com/<?php echo $value-> opportunity_id; ?>" target="_blank"><?php echo $value-> opportunity_name; ?></a></div>
						</td>
					</tr>
				<?php
				}
				$i++;
			}
		?>

	</tbody>
	<tfoot>
</table>

<div class="align_right show_query"> <a class='inline' href="#inline_content"><img src="assets/images/show_query.png" alt="show query" /></a></div>
<div style="display:none">
	<div id='inline_content' style='padding:10px; background:#fff;'>
<pre>
<?php
$arrayDate = array('1'=>'7','2'=>'14','3'=>'21','4'=>'28','5'=>'30','6'=>'60','7'=>'90','8'=>'120','9'=>'150','10'=>'180','11'=>'360','12'=>'720');
if (array_key_exists($timeFrame,$arrayDate)){
	$timespan = $arrayDate[$timeFrame];
}
else{ $timespan= "30"; }
?>
SELECT u.name,
       fonality_uae_phone_number_custom,
       fonality_uae_duration_custom,
       a.name account_name,
       c.name contact_name,
       l.name lead_name,
       o.name opportunity_name,
       a.id account_id,
       c.id contact_id,
       l.id lead_id,
       o.id opportunity_id
  FROM janak.salesforce_users u, janak.salesforce_fonality_phonecalls f
       LEFT OUTER JOIN janak.salesforce_accounts a
       ON f.fonality_uae_account_custom = a.id
       LEFT OUTER JOIN janak.salesforce_contacts c
       ON f.fonality_uae_customontact_custom = c.id
       LEFT OUTER JOIN janak.salesforce_leads l
       ON f.fonality_uae_lead_custom = l.id       
       LEFT OUTER JOIN janak.salesforce_opportunities o
       ON f.fonality_uae_opportunity_custom = o.id              
 WHERE DATE_ADD(f.created_date, INTERVAL <?php echo $timespan; ?> DAY) > SYSDATE()
   AND u.id = f.owner_id
 ORDER BY fonality_uae_duration_custom DESC
 LIMIT 1000

</pre>
	</div>
</div>
	
		</div>
</div>
<?php 
	$start = array('start_time'=>$start_time,'start_width'=>'50%');
	render('_footer',$start)
?>

