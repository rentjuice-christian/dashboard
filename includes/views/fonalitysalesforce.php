<?php render('_header',array('title'=>$title));  ?>

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

<table cellpadding="0" cellspacing="0" border="0" class="display" id="example" width="100%">
	<thead>
		<tr>
			<th><div class="bold">Connection Type</div></th>
			<th><div class="bold">Phone Call Count</div></th>
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
							<?php echo $value->connection_type; ?>
						</td>
						<td class="center"  style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; "><?php echo $value->COUNT; ?></td>
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
SELECT CONCAT_WS(",",IF(LENGTH(a.id),'Account',NULL),
                     IF(LENGTH(l.id),"Lead",NULL),
                     IF(LENGTH(c.id),"Contact",NULL),
                     IF(LENGTH(o.id),"Opportunity",NULL)) connection_type, COUNT(*) COUNT

  FROM janak.salesforce_users u, janak.salesforce_fonality_phonecalls f
       LEFT OUTER JOIN janak.salesforce_accounts a
       ON f.fonality_uae_account_custom = a.id
       LEFT OUTER JOIN janak.salesforce_contacts c
       ON f.fonality_uae_customontact_custom = c.id
       LEFT OUTER JOIN janak.salesforce_leads l
       ON f.fonality_uae_lead_custom = l.id      
       LEFT OUTER JOIN janak.salesforce_opportunities o
       ON f.fonality_uae_opportunity_custom = o.id             
 WHERE u.id = f.owner_id
 GROUP BY 1
 ORDER BY 2 DESC
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

