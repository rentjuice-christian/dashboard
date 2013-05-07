<?php render('_header')?>
<?php

	if(isset($_REQUEST['account_status'])){
		if (!empty($_REQUEST['account_status'])){
			if($_REQUEST['account_status'] == "exclude" || $_REQUEST['account_status'] =="include"){
				$account_status = $_REQUEST['account_status'];
			}
			else{ $account_status = "exclude"; } 
		}
		else{
			$account_status = "exclude";
		}
	}
	else{ $account_status = "exclude"; }
	
	if(isset($_REQUEST['show_blank_id'])){
		if (!empty($_REQUEST['show_blank_id'])){
			if($_REQUEST['show_blank_id'] == "no" || $_REQUEST['show_blank_id'] =="yes"){
				$show = $_REQUEST['show_blank_id'];
			}
			else{ $show = "no"; } 
		}
		else{
			$show = "no";
		}
	}
	else{ $show = "no"; }

?>

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
		<div class="manualmerges_title">Bad Salesforce IDs</div>
	
	</div>
	<script type="text/javascript" charset="utf-8">
		$(document).ready(function() {
			$('#badsalesforceid').dataTable( {
				"bJQueryUI": true,
				"sPaginationType": "full_numbers",
				"bSort":false,
				"iDisplayLength": 100
			} );
		} );
	</script>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="form_select" id="form_submit" >
	<div class="align_right">
		<input type="hidden" value="badsalesforceid" name="page" />
		<span>Account Status: </span>
		<select name="account_status" onchange="submit();" class="select_time">
			<option value="exclude" <?php if($account_status =="exclude"){ echo"selected";} else{ echo""; } ?>>Exclude Free Accounts</option>
			<option value="include" <?php if($account_status =="include"){ echo"selected";} else{ echo""; } ?>>Include Free Accounts</option>
		</select>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<span>Show Blank IDs: </span>
		<select name="show_blank_id" onchange="submit();" class="select_time">
			<option value="no" <?php if($show =="no"){ echo"selected";} else{ echo""; } ?>>No</option>
			<option value="yes" <?php if($show =="yes"){ echo"selected";} else{ echo""; } ?>>Yes</option>
		</select>
	</div>
</form>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="badsalesforceid" width="100%">
	<thead>
		<tr>
			<th><div class="bold">Customer Name</div></th>
			<th><div class="bold">Salesforce ID</div></th>
			<th><div class="bold">Notes</div></th>
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
							<a href="http://radmin.rentalapp.zillow.com/customer:<?php echo $value->id; ?>" target="_blank">
								<?php echo $value->name; ?>
							</a>
						</td>
						<td class="center"  style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; ">
							<a href="https://na10.salesforce.com/<?php echo $value->sforce_acct_id; ?>" target="_blank">
								<?php echo $value->sforce_acct_id; ?>
							</a>
						</td>
						<td class="center"  style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; "><?php echo $value->notes ; ?></td>
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
SELECT o.sforce_acct_id, o.id, o.created_on, o.name,
       CONCAT(IF(ISNULL(so.id),"","Salesforce ID is a Opportunity"),
              IF(ISNULL(sl.id),"","Salesforce ID is a Lead"),
              IF(ISNULL(sa.id),"","Salesforce ID is an Account")) notes
  FROM rentjuice.customers o
       LEFT OUTER JOIN
       janak.salesforce_opportunities so
       ON o.sforce_acct_id = so.id_15
       LEFT OUTER JOIN
       janak.salesforce_leads sl
       ON o.sforce_acct_id = sl.id_15
       LEFT OUTER JOIN
       janak.salesforce_accounts sa
       ON o.sforce_acct_id = sa.id_15
 WHERE (so.id IS NOT NULL OR sl.id IS NOT NULL)
 ORDER BY o.created_on DESC
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

