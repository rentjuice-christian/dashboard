<?php render('_header',array('title'=>'No Last Names Report'));  ?>

<div class="body-wrapper">
	<div class="centered">
		<div class="main_content_temp">

<?php
 if(!empty($error_message)){
	render('error',array('error_message'=>$error_message));
 }
 else{
?>

	<div class="align_center">
		<div class="manualmerges_title">No Last Names Report</div>
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
			<th><div class="bold">User ID</div></th>
			<th><div class="bold">First Name</div></th>
			<th><div class="bold">Last Name</div></th>
			<th><div class="bold">User Type</div></th>
			<th><div class="bold">Date Joined</div></th>
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
						<td style="border-left:1px solid #cccccc;" class="align_right">
							<a href="http://radmin.rentalapp.zillow.com/user:<?php echo $value->user_id; ?>" target="_blank">
								<?php echo $value->user_id; ?>
							</a>
						</td>
						<td style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; "><?php echo $value->first_name; ?></td>
						<td style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; "><?php echo $value->last_name; ?></td>
						<td style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; "><?php echo $value->user_type; ?></td>
						<td style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; "><?php echo $value->date_joined; ?></td>
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
SELECT id user_id, first_name, last_name, TYPE user_type, date_joined
  FROM rentjuice.users u
 WHERE last_name = ""
    OR last_name IS NULL
 ORDER BY id desc
</pre>
	</div>
</div>



		</div>		
	</div>
</div>
<?php 
	$start = array('start_time'=>$start_time);
	render('_footer',$start)
?>

