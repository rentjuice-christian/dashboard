<?php render('_header')?>
<?php

$arrayDate = array('1'=>'1 WEEK','2'=>'2 WEEK','3'=>'3 WEEK','4'=>'4 WEEK','5'=>'2 MONTH','6'=>'3 MONTH','7'=>'4 MONTH','8'=>'alltime');
$date2 = "";

if(isset($_REQUEST['username'])){
		if(!empty($_REQUEST['username'])){ 
			$explode = explode("(", urldecode($_REQUEST['username']));
			$url = urldecode($explode[0]); 
		}
		else{ $url = "Aaron Barrett"; }
}
else{ $url = "Aaron Barrett";	}	

if(isset($_REQUEST['ticketsearch_time'])){		
	if (array_key_exists($_REQUEST['ticketsearch_time'],$arrayDate)){
		$date = $arrayDate[$_REQUEST['ticketsearch_time']];
		$date2 = $_REQUEST['ticketsearch_time'];
	}
	else{ $date = "alltime"; $date2 = "alltime";  }
}
else{ $date = "alltime"; $date2 = "alltime"; }

if($date != "alltime"){ 
	$strQuery = "AND DATE_ADD(ca.created_at, INTERVAL ".$date.") >= SYSDATE()"; 
}
else{ 
	$strQuery =""; 
}

?>

<?php
	/*echo"<pre>";
		print_r($barcontent);
	echo"</pre>";*/
	
?>
 <?php
	/*$count = count($barcontent);
	$i = 0;
	foreach($barcontent as $companyValue){
		$i = $i + 1;
			echo  '"'.preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '',$companyValue->username).'('.$companyValue->ticket_count.')" <br/>';
		if($i != $count){ echo",";}
	}
	
	$i++;*/
?>

<div class="body-wrapper">
		<div class="centered">
			<div class="main_content_temp">


<div>
	<div class="align_center">
		<div class="manualmerges_title">Ticket Search for Users</div>
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
						echo  '"'.preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '',$companyValue->username).'('.$companyValue->ticket_count.')"';
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
			 
			  $("#username").val(fixedEncodeURIComponent(ui.item.value));
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
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" id="form_submit" >

				<input type="hidden" value="ticketsearchforusers" name="page" />
				<input type="hidden" name="username" id="username" value="<?php echo urlencode($url); ?>"/>
		
				<div style="font-size:12px;">
					<div><label for="tags">Select Office Users: </label><input id="tags" style="width:250px;"/></div>
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
			<th><div class="bold">Days</div></th>
			<th><div class="bold">Subject</div></th>
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
						<td class="center" style="border-left:1px solid #cccccc; width:20%;">
							<?php echo $value->days_since; ?> Days ago
						</td>
						<td class="center" style="border-left:1px solid #cccccc;border-right:1px solid #cccccc;width:60%; ">
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
-----------------------------------------------------------------------------
Select Query for the Users Ticket Count Drop Down
-----------------------------------------------------------------------------

SELECT CONCAT(u.first_name,' ',u.last_name) username, COUNT(*) ticket_count
  FROM janak.assistly_cases ca, janak.assistly_customers cu, rentjuice.users u, rentjuice.offices o
WHERE ca.customer_id = cu.id
   AND cu.custom_user_id = u.id
   AND u.office_id = o.id
<?php echo $strQuery; ?>
GROUP BY 1
ORDER BY 1

-----------------------------------------------------------------------------
Select Query for the Users Ticket Count
-----------------------------------------------------------------------------

SELECT o.name,
   ca.id ticket_id,
   DATEDIFF(SYSDATE(),created_at) days_since,
   ca.subject,
   u.id user_id,
   CONCAT(u.first_name,' ',u.last_name) username
FROM janak.assistly_cases ca, janak.assistly_customers cu, rentjuice.users u, rentjuice.offices o
WHERE ca.customer_id = cu.id
AND cu.custom_user_id = u.id
AND u.office_id = o.id
AND CONCAT(u.first_name,' ',u.last_name) = '<?php echo $url; ?>'
<?php echo $strQuery; ?>
ORDER BY ca.created_at DESC

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

