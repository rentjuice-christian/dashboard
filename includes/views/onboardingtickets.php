<?php render('_header')?>
<?php

	$timeFrame = "";
	
	if(isset($_REQUEST['onboarding_tickets'])){  $timeFrame = $_REQUEST['onboarding_tickets']; }
	else{ $timeFrame = '28'; }
		
?>
<div class="body-wrapper">
		<div class="centered">
			<div class="main_content_temp">


<?php
	//echo"<pre>";
	//	print_r($barcontent);
		//print_r($newOptions);
	//echo"</pre>";
?>


<div>
	<div class="align_center">
		<div class="manualmerges_title">Onboarding Tickets by Status</div>
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
		<input type="hidden" value="onboardingtickets" name="page" />
		<span>Select Time Frame: </span>
		<select name="onboarding_tickets" onchange="submit();" class="select_time">
			<option value="7" <?php if($timeFrame == '7'){ echo'selected'; }else{ echo''; }  ?>>1 Week</option>
			<option value="14" <?php if($timeFrame == '14'){ echo'selected'; }else{ echo''; }  ?>>2 Weeks</option>
			<option value="21" <?php if($timeFrame == '21'){ echo'selected'; }else{ echo''; }  ?>>3 Weeks</option>
			<option value="28" <?php if($timeFrame == '28'){ echo'selected'; }else{ echo''; }  ?>>4 Weeks</option>
			<option value="alltime" <?php if($timeFrame == 'alltime'){ echo'selected'; }else{ echo''; }  ?>>All Time</option>
		</select>
	</div>
</form>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="example" width="100%">
	<thead>
		<tr>
			<th><div class="bold">Tag Name</div></th>
			<th><div class="bold">New</div></th>
			<th><div class="bold">Open</div></th>
			<th><div class="bold">Pending</div></th>
			<th><div class="bold">Resolved</div></th>
		</tr>
	</thead>
	<tbody>
	
	<?php
			$getValue = $barcontent;
			$countData = count($getValue);
			if($getValue != 0){
				$i = 0;
				foreach($getValue as $value){
				$i = $i + 1;
				 if($i%2 == 0){ $class = 'even';  }
				 else{ $class = 'odd'; }

				?>
					<tr class="<?php echo $class; ?>">
						<td style="border-left:1px solid #cccccc;"><?php echo $value->label_name; ?></td>
						<td class="center"  style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; "><?php if($value->new != 0){ echo $value->new; } ?></td>
						<td class="center"  style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; "><?php if($value->open != 0){ echo $value->open; } ?></td>
						<td class="center"  style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; "><?php if($value->pending != 0){ echo $value->pending; } ?></td>
						<td class="center"  style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; "><?php if($value->resolved != 0){ echo $value->resolved; } ?></td>
					</tr>
				<?php
				}
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
<pre>
<?php
	if($timeFrame !="alltime"){
?>
SELECT label_name, COUNT(*),
       SUM(IF(c.case_status_type = 'New',1,0)) NEW,
       SUM(IF(c.case_status_type = 'Open',1,0)) OPEN,
       SUM(IF(c.case_status_type = 'Pending',1,0)) pending,
       SUM(IF(c.case_status_type = 'Resolved',1,0)) resolved
  FROM janak.assistly_case_labels cl, janak.assistly_cases c
 WHERE cl.label_name IN ('Database', 'Data Import', 'Feeds', 'Forms', 'Merge', 'Offices', 'Onboarding', 'PM Onboarding', 'Training', 'Webinar')
   AND cl.case_id = c.id
   AND DATE_ADD(c.created_at, INTERVAL <?php echo $timeFrame ; ?> DAY) >= SYSDATE()
 GROUP BY 1
 ORDER BY 2 desc
<?php
	}
	else{
?>

SELECT label_name, COUNT(*),
       SUM(IF(c.case_status_type = 'New',1,0)) NEW,
       SUM(IF(c.case_status_type = 'Open',1,0)) OPEN,
       SUM(IF(c.case_status_type = 'Pending',1,0)) pending,
       SUM(IF(c.case_status_type = 'Resolved',1,0)) resolved
  FROM janak.assistly_case_labels cl, janak.assistly_cases c
 WHERE cl.label_name IN ('Database', 'Data Import', 'Feeds', 'Forms', 'Merge', 'Offices', 'Onboarding', 'PM Onboarding', 'Training', 'Webinar')
   AND cl.case_id = c.id
 GROUP BY 1
 ORDER BY 2 desc

<?php
	}
?>

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

