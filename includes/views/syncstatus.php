<?php 
	render('_header',array('title'=>$title));
	date_default_timezone_set('America/New_York');
?>


<div class="body-wrapper">
		<div style="width:85%; margin:0 auto;">
			<div class="main_content_temp">

<?php
	/*echo"<pre>";
		print_r($barcontent);
	echo"</pre>";*/
?>

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


<div class="align_center">
	<div class="manualmerges_title">  <?php echo $title; ?></div>	
</div>



	
	<table cellpadding="0" cellspacing="0" border="0" class="display syncTable" id="example" width="100%">
	<thead style="background-color:#f1f1f1">
		
		  <tr>
			<th rowspan="2" width="170px"><div class="bold center">Started On</div></th>
			<th rowspan="2" width="170px"><div class="bold center">Running Time</div></th>
			<th rowspan="2"><div class="bold center">Groups</div></th>
			<th rowspan="2"><div class="bold center">Users</div></th>
			<th rowspan="2"><div class="bold center">Topics</div></th>
			<th rowspan="2"><div class="bold center">Macros</div></th>
			<th rowspan="2"><div class="bold center">Articles</div></th>
			<th rowspan="2"><div class="bold center">Cases</div></th>
			<th rowspan="2"><div class="bold center">Customers</div></th>
			<th colspan="4"><div class="bold center">Interactions</div></th>
			<th rowspan="2"><div class="bold center">Logfile</div></th>
		  </tr>
		  <tr>
			<th><div class="bold center">Main</div></th>
			<th><div class="bold center">Emails</div></th>
			<th><div class="bold center">Qnas</div></th>
			<th><div class="bold center">Phone</div></th>
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
				 
				 if(empty($value->finished_at)){
				 	$cellColor ="#f15555";
				 }else { $cellColor ="#a6cc32"; } 

				?>
					<tr class="<?php echo $class; ?>">
						<td style="border-left:1px solid #cccccc;">
							<?php 
								$currentDateTime = $value->started_at;
								$newDateTime = date('g:i A', strtotime($currentDateTime));	
								$newDateTime2 = date('m/d', strtotime($currentDateTime));
								echo $newDateTime2." ".$newDateTime;
							?>
						</td>
						<td class="center"  style="border-left:1px solid #cccccc;" bgcolor="<?php echo $cellColor; ?>">
							<?php 
								//echo $value->finished_at; 
								if(!empty($value->finished_at)){
									$now = new DateTime($value->started_at);
									$ref = new DateTime($value->finished_at);
									$diff = $now->diff($ref);
									//printf('%d days, %d hours, %d minutes', $diff->d, $diff->h, $diff->i);
									if($diff->d == 0 && $diff->h == 0 && $diff->i == 0 && $diff->s == 0){
										echo"N/A";
									}
									else{
										if($diff->d != 0){
											echo $diff->d;
											if($diff->d == 1){ echo" day ";}else{ echo" days "; }
										}
										if($diff->h != 0){
											echo $diff->h;
											if($diff->h == 1){ echo" hour ";}else{ echo" hours "; }
										}
										if($diff->i != 0){
											echo $diff->i;
											if($diff->i == 1){ echo" minute ";}else{ echo" minutes "; }
										}
										if($diff->i == 0 && $diff->s != 0){
											echo $diff->s;
											if($diff->s == 1){ echo" second ";}else{ echo" seconds "; }
										}
									}
								}
								else{
									echo"N/A";
								}
							?>
						</td>
						
						
						<td class="center"  style="border-left:1px solid #cccccc;"><?php if($value->groups_count != 0){ echo $value->groups_count; } ?></td>
						<td class="center"  style="border-left:1px solid #cccccc;"><?php if($value->users_count != 0){ echo $value->users_count; } ?></td>
						<td class="center"  style="border-left:1px solid #cccccc;"><?php if($value->topics_count != 0){ echo $value->topics_count; } ?></td>
						<td class="center"  style="border-left:1px solid #cccccc;"><?php if($value->macros_count != 0){ echo $value->macros_count; } ?></td>
						<td class="center"  style="border-left:1px solid #cccccc;"><?php if($value->articles_count != 0){ echo $value->articles_count; } ?></td>
						
						<td class="center"  style="border-left:1px solid #cccccc;"><?php if($value->cases_count != 0){ echo number_format($value->cases_count); } ?></td>
						<td class="center"  style="border-left:1px solid #cccccc;"><?php if($value->customers_count != 0){ echo number_format($value->customers_count); } ?></td>

						<td class="center"  style="border-left:1px solid #cccccc;"><?php if($value->interactions_count != 0){ echo number_format($value->interactions_count); } ?></td>
						<td class="center"  style="border-left:1px solid #cccccc;"><?php if($value->interaction_emails_count != 0){ echo number_format($value->interaction_emails_count); } ?></td>
						<td class="center"  style="border-left:1px solid #cccccc;"><?php if($value->interaction_qnas_count != 0){ echo number_format($value->interaction_qnas_count); } ?></td>
						<td class="center"  style="border-left:1px solid #cccccc;"><?php if($value->interaction_phonecalls_count != 0){ echo number_format($value->interaction_phonecalls_count); } ?></td>
						
						<td class="center"  style="border-left:1px solid #cccccc; border-right:1px solid #cccccc;font-size:11px;">
							<?php
							if(!empty($value->notes)){
							?>
								<a id="click_inline_notes" class='inline_notes' href="#inline_notes_<?php echo $value->id;  ?>">view</a>
								<div style="display:none"> 
									<div id="inline_notes_<?php echo $value->id;  ?>"><pre><?php echo $value->notes; ?></pre></div>
								</div>
								
							<?php
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
	<tfoot>
</table>
		
<div class="align_right show_query">
	<a class='inline' href="#inline_content"><img src="assets/images/show_query.png" alt="show query" /></a>
</div>
<div style="display:none">
	<div id='inline_content' style='padding:10px; background:#fff;'>
<pre>
SELECT * FROM janak.assistly_sync_logfiles
 ORDER BY started_at desc
</pre>
	</div>
</div>	


			</div><!-- main_content_temp -->	
		</div><!-- centered -->
</div><!-- body wrapper -->
<?php 
	$start = array('start_time'=>$start_time,'start_width'=>'wide');
	render('_footer',$start)
?>
