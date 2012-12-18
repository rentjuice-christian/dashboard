<?php render('_header')?>

<div class="body-wrapper">
		<div class="centered">
<?php
	/*echo"<pre>";
		print_r($sub_barcontent);
	echo"</pre>";*/
?>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#table1').dataTable( {
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"bSort":false,
			"iDisplayLength": 100
		} );
		
		$('#table2').dataTable( {
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"bSort":false,
			"iDisplayLength": 100
		} );
		
	});
</script>
<div class="manualmerges_title align_center">Super Tag Definitions </div>
<div class="group">
	<div class="column480 left">
		
		<div class="title2 align_center">Super Tag Assignments</div>
		
		<table cellpadding="0" cellspacing="0" border="0" class="display" id="table1" width="100%">
			<thead>
				<tr>
					<th><div class="bold">Tag Name</div></th>
					<th><div class="bold">Super Tag Name</div></th>
					<th><div class="bold">Tag Count</div></th>
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
								<td style="border-left:1px solid #cccccc;"><?php echo $value->tag_name; ?></td>
								<td style="border-left:1px solid #cccccc;"><?php echo $value->super_tag_name; ?></td>
								<td class="center"  style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; "><?php echo $value->tag_count; ?></td>
							</tr>
						<?php
						}
						$i++;
					}
				?>
		
			</tbody>
			<tfoot>
		</table>

	</div><!-- end column left -->
	<div class="column480 right">
		<div class="title2 align_center">Super Tag Priorities</div>
		
		<table cellpadding="0" cellspacing="0" border="0" class="display" id="table2" width="100%">
			<thead>
				<tr>
					<th><div class="bold">Tag Name</div></th>
					<th><div class="bold">Priority</div></th>
				</tr>
			</thead>
			<tbody>
			
			<?php
					$getValue = $sub_barcontent;
					$countData = count($getValue);
					if($countData != 0){
						$i = 0;
						foreach($getValue as $value){
						$i = $i + 1;
						 if($i%2 == 0){ $class = 'even';  }
						 else{ $class = 'odd'; }
		
						?>
							<tr class="<?php echo $class; ?>">
								<td style="border-left:1px solid #cccccc;"><?php echo $value->tag_name; ?></td>
								<td class="center"  style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; "><?php echo $value->priority; ?></td>
							</tr>
						<?php
						}
						$i++;
					}
				?>
		
			</tbody>
			<tfoot>
		</table>
	</div>
</div>

<div class="align_right show_query">
	<a class='inline' href="#inline_content"><img src="assets/images/show_query.png" alt="show query" /></a>
</div>
<div style="display:none">
	<div id='inline_content' style='padding:10px; background:#fff;'>
	
<div class="bold">Super Tag Assignments</div>
	
<pre>
SELECT tag_count, cl.tag_name, t.tag_name super_tag_name
  FROM ( 
        SELECT COUNT(*) tag_count, label_name tag_name
          FROM assistly_case_labels
         GROUP BY label_name
       ) cl
       
       LEFT OUTER JOIN super_taggings st
         ON cl.tag_name = st.label_name
       LEFT OUTER JOIN super_tags t
         ON st.super_tag_id = t.id
</pre>
<br/>
<div>--------------------------------------------------------------------------------</div>
<br/>
<div class="bold">Super Tag Priorities</div>
	
<pre>
SELECT tag_name, priority FROM super_tags
ORDER BY priority
</pre>
	</div>
</div>


	</div><!-- centered -->
</div><!-- body_wrapped -->
<?php 
	$start = array('start_time'=>$start_time);
	render('_footer',$start)
?>


