<?php 
	render('_header');
	date_default_timezone_set('America/New_York');
?>


<div class="body-wrapper">
		<div style="width:87%; margin:0 auto;">
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
	<div class="manualmerges_title"> Salesforce Fields </div>	
</div>



	
	<table cellpadding="0" cellspacing="0" border="0" class="display syncTable" id="example" width="100%">
	<thead style="background-color:#f1f1f1">
		
		  <tr>
			<th><div class="bold center">Model Name</div></th>
			<th><div class="bold center">Name</div></th>
			<th><div class="bold center">Type</div></th>
			<th><div class="bold center">Length</div></th>
			<th><div class="bold center">Label</div></th>
			<th><div class="bold center">Calculated</div></th>
			<th><div class="bold center">Calculated Formula</div></th>
			<th><div class="bold center">Picklist Values</div></th>
			<th><div class="bold center">Reference To</div></th>
			<th><div class="bold center">Relationship Name</div></th>
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
					



						<td class="center"  style="border-left:1px solid #cccccc;"><?php echo $value->model_name;  ?></td>
						<td class="align_left"  style="border-left:1px solid #cccccc;"><?php echo $value->NAME;  ?></td>
						<td class="center"  style="border-left:1px solid #cccccc;"><?php echo $value->TYPE;  ?></td>
						<td class="align_right"  style="border-left:1px solid #cccccc;"><?php echo $value->LENGTH;  ?></td>
						<td class="align_left"  style="border-left:1px solid #cccccc;"><?php echo $value->label;  ?></td>
						<td class="align_right"  style="border-left:1px solid #cccccc;"><?php echo $value->calculated;  ?></td>
						<td class="center"  style="border-left:1px solid #cccccc;"> 
							
							<?php
						
							if (!empty($value->calculated_formula)) {
    
								if (strpos($value->calculated_formula, '--- []') === false) { 

							?>
								<a class='inline_notes' href="#inline_calculated_formula_<?php echo $value->id;  ?>">view</a>
								<div style="display:none"> 
									<div id="inline_calculated_formula_<?php echo $value->id;  ?>"><pre><?php echo $value->calculated_formula; ?></pre></div>
								</div>
								
							<?php
								}
							}
							?>
						</td>
						<td class="center"  style="border-left:1px solid #cccccc;">
							
							<?php
						
							if (!empty($value->picklist_values)) {
    
								if (strpos($value->picklist_values, '--- []') === false) { 

							?>
								<a class='inline_notes' href="#inline_picklist_values_<?php echo $value->id;  ?>">view</a>
								<div style="display:none"> 
									<div id="inline_picklist_values_<?php echo $value->id;  ?>"><pre><?php echo $value->picklist_values; ?></pre></div>
								</div>
								
							<?php
								}
							}
							?>
						</td>
						<td class="center"  style="border-left:1px solid #cccccc;">
						
							
							<?php
						
							if (!empty($value->reference_to)) {
    
								if (strpos($value->reference_to, '--- []') === false) { 

							?>
								<a class='inline_notes' href="#inline_reference_to_<?php echo $value->id;  ?>">view</a>
								<div style="display:none"> 
									<div id="inline_reference_to_<?php echo $value->id;  ?>"><pre><?php echo $value->reference_to; ?></pre></div>
								</div>
								
							<?php
								}
							}
							?>
							
						</td>
						<td class="center"  style="border-left:1px solid #cccccc;border-right:1px solid #cccccc;">
						
						<?php
						
						if (!empty($value->relationship_name)) {

							if (strpos($value->relationship_name, '--- []') === false) { 

						?>
							<a class='inline_notes' href="#inline_relationship_name_<?php echo $value->id;  ?>">view</a>
							<div style="display:none"> 
								<div id="inline_relationship_name_<?php echo $value->id;  ?>"><pre><?php echo $value->relationship_name; ?></pre></div>
							</div>
							
						<?php
							}
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
SELECT model_name, NAME, TYPE, LENGTH, label, calculated, calculated_formula, picklist_values, reference_to, relationship_name
  FROM salesforce_model_fields
 WHERE model_id = <?php echo $_GET["model_id"]; ?>
 ORDER BY Name

</pre>
	</div>
</div>	


			</div><!-- main_content_temp -->	
		</div><!-- centered -->
</div><!-- body wrapper -->
<?php render('_footer')?>

