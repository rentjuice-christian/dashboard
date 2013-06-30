<?php 
	render('_header',array('title'=>'Salesforce Data Sync Status'));
	date_default_timezone_set('America/New_York');
?>


<div class="body-wrapper">
	<div class="centered">
		<div class="main_content_temp">


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

<?php
 if(!empty($error_message)){
	render('error',array('error_message'=>$error_message));
 }
 else{
?>

<div class="align_center">
	<div class="manualmerges_title">Salesforce Data Sync Status</div>	
</div>

	<table cellpadding="0" cellspacing="0" border="0" class="display syncTable" id="example" width="100%">
	<thead style="background-color:#f1f1f1">
		
		  <tr>
			<th><div class="bold center">Started On</div></th>
			<th><div class="bold center">Running Time</div></th>
			<th><div class="bold center">User</div></th>
			<th><div class="bold center">Account</div></th>
			<th><div class="bold center">Lead</div></th>
			<th><div class="bold center">Contact</div></th>
			<th><div class="bold center">Opportunity</div></th>
			<th><div class="bold center">Phone Call</div></th>
			<th><div class="bold center">Logfiles</div></th>
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
									//print_r($diff);
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
						<td class="center"  style="border-left:1px solid #cccccc;"><?php if($value->user_count != 0){ echo number_format($value->user_count); } ?></td>
						<td class="center"  style="border-left:1px solid #cccccc;"><?php if($value->account_count != 0){ echo number_format($value->account_count); } ?></td>
						<td class="center"  style="border-left:1px solid #cccccc;"><?php if($value->lead_count != 0){ echo number_format($value->lead_count); } ?></td>
						<td class="center"  style="border-left:1px solid #cccccc;"><?php if($value->contact_count != 0){ echo number_format($value->contact_count); } ?></td>
						<td class="center"  style="border-left:1px solid #cccccc;"><?php if($value->opportunity_count != 0){ echo number_format($value->opportunity_count); } ?></td>
						<td class="center"  style="border-left:1px solid #cccccc;">
							<?php if($value->fonalityuae_phonecall_count != 0){ echo number_format($value->fonalityuae_phonecall_count); } ?>
						</td>
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

<?php } ?>
		
<div class="align_right show_query">
	<a class='inline' href="#inline_content"><img src="assets/images/show_query.png" alt="show query" /></a>
</div>
<div style="display:none">
	<div id='inline_content' style='padding:10px; background:#fff;'>
<pre>
SELECT * FROM janak.salesforce_sync_logfiles
 ORDER BY started_at desc
</pre>
	</div>
</div>	


			</div><!-- main_content_temp -->	
		</div><!-- centered -->
</div><!-- body wrapper -->
<?php 
	$start = array('start_time'=>$start_time);
	render('_footer',$start)
?>
