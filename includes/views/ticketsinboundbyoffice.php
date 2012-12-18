<?php render('_header')?>
<?php
	$getValue = $barcontent;
	$countData = count($getValue);
	$countTicket = 0;
	$timeFrame = "";
	$timeValue = array('1'=>'In the last week',
					   '2'=>'In the last two weeks',
					   '3'=>'In the last three weeks',
					   '4'=>'In the last four weeks',
					   '5'=>'In the last 2 months',
					   '6'=>'In the last 3 months',
					   '7'=>'In the last 4 months',
					   '8'=>'Since the dawn of the third age of mankind...'
					   );
	
	if(isset($_REQUEST['ticketsinboundbyoffice_time'])){
		$timeFrame = $_REQUEST['ticketsinboundbyoffice_time'];
	}
	else{
		$timeFrame = '4';
	}
?>
<div class="body-wrapper">
		<div class="centered">
			<div class="main_content_temp">
<?php
	/*echo"<pre>";
		echo"<div>Results for Query 1</div>";
		print_r($sub_barcontent);
		echo"<div>Results for Query 2</div>";
		print_r($sub_barcontent2);
		//print_r($barcontent);
		//print_r($newOptions);
	echo"</pre>";*/
?>

<div>
	<div class="align_center">
		<div class="manualmerges_title">Tickets Inbound for Top 100 Offices</div>
	</div>
	<div style="margin-top:10px; padding:5px 0; border-top:1px solid #CCCCCC;">
		<div class="align_left left">
			<div class="bold" style="font-size:16px;">
				<?php
					foreach($timeValue as $key=>$value){ if($timeFrame == $key){	echo $value; break;	} }	
				?>
			</div>
			<div>
				The top <b><?php echo  $sub_barcontent[0]->office_count; ?> </b> offices accounted for 
				<b><?php echo  floor($sub_barcontent[0]->ticket_count / ($sub_barcontent[0]->ticket_count + $sub_barcontent2[0]->ticket_count) * 100); ?>% </b>
				of our tickets
			</div>
			<div>
				 The other <b><?php  echo  $sub_barcontent2[0]->office_count; ?> </b>
				 offices accounted for <b><?php echo  floor($sub_barcontent2[0]->ticket_count / ($sub_barcontent[0]->ticket_count + $sub_barcontent2[0]->ticket_count) * 100); ?>% </b>
				 of our tickets
			</div>
		</div>
		<div class="right" style="padding-top:25px;">
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="form_select" id="form_submit" >
				<div class="align_right">
					<input type="hidden" value="ticketsinboundbyoffice" name="page" />
					<span>Select Time Frame: </span>
					<select name="ticketsinboundbyoffice_time" onchange="submit();" class="select_time">
						<option value="1" <?php if($timeFrame == '1'){ echo'selected'; }else{ echo''; }  ?>>1 Week</option>
						<option value="2" <?php if($timeFrame == '2'){ echo'selected'; }else{ echo''; }  ?>>2 Weeks</option>
						<option value="3" <?php if($timeFrame == '3'){ echo'selected'; }else{ echo''; }  ?>>3 Weeks</option>
						<option value="4" <?php if($timeFrame == '4'){ echo'selected'; }else{ echo''; }  ?>>4 Weeks</option>
						<option value="5" <?php if($timeFrame == '5'){ echo'selected'; }else{ echo''; }  ?>>2 Months</option>
						<option value="6" <?php if($timeFrame == '6'){ echo'selected'; }else{ echo''; }  ?>>3 Months</option>
						<option value="7" <?php if($timeFrame == '7'){ echo'selected'; }else{ echo''; }  ?>>4 Months</option>
						<option value="8" <?php if($timeFrame == 'alltime' || $timeFrame == '8' ){ echo'selected'; }else{ echo''; }  ?>>All Time</option>
					</select>
				</div>
			</form>
		</div>
		<div class="clear"></div>
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
<table cellpadding="0" cellspacing="0" border="0" class="display" id="example" width="100%">
	<thead>
		<tr>
			<th><div class="bold">Office Name</div></th>
			<th><div class="bold">Ticket Count</div></th>
		</tr>
	</thead>
	<tbody>
	<?php
	if($getValue != 0){
		$i = 0;
		foreach($getValue as $value){
		$i = $i + 1;
		 if($i%2 == 0){ $class = 'even';  }
		 else{ $class = 'odd'; }
		?>
			<td class="center"  style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; ">
					<a href="http://radmin.rentjuice.com/customer:<?php echo urlencode($value->office_id); ?>" target="_blank">
						<?php echo $value->officename; ?>
					</a>
				</td>
				<td class="center"  style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; ">
					<a href="index.php?page=ticketsearchforoffices&officename=<?php echo urlencode($value->officename); ?>&ticketsearch_time=<?php echo $timeFrame; ?>">
						<?php echo $value->ticket_count; ?>
					</a>
				</td>
			</tr>
		<?php
		} // end fore each
		$i++;
	}
	?>
	</tbody>
	<tfoot>
</table>

<div class="align_right show_query">
	<a class='inline' href="#inline_content"><img src="assets/images/show_query.png" alt="show query" /></a>
</div>
<div style="display:none">
	<div id='inline_content' style='padding:10px; background:#fff;'>
<?php
	$arrayDate = array('1'=>'1 WEEK','2'=>'2 WEEK','3'=>'3 WEEK','4'=>'4 WEEK','5'=>'2 MONTH','6'=>'3 MONTH','7'=>'4 MONTH','8'=>'alltime');
				
	if(!empty($timeFrame)){
	
		if (array_key_exists($timeFrame,$arrayDate)){ $date = $arrayDate[$timeFrame]; }
		else{ $date = "4 WEEK";	}
	
	}
	else{ $date = "4 WEEK"; }
?>	
<pre>
SELECT o.id office_id, o.name, COUNT(*)
  FROM janak.assistly_cases ca, janak.assistly_customers cu, rentjuice.users u, rentjuice.offices o
 WHERE ca.customer_id = cu.id
   AND u.id = cu.custom_user_id
   AND u.office_id = o.id
   <?php if($date !="alltime"){
   ?>
   AND DATE_ADD(ca.created_at, INTERVAL <?php echo $date;  ?>) >= SYSDATE()
   <?php
   } ?>
 GROUP BY 1
 ORDER BY COUNT(*) DESC
LIMIT 100
</pre>
	</div>
</div>

		
</div><!-- manual merges holder -->

			</div>		
		</div>
</div>
<?php 
	$start = array('start_time'=>$start_time);
	render('_footer',$start)
?>

