<?php 
	render('_header',array('title'=>$title));
	date_default_timezone_set('America/New_York');
?>


<div class="body-wrapper">
		<div style="width:80%; margin:0 auto;">
			<div class="main_content_temp">

<?php
	/*echo"<pre>";
		print_r($barcontent);
	echo"</pre>";*/
?>

<script type="text/javascript" charset="utf-8">
	jQuery.fn.dataTableExt.oSort['numeric-comma-asc']  = function(a,b) {
		var x = (a == "-") ? 0 : a.replace( /,/, "" );
		var y = (b == "-") ? 0 : b.replace( /,/, "" );
		x = parseFloat( x );
		y = parseFloat( y );
		return ((x < y) ? -1 : ((x > y) ?  1 : 0));
	};
	
	jQuery.fn.dataTableExt.oSort['numeric-comma-desc'] = function(a,b) {
		var x = (a == "-") ? 0 : a.replace( /,/, "" );
		var y = (b == "-") ? 0 : b.replace( /,/, "" );
		x = parseFloat( x );
		y = parseFloat( y );
		return ((x < y) ?  1 : ((x > y) ? -1 : 0));
	};
	

	$(document).ready(function() {
		$('#example').dataTable( {
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"bAutoWidth": false,
			"aoColumns": [
			  null,
			  { "sType": "numeric-comma", "bSearchable": false  },
			  { "sType": "numeric-comma", "bSearchable": false},
			  { "bSearchable": true },
			  { "bSortable": false, "bSearchable": false },
			  { "bSortable": false, "bSearchable": false },
			  { "bSortable": false, "bSearchable": false }
			 ], 
			"iDisplayLength": 100
		} );
	} );
</script>


<div class="align_center">
	<div class="manualmerges_title"> <?php echo $title; ?> </div>	
</div>

	<table cellpadding="0" cellspacing="0" border="0" class="display syncTable" id="example" width="100%">
	<thead style="background-color:#f1f1f1">
		
		  <tr>
			<th><div class="bold center">Name</div></th>
			<th><div class="bold center">Record Count</div></th>
			<th><div class="bold center">Field Count</div></th>
			<th><div class="bold center">Label</div></th>
			<th><div class="bold center">Queryable</div></th>
			<th><div class="bold center">Child Relationships</div></th>
			<th><div class="bold center">Fields</div></th>
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
					


						<td class="align_left"  style="border-left:1px solid #cccccc;">
							<a href="?page=salesforcefields&model_name=<?php echo $value->NAME;  ?>"><?php echo $value->NAME;  ?></a>
						</td>
						<td class="align_right"  style="border-left:1px solid #cccccc;"><?php echo number_format($value->record_count);  ?></td>
						<td class="align_right"  style="border-left:1px solid #cccccc;"><?php echo number_format($value->field_count);  ?></td>
						<td class="align_left"  style="border-left:1px solid #cccccc;"><?php echo $value->label;  ?></td>
						<td class="center"  style="border-left:1px solid #cccccc;">
							<?php 
								if($value->queryable == 0){
									echo"False";
								} 
							?>
						</td>
						

						
						<td class="center"  style="border-left:1px solid #cccccc; border-right:1px solid #cccccc;font-size:11px;">
							
							
							<?php
							if (!empty($value->child_relationships)) {
    
								if (strpos($value->child_relationships, '--- []') === false) { 

							?>
								<a class='inline_notes' href="#inline_notes_<?php echo $value->id;  ?>">view</a>
								<div style="display:none"> 
									<div id="inline_notes_<?php echo $value->id;  ?>"><pre><?php echo $value->child_relationships; ?></pre></div>
								</div>
								
							<?php
								}
							}
							?>
							
						</td>
						
						<td class="center"  style="border-left:1px solid #cccccc; border-right:1px solid #cccccc;font-size:11px;">
							
							<?php
							if (!empty($value->FIELDS)) {
    
								if (strpos($value->FIELDS, '--- []') === false) { 

							?>
								<a class='inline_notes' href="#inline_notes_<?php echo $value->id;  ?>">view</a>
								<div style="display:none"> 
									<div id="inline_notes_<?php echo $value->id;  ?>"><pre><?php echo $value->FIELDS; ?></pre></div>
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
SELECT id, NAME, record_count, field_count, label, queryable, child_relationships, FIELDS
  FROM salesforce_models
 ORDER BY NAME
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

