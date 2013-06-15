<?php render('_header',array('title'=>$title))?>
<?php

$arrayDate = array('1'=>'1 WEEK','2'=>'2 WEEK','3'=>'3 WEEK','4'=>'4 WEEK','5'=>'2 MONTH','6'=>'3 MONTH','7'=>'4 MONTH','8'=>'alltime');
$date2 = "";


if(isset($_REQUEST['officename'])){
		if(!empty($_REQUEST['officename'])){ 
			$explode = explode("(", urldecode($_REQUEST['officename']));
			$url = urldecode($explode[0]); 
		}
		else{ $url = "159 Real Estate"; }
}
else{ $url = "159 Real Estate";	}	

if(isset($_REQUEST['ticketsearch_time'])){		
	if (array_key_exists($_REQUEST['ticketsearch_time'],$arrayDate)){
		$date = $arrayDate[$_REQUEST['ticketsearch_time']];
		$date2 = $_REQUEST['ticketsearch_time'];
	}
	else{ 
		$date = "alltime"; 
		$date2 = "alltime";  
	}
}
else{ 
	$date = "alltime"; 
	$date2 = "alltime"; 
}

?>
<?php
	/*echo"<pre>";
		print_r($sub_barcontent);
	echo"</pre>";*/
	
?>

<div class="body-wrapper">
		<div class="centered">
			
	<div class="align_center">
		<div class="manualmerges_title"><?php echo $title; ?></div>
	</div>


