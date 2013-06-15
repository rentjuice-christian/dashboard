<?php render('_header',array('title'=>$title))?>

<?php
	$timeFrame = "";
	
	if(isset($_REQUEST['timespan'])){
	
		if(!empty($_REQUEST['timespan'])){
			$timeFrame = $_REQUEST['timespan']; 
		}
		else{$timeFrame = '4';}
		
	}
	else{ $timeFrame = '4'; }
?>

<div class="body-wrapper">
		<div class="centered">
			<div class="main_content_temp">
			
<script type="text/javascript">

$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
				type:'pie'
            },
            title: {
                text: ''
            },
            tooltip: {

				 formatter: function() {
				  return '<b>'+ this.series.name +'</b>'+ addCommas(this.y) +' <br/>';
			 	}
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        color: '#000000',
                        connectorColor: '#000000'
                    }
                }
            },
            series: [{
                name: 'Count Data: ',
                data: [
					
					<?php
					
							$getValue = $barcontent;
							$countData = count($getValue);
							if($countData != 0){
								$i = 0;
								foreach($getValue as $value){
									$i = $i + 1;
									
									echo "['".$value->source."', ".$value->count_data."]";
									
									if($i != $countData){ echo",";}
								}
								$i++;
							}
					?>
					
                ]
            }]
        });
    });
    
});

</script>

<?php
/*echo"<pre>";
print_r($barcontent);
echo"</pre>";*/
?>

<div class="align_center">
	<div class="manualmerges_title"><?php echo $title; ?></div>
</div>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="form_select" id="form_submit" >
	<div class="align_right">
		<input type="hidden" value="listingsbysource" name="page" />
		<span>Select Time Frame: </span>
		<select name="timespan" onchange="submit();" class="select_time">
			<option value="1" <?php if($timeFrame == '1'){ echo'selected'; }else{ echo''; }  ?>>1 Week</option>
			<option value="2" <?php if($timeFrame == '2'){ echo'selected'; }else{ echo''; }  ?>>2 Weeks</option>
			<option value="3" <?php if($timeFrame == '3'){ echo'selected'; }else{ echo''; }  ?>>3 Weeks</option>
			<option value="4" <?php if($timeFrame == '4'){ echo'selected'; }else{ echo''; }  ?>>4 Weeks</option>
			<option value="5" <?php if($timeFrame == '5'){ echo'selected'; }else{ echo''; }  ?>>1 Month</option>
			<option value="6" <?php if($timeFrame == '6'){ echo'selected'; }else{ echo''; }  ?>>2 Months</option>
			<option value="7" <?php if($timeFrame == '7'){ echo'selected'; }else{ echo''; }  ?>>3 Months</option>
			<option value="8" <?php if($timeFrame == '8'){ echo'selected'; }else{ echo''; }  ?>>4 Months</option>
			<option value="9" <?php if($timeFrame == '9'){ echo'selected'; }else{ echo''; }  ?>>5 Months</option>
			<option value="10" <?php if($timeFrame == '10'){ echo'selected'; }else{ echo''; }  ?>>6 Months</option>
			<option value="11" <?php if($timeFrame == '11'){ echo'selected'; }else{ echo''; }  ?>>1 Year</option>
			<option value="12" <?php if($timeFrame == '12'){ echo'selected'; }else{ echo''; }  ?>>2 Years</option>
		</select>
	</div>
</form>

<div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
<div class="align_right show_query">
	<a class='inline' href="#inline_content"><img src="assets/images/show_query.png" alt="show query" /></a>
</div>
<div style="display:none">
<div id='inline_content' style='padding:10px; background:#fff;'>
<?php
	$arrayDate = array('1'=>'7','2'=>'14','3'=>'21','4'=>'28','5'=>'30','6'=>'60','7'=>'90','8'=>'120','9'=>'150','10'=>'180','11'=>'360','12'=>'720');
			
	if(isset($_REQUEST['timespan'])){
		if(!empty($_REQUEST['timespan'])){ 
			if (array_key_exists($_REQUEST['timespan'],$arrayDate)){
				$timeSpan = $arrayDate[$_REQUEST['timespan']];
			}
			else{ $timeSpan= "28"; }
		}
		else{ $timeSpan= "28"; }
	}
	else{ $timeSpan= "28"; }
?>
<pre>
SELECT 'feed sync' AS "source", COUNT(l.id)
  FROM rentjuice.listings l
 WHERE l.active = 1
   AND l.listing_source IN ("nsync","zif")
   AND DATE(l.created) >= DATE_SUB(CURDATE() , INTERVAL <?php echo $timeSpan; ?> DAY)
UNION
SELECT 'import' AS "source", COUNT(l.id)
  FROM rentjuice.listings l
 WHERE l.active = 1
   AND l.listing_source IN ("custom import","ditch","diw","postlets")
   AND DATE(l.created) >= DATE_SUB(CURDATE() , INTERVAL <?php echo $timeSpan; ?> DAY)
UNION
SELECT 'data entry' AS "source", COUNT(l.id)
  FROM rentjuice.listings l
 WHERE l.active = 1
   AND l.listing_source = "data entry"
   AND DATE(l.created) >= DATE_SUB(CURDATE() , INTERVAL <?php echo $timeSpan; ?> DAY)
UNION
SELECT 'manual' AS "source", COUNT(l.id)
  FROM rentjuice.listings l
 WHERE l.active = 1
   AND l.listing_source = "manual"
   AND DATE(l.created) >= DATE_SUB(CURDATE() , INTERVAL <?php echo $timeSpan; ?> DAY) 
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

