<?php render('_header',array('title'=>'Listings Imported Over Time'));  ?>

		
						
<script type="text/javascript">

$(function () {

	// create the chart
    var chart = new Highcharts.Chart({
        chart: {
            renderTo: 'container',
			type:'area',
			backgroundColor: '#F7F5F0'
        },
		loading: {
            labelStyle: {color: 'white',top: '45%'},
            style: {backgroundColor: 'gray'}
        },
		title: {
                text: ' '
        },
		plotOptions: {
                area: {
                    stacking: 'normal',
                    lineColor: '#666666',
                    lineWidth: 1,
                    marker: {
                        lineWidth: 1,
                        lineColor: '#666666',
						enabled: false,
						states: {
							hover: {
								enabled: true,
								fillColor: 'white',
								lineWidth: 2
							}
						}
                    }
                } // and area
         },
        xAxis: {
			title: {
				text: ''
			},
			type : 'datetime',
			labels: {
				formatter: function() {	
				 return Highcharts.dateFormat('%b %Y', this.value); 
				}
			}
        },
		yAxis: {

			title: {
				text: ''
			},
			min:0
		},
		tooltip: {
			  formatter: function() {
			  		
					return '<b>'+ this.series.name +'</b><br/>'+
				    Highcharts.dateFormat('%B %Y', this.x) +' = '+ addCommas(this.y);
					
			 }
		  },

          series: [
			<?php
			
					$newOptions = array();
		
					$getValue = $barcontent;
					
					$countData = count($getValue);
					if($countData != 0){
						foreach($getValue as $value){
							$month = $value->MONTH;
							$listing_status = $value->listing_status;
							$count_data = $value->count_data;
							
								$newOptions[$listing_status][$month] = $count_data;

						}
					}
					
					$countListings = count($newOptions);
					
					$result = array_map('array_sum', $newOptions); // total the sum of the reult query
					arsort($result); // sort it higher first
					
					$result2 = array_merge($result,  $newOptions); //merge array 
					
					$i = 0;
					foreach($result2 as $keyListings => $valueListings){
						$i = $i + 1;
						echo"{";
							$countListings2 = count($valueListings);
							echo"name:'".$keyListings."',";
							echo"data: [";
							$a = 0;
								foreach($valueListings as $keyDate => $valueListings ){
									$a = $a + 1;
									
									$arrayValues = explode("-",$keyDate);
									$getmonth = $arrayValues[1] - 1;
									$reindex = $arrayValues[0].",".$getmonth.",01";
									
									echo "[Date.UTC(".$reindex."),".$valueListings."]"; 
									
									if($a != $countListings2){ echo",";}			
								}
								$a++;
							echo"]";
						echo"}";
						
						if($i != $countListings){ echo",";}
						
					}
					
					$i++;
			
			?>
		]
		
    }); // end new Highcharts
});// end function


</script>

<div class="body-wrapper">
	<div class="centered">
		<div class="main_content_temp">

<?php
 if(!empty($error_message)){
	render('error',array('error_message'=>$error_message));
 }
 else{
?>		
	<div class="manualmerges_title align_center">Listings Imported Over Time</div>
	<div class="align_center">Number of listings imported each Month.</div>
	<div class="align_center">November 2010 has been removed to make the graph more useful</div>
	<div style="font-size:11px;" class="align_center">Note: This may contain a lot of junk listings that never go active in our system</div>
	<br />

	<div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>

<?php } ?>

<div class="align_right show_query">
	<a class='inline' href="#inline_content"><img src="assets/images/show_query.png" alt="show query" /></a>
</div>
<div style="display:none">
<div id='inline_content' style='padding:10px; background:#fff;'>
<pre>
SELECT LEFT(o.created_on,7) MONTH,
       IF(l.active = 1,'Active Listings', 'Inactive Listings') listing_status,
       COUNT(l.id)
  FROM rentjuice.offices o
       INNER JOIN rentjuice.listings l
         ON l.office_id = o.id
 WHERE (l.import_reference_id IS NOT NULL AND import_reference_id != '')
   AND o.dataentry = 0
   AND o.name NOT LIKE "%OLD DE ACCOUNT%"
   AND o.id != 4463
   AND LEFT(o.created_on,7) <> '2010-11'
 GROUP BY 1,2
 ORDER BY 1,2

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