<script>
	function fixedEncodeURIComponent (str) {
	  return encodeURIComponent(str).replace(/[!'()]/g, escape).replace(/\*/g, "%2A");
	}
	
    $(function() {
        var availableTags = [
           <?php
				$count = count($barcontent);
				$i = 0;
				foreach($barcontent as $companyValue){
					$i = $i + 1;
					
					echo  '"'.$companyValue->name.'('.$companyValue->ticket_count.')"';
					if($i != $count){ echo",";}
					
				}
				
				$i++;
			?>
        ];
		
       $( "#tags" ).autocomplete({
		  source: function( request, response ) {
			var matches = $.map( availableTags, function(tag) {
			  if ( tag.toUpperCase().indexOf(request.term.toUpperCase()) === 0 ) {
				return tag;
			  }
			});
			response(matches);
		  },
		   select: function( event, ui ) {
             
			 // alert(ui.item.value);
			 
			 $('#cboxOverlay').css("display","block");
			 $('#cboxOverlay').css("opacity","0.7");
			 $('#spinner').show();
			 
			  $("#officename").val(fixedEncodeURIComponent(ui.item.value));
			  $('#form_submit').submit();
            }
		  
		}); // end autocomplete
		
		
		$('#example').dataTable( {
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"bSort":false,
			"iDisplayLength": 100
		} );
		
	   $('#example a.tooltip').each(function()
	   {
		  $(this).qtip({
				content: $(this).find('.preview'),
				show: { delay: 0 },
				position: {
					corner: {
						tooltip: 'topMiddle',
						target: 'bottomMiddle'
					}
				},
				style: {
					tip: true, // Give it a speech bubble tip with automatic corner detection
					name: 'dark'
				}
		  });
	   });
		
    });
</script>

	<div style="padding:10px 0;border-top:1px solid #cccccc;">
		<div class="left">
			<div><strong>Office Name:</strong> <?php echo $url; ?></div>
			<div><strong>Time Span:</strong> 
				<?php 
					$arrayDate2 = array('1'=>'1 Week','2'=>'2 Weeks','3'=>'3 Weeks','4'=>'4 Weeks','5'=>'2 Months','6'=>'3 Months','7'=>'4 Months','8'=>'All Time'); 
					if (array_key_exists($_REQUEST['ticketsearch_time'],$arrayDate2)){
						echo $arrayDate2[$_REQUEST['ticketsearch_time']];
					}
					else{
						echo"All Time";
					}
				?>
			</div>
		</div>
		<div class="right">
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" id="form_submit"  >

					<input type="hidden" value="ticketsearchforoffices" name="page" />
					<input type="hidden" name="officename" id="officename" value="<?php echo urlencode($url); ?>"/>
			
					<div style="font-size:12px;">
						<div><label for="tags">Select Office Name: </label><input id="tags" style="width:250px;"/></div>
						<div style="padding-top:5px;">
							<span>Select Time Frame: </span>
							<select name="ticketsearch_time" onchange="submit();" class="select_time">
								<option value="1" <?php if($date2 == '1'){ echo'selected'; }else{ echo''; }  ?>>1 Week</option>
								<option value="2" <?php if($date2 == '2'){ echo'selected'; }else{ echo''; }  ?>>2 Weeks</option>
								<option value="3" <?php if($date2 == '3'){ echo'selected'; }else{ echo''; }  ?>>3 Weeks</option>
								<option value="4" <?php if($date2 == '4'){ echo'selected'; }else{ echo''; }  ?>>4 Weeks</option>
								<option value="5" <?php if($date2 == '5'){ echo'selected'; }else{ echo''; }  ?>>2 Months</option>
								<option value="6" <?php if($date2 == '6'){ echo'selected'; }else{ echo''; }  ?>>3 Months</option>
								<option value="7" <?php if($date2 == '7'){ echo'selected'; }else{ echo''; }  ?>>4 Months</option>
								<option value="8" <?php if($date2 == '8' || $date2 == 'alltime'){ echo'selected'; }else{ echo''; }  ?>>All Time</option>
							</select>		
							</div>	
					</div>
					
			</form>
		</div>
		<div class="clear"></div>
	</div>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="example" width="100%">
	<thead>
		<tr>
			<th style="width:11%;"><div class="bold">Days</div></th>
			<th style="width:8%;"><div class="bold">Channel</div></th>
			<th style="width:14%;"><div class="bold">Super Tag</div></th>
			<th style="width:17%;"><div class="bold">User Name</div></th>
			<th style="width:39%;"><div class="bold">Subject</div></th>
			<th style="width:14%;"><div class="bold">Handle Time</div></th>
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
						<td  style="border-left:1px solid #cccccc;">
							<?php echo $value->days_since; ?> Days ago
						</td>
						<td  style="border-left:1px solid #cccccc;border-right:1px solid #cccccc;">
							<?php 
								if(!empty($value->channel)){
									echo $value->channel; 
								}
							?>
						</td>
						<td  style="border-left:1px solid #cccccc;border-right:1px solid #cccccc;">
							<?php 
								if(!empty($value->super_tag)){
									echo $value->super_tag; 
								}
							?>
						</td>
						<td  style="border-left:1px solid #cccccc;border-right:1px solid #cccccc;">
							<?php 
								if(!empty($value->username)){
									?>
									<a href="http://radmin.rentjuice.com/user:<?php echo $value->user_id;  ?>" target="_blank">
										<?php echo $value->username; ?>
									</a>
									<?php
								}
							?>
						</td>
						<td style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; ">
							<?php 
								if(!empty($value->subject)){
									?>
									<a href="https://rentjuice.desk.com/agent/case/<?php echo $value->ticket_id;  ?>" target="_blank" class="tooltip">
										<?php echo html_entity_decode($value->subject); ?>
										<div class="preview" style="display:none;">
											<?php 
												if(!empty($value->preview)){ echo html_entity_decode($value->preview); }
												else{ echo"No Data to Preview"; }
											?>
										</div>
									</a>
									<?php
								}
							?>
						<?php //echo html_entity_decode($value->preview);  ?>	
						</td>
						<td  style="border-left:1px solid #cccccc;border-right:1px solid #cccccc;">
							<?php 
								if(!empty($value->handle_time_total)){
									//echo number_format($value->handle_time_total); 
									if($value->handle_time_total < 60){
										echo $value->handle_time_total." minutes"; 
									}
									else if($value->handle_time_total < 1440){
										if(floor($value->handle_time_total/60) == 1){
											echo floor($value->handle_time_total/60)." hour";
										}
										else{
											echo floor($value->handle_time_total/60)." hours";
										}
									}
									else if($value->handle_time_total > 1440){
										if(floor($value->handle_time_total/1440) == 1){
											echo floor($value->handle_time_total/1440)." day";
										}
										else{
											echo floor($value->handle_time_total/1440)." days";
										}
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
-----------------------------------------------------------------------------
Select Query for the Office Ticket Count Drop Down
-----------------------------------------------------------------------------

SELECT o.name, COUNT(*) ticket_count
  FROM janak.assistly_cases ca, janak.assistly_customers cu, rentjuice.users u, rentjuice.offices o
 WHERE ca.customer_id = cu.id
   AND cu.custom_user_id = u.id
   AND u.office_id = o.id
<?php
if($date != "alltime"){ 
?>
   AND DATE_ADD(ca.created_at, INTERVAL <?php echo $date; ?>) >= SYSDATE()
<?php
}
?>
 GROUP BY 1
 ORDER BY 1

-----------------------------------------------------------------------------
Select Query for the Office Ticket Count
-----------------------------------------------------------------------------

SELECT o.name,
       ca.id ticket_id,
       DATEDIFF(SYSDATE(),created_at) days_since,
       ca.subject,
       u.id user_id,
       CONCAT(u.first_name,' ',u.last_name) username
       ca.preview,
       channel,
       ca.super_tag,
       TIMESTAMPDIFF(MINUTE,first_opened_at,resolved_at) handle_time_total
  FROM janak.assistly_cases ca, janak.assistly_customers cu, rentjuice.users u, rentjuice.offices
 WHERE ca.customer_id = cu.id
   AND cu.custom_user_id = u.id
   AND u.office_id = o.id
   AND o.name = '<?php echo $url; ?>'
<?php
if($date != "alltime"){ 
?>
    AND DATE_ADD(ca.created_at, INTERVAL <?php echo $date; ?>) >= SYSDATE()
<?php
}
?>
 ORDER BY ca.created_at DESC
</pre>
	</div>
</div>
		
		</div>
</div>
<?php 
	$start = array('start_time'=>$start_time);
	render('_footer',$start)
?>

