<?php render('_header')?>
<?php

	$timeFrame = "";
	
	if(isset($_REQUEST['manualmerges_time'])){
		$timeFrame = $_REQUEST['manualmerges_time'];
	}
	else{
		$timeFrame = '2';
	}
		
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


<div><!-- class="manualmerges_holder"-->
	<div class="align_center">
		<div class="manualmerges_title">Manual Merges</div>
		
		
		<?php
			$getValue = $barcontent;
			$totalThreads = 0;
			$manualMerges = 0;
			$sumMerges = 0;
			if($getValue != 0){
				foreach($getValue as $value){
					$totalThreads += $value->threadcount;
					$manualMerges += $value->summerge;
				}
			}
			$sumMerges = $manualMerges / $totalThreads;  
		?>
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

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="form_select"  id="form_submit">
	<div class="align_right">
		<span>Select Time Frame: </span>
		<input type="hidden" value="manualmerges" name="page" />
		<select name="manualmerges_time" onchange="submit();" class="select_time">
			<option value="1" <?php if($timeFrame == '1'){ echo'selected'; }else{ echo''; }  ?>>1 Week</option>
			<option value="2" <?php if($timeFrame == '2'){ echo'selected'; }else{ echo''; }  ?>>2 Weeks</option>
			<option value="3" <?php if($timeFrame == '3'){ echo'selected'; }else{ echo''; }  ?>>3 Weeks</option>
			<option value="4" <?php if($timeFrame == '4'){ echo'selected'; }else{ echo''; }  ?>>4 Weeks</option>
			<!--<option value="alltime" <?php //if($timeFrame == 'alltime'){ echo'selected'; }else{ echo''; }  ?>>All Time</option>-->
		</select>
	</div>
</form>

<table width="100%" border="0" cellpadding="5" cellspacing="0"  style="border-top:1px solid #e0e0e0;border-left:1px solid #e0e0e0;border-right:1px solid #e0e0e0; margin-top:5px;">
 <tbody>
  <tr>
    <td class="bold">
		<div style="padding-left:5px">Total Threads</div>
		<div class="font24" style="padding-left:5px"><?php echo $totalThreads; ?></div>
	</td>
    <td class="bold" style="border-left:1px solid #cccccc; border-right:1px solid #cccccc;"> 
		<div style="padding-left:5px">Total Merges</div>
		<div class="font24" style="padding-left:5px"><?php echo $manualMerges; ?></div>
	</td>
    <td class="bold">
		<div style="padding-left:5px">Total Merges / Total Threads</div>
		<div class="font24" style="padding-left:5px"><?php echo round($sumMerges, 2) * 100; ?>%</div>
	</td>
  </tr>
  </tbody>
</table>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="example" width="100%">
	<thead>
		<tr>
			<th><div class="bold">Office Name</div></th>
			<th><div class="bold">Thread Count</div></th>
			<th><div class="bold">Manual Merges</div></th>
		</tr>
	</thead>
	<tbody>
	
	<?php
			//$getValue = $barcontent;
			$countData = count($getValue);
			if($getValue != 0){
				$i = 0;
				foreach($getValue as $value){
				$i = $i + 1;
				 if($i%2 == 0){ $class = 'even';  }
				 else{ $class = 'odd'; }

				?>
					<tr class="<?php echo $class; ?>">
						<td style="border-left:1px solid #e0e0e0; color:#555555;"><a href="http://radmin.rentjuice.com/office:<?php echo $value->office_id; ?>/data_entry" target="_blank"><?php echo $value->officename; ?></a></td>
						<td class="center bold"  style="border-left:1px solid #e0e0e0;border-right:1px solid #e0e0e0;color:#555555; "><?php echo $value->threadcount; ?></td>
						<td class="center bold"  style="border-right:1px solid #e0e0e0;color:#555555;"><?php echo $value->summerge; ?></td>
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
SELECT COUNT(thread_id), SUM(had_manual_merge), NAME, office_id
  FROM (
        SELECT t.id AS thread_id, IF(a.id, 1, 0) AS had_manual_merge, o.name AS NAME, o.id office_id
          FROM dataentry.emails e
               INNER JOIN dataentry.threads t ON t.id = e.thread_id
                 AND e.content_office_id > 0
                 AND DATE_ADD(t.created_on, INTERVAL  <?php echo $timeFrame; ?> WEEK) > SYSDATE()
               LEFT OUTER JOIN dataentry.activities a ON a.thread_id = t.id
                 AND a.action LIKE '%merged into this thread'
               INNER JOIN rentjuice.offices o ON t.office_id = o.id
        ) AS TMP
 GROUP BY 3
HAVING SUM(had_manual_merge) > 0
 ORDER BY 2 desc    
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


