<?php 
	render('_header',array('title'=>$title)); 
	date_default_timezone_set('America/New_York');
?>
<div class="body-wrapper">
			
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#modeldetails').dataTable( {
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"bSort":false,
			"iDisplayLength": 100
		} );
		
		$('#modelrelationship').dataTable( {
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"bSort":false,
			"iDisplayLength": 100
		} );
		
	} );
</script>

<?php
	/*echo"<pre>";
		print_r($barcontent_relationship);
	echo"</pre>";*/
?>

<div class="align_center">
	<div class="manualmerges_title"> <?php echo $title; ?></div>	
</div>

<div style="width:70%; margin:0 auto;">
	<div style="text-align:center; font-weight:bold; margin:15px 0; padding-top:20px; font-size:14px; border-top:1px solid #CCCCCC;">Relationships for Account Model</div>
	<table cellpadding="0" cellspacing="0" border="0" class="display syncTable" id="modelrelationship" width="100%">
		<thead style="background-color:#f1f1f1">
			
			  <tr>
				<th><div class="bold center">Parent</div></th>
				<th><div class="bold center">Child</div></th>
				<th><div class="bold center">Relationship Name</div></th>
				<th><div class="bold center">Parent Field</div></th>
			  </tr>
			  
		</thead>
		<tbody>
		
			<?php
			$getValueRelationship = $barcontent_relationship;
			$countDataRelationship = count($getValueRelationship);
			if($countDataRelationship != 0){
				$i2 = 0;
				foreach($getValueRelationship as $valueRelationship){
				$i2 = $i2 + 1;
				 if($i2%2 == 0){ $class2 = 'even';  }
				 else{ $class2 = 'odd'; }
				?>
					<tr class="<?php echo $class2; ?>">
					
						<td style="border-left:1px solid #cccccc;">
							<?php
								if(isset($_GET['model_name'])){
									if( strtolower($valueRelationship->parent) != strtolower($_GET['model_name']) ){
								?>
									<a href="?page=salesforcefields&model_name=<?php echo $valueRelationship->parent;  ?>"><?php echo $valueRelationship->parent;  ?></a>
								<?php
									}
									else{ echo $valueRelationship->parent; }
								}
								else{ 
								?>
									<a href="?page=salesforcefields&model_name=<?php echo $valueRelationship->parent;  ?>"><?php echo $valueRelationship->parent;  ?></a>
								<?php
								}

							?>
						</td>
						<td style="border-left:1px solid #cccccc;">
														
							<?php
								if(isset($_GET['model_name'])){
									if( strtolower($valueRelationship->child) != strtolower($_GET['model_name']) ){
								?>
									<a href="?page=salesforcefields&model_name=<?php echo $valueRelationship->child;  ?>"><?php echo $valueRelationship->child;  ?></a>
								<?php
									}
									else{ echo $valueRelationship->child; }
								}
								else{  
								?>
									<a href="?page=salesforcefields&model_name=<?php echo $valueRelationship->child;  ?>"><?php echo $valueRelationship->child;  ?></a>
								<?php
								}

							?>
							
						</td>
						<td style="border-left:1px solid #cccccc;"><?php echo $valueRelationship->relationship_name;  ?></td>
						<td style="border-left:1px solid #cccccc;border-right:1px solid #cccccc;"><?php echo $valueRelationship->parent_field;  ?></td>
						
					</tr>
				<?php
				}
				$i2++;
			}
		?>
		
		</tbody>
	</table>
</div>
	<br />
<div style="width:90%; margin:0 auto;">	
	<br/>
	<div style="text-align:center; font-weight:bold; margin:15px 0; font-size:14px">Fields for the Account Model</div>
	<table cellpadding="0" cellspacing="0" border="0" class="display syncTable" id="modeldetails" width="100%">
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
	
</table>



<div class="align_right show_query">
	<a class='inline' href="#inline_content"><img src="assets/images/show_query.png" alt="show query" /></a>
</div>
<div style="display:none">
	<div id='inline_content' style='padding:10px; background:#fff;'>
<pre>

-----------------------------------------------
First Table
-----------------------------------------------

SELECT parent_object parent, child_object child, s.relationship_name, s.parent_field
  FROM salesforce_model_relationships s
 WHERE s.parent_object = "<?php echo $_GET["model_name"]; ?>"

UNION

SELECT parent_object parent, child_object child, s.relationship_name, s.parent_field
  FROM salesforce_model_relationships s
 WHERE s.child_object = "<?php echo $_GET["model_name"]; ?>"

-----------------------------------------------
Second Table
-----------------------------------------------

SELECT model_name, NAME, TYPE, LENGTH, label, calculated, calculated_formula, picklist_values, reference_to, relationship_name
  FROM salesforce_model_fields
 WHERE model_id = <?php echo $_GET["model_id"]; ?>
 ORDER BY Name
</pre>
	</div>
</div>	

</div> <!-- width 85% -->

</div><!-- body wrapper -->
<?php 
	$start = array('start_time'=>$start_time,'start_width'=>'wide');
	render('_footer',$start)
?>

