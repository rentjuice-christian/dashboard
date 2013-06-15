<?php render('_header',array('title'=>$title))?>
<?php

	$timeFrame = "";

	if(isset($_REQUEST['leaderboard_time']) && !empty($_REQUEST['leaderboard_time'])){ 
		if($_REQUEST['leaderboard_time'] == 'alltime'){ $timeFrame = "12";  }
		else{ $timeFrame = $_REQUEST['leaderboard_time'];  }
	}
	else{ $timeFrame = '28'; }
		
?>
<div class="body-wrapper">
		<div class="centered">
			<div class="main_content_temp">


<?php
	/*echo"<pre>";
		print_r($barcontent);
		//print_r($newOptions);
	echo"</pre>";*/
?>


<div>
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
		<input type="hidden" value="supertagusageleader" name="page" />
		<span>Select Time Frame: </span>
		<select name="leaderboard_time" onchange="submit();" class="select_time">
			<option value="7" <?php if($timeFrame == '7'){ echo'selected'; }else{ echo''; }  ?>>1 Week</option>
			<option value="14" <?php if($timeFrame == '14'){ echo'selected'; }else{ echo''; }  ?>>2 Weeks</option>
			<option value="21" <?php if($timeFrame == '21'){ echo'selected'; }else{ echo''; }  ?>>3 Weeks</option>
			<option value="28" <?php if($timeFrame == '28'){ echo'selected'; }else{ echo''; }  ?>>4 Weeks</option>
			<option value="alltime" <?php if($timeFrame == '12'){ echo'selected'; }else{ echo''; }  ?>>All Time</option>
		</select>
	</div>
</form>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="example" width="100%">
	<thead>
		<tr>
			<th><div class="bold">Super Tag Name</div></th>
			<th><div class="bold">Count</div></th>
		</tr>
	</thead>
	<tbody>
	
	<?php
			$getValue = $barcontent;
			$countData = count($getValue);
			if($countData != 0){
				$i = 0;
				foreach($getValue as $value){
				if(!empty($value->super_tag)){
					$i = $i + 1;
					 if($i%2 == 0){ $class = 'even';  }
					 else{ $class = 'odd'; }
	
					?>
						<tr class="<?php echo $class; ?>">
							<td style="border-left:1px solid #cccccc;">
								<a href="index.php?page=tagusageovertime&tagusage_label=<?php echo urlencode($value->super_tag); ?>&tagusage_time=<?php echo $timeFrame; ?>">
									<?php echo $value->super_tag; ?>
								</a>
							</td>
							<td class="center"  style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; "><?php echo $value->count_tag; ?></td>
						</tr>
					<?php
					}
					$i++;
				}
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

if($timeFrame == "alltime"){
?>
SELECT c.super_tag, COUNT(*)
  FROM janak.assistly_cases c
 GROUP BY 1
 ORDER BY 2 DESC
<?php
}
else{
?>
SELECT c.super_tag, COUNT(*)
  FROM janak.assistly_cases c
 WHERE DATE_ADD(created_at, INTERVAL <?php echo $timeFrame; ?> DAY) >= SYSDATE()
 GROUP BY 1
 ORDER BY 2 DESC
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

