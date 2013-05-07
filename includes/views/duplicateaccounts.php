<?php render('_header')?>

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
		<div class="manualmerges_title">Duplicate Accounts Report</div>
	
	</div>
	<script type="text/javascript" charset="utf-8">
		$(document).ready(function() {
			$('#duplicate_accounts').dataTable( {
				"bJQueryUI": true,
				"sPaginationType": "full_numbers",
				"bSort":false,
				"iDisplayLength": 100
			} );
		} );
	</script>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="duplicate_accounts" width="100%">
	<thead>
		<tr>
			<th><div class="bold">Customer ID</div></th>
			<th><div class="bold">Customer Name</div></th>
			<th><div class="bold">Salesforce ID</div></th>
			<th><div class="bold">Created On</div></th>
			<th><div class="bold">Status</div></th>
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

				// new row for grouping
				if( $brand!=$value->sforce_acct_id ){

					if( $brand!=false ){
				?>
					<tr>
						<td style="border-left:1px solid #cccccc;border-right:1px solid #cccccc;">&nbsp;</td>
						<td style="border-left:1px solid #cccccc;border-right:1px solid #cccccc;">&nbsp;</td>
						<td style="border-left:1px solid #cccccc;border-right:1px solid #cccccc;">&nbsp;</td>
						<td style="border-left:1px solid #cccccc;border-right:1px solid #cccccc;">&nbsp;</td>
						<td style="border-left:1px solid #cccccc;border-right:1px solid #cccccc;">&nbsp;</td>
					</tr>
				<?php	 
					}

					$brand = $value->sforce_acct_id;
				  }					
				?>
				
					<tr class="<?php echo $class; ?>">
						<td style="border-left:1px solid #cccccc;" class="align_right">
							<a href="http://radmin.rentalapp.zillow.com/customer:<?php echo $value->customer_id; ?>" target="_blank"><?php echo $value->customer_id; ?></a>
						</td>
						<td  style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; "><?php echo $value->name; ?></td>
						<td   style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; ">
							<a href="https://na10.salesforce.com/<?php echo $value->sforce_acct_id; ?>" target="_blank">
								<?php echo $value->sforce_acct_id; ?>
							</a>
						</td>
						<td   style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; "><?php echo $value->created_on; ?></td>
						<td   style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; "><?php echo $value->status; ?></td>
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
SELECT c1.id customer_id, c1.name, c1.sforce_acct_id, c1.created_on, c1.status, c1.*
  FROM rentjuice.customers c1,
       (
        SELECT CAST(sforce_acct_id AS BINARY) sforce_acct_id, COUNT(*)
          FROM rentjuice.customers
         WHERE sforce_acct_id IS NOT NULL
         GROUP BY 1
        HAVING COUNT(*) > 1
         ORDER BY COUNT(*) DESC
       ) c2
 WHERE c1.sforce_acct_id = c2.sforce_acct_id
 ORDER BY c1.sforce_acct_id
 
 
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

