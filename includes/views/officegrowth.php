<?php render('_header',array('title'=>$title));  ?>

<div class="body-wrapper">
		<div class="centered">
			<div class="main_content_temp">

<script type="text/javascript">

$(function () {

	var isLoading = false,
    $button = $('.select_time');
    $button.change(function() {
        if (!isLoading) {
            chart.showLoading();
        } else {
            chart.hideLoading();
        }
        isLoading = !isLoading;
    });
	//chart initialization
	Highcharts.setOptions({
		lang: {
			loading: 'Waiting for Data'
		}
	});
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
			type: 'datetime',
		  	labels: {
						formatter: function() {
							return Highcharts.dateFormat('%b %Y', this.value);
						}
			}
			
        },
		yAxis: {
			title: {
				text: ' '
			},
			min:0
			
		},
		tooltip: {
			 formatter: function() {
				  var s = '<b>'+ this.series.name +'</b><br/>'+ Highcharts.dateFormat('%b %Y', this.x) +'='+ addCommas(this.y) +'';
				  return s;
			 }
		  },

      series: [
	  	<?php
				$newOptions = array();
		
					$getValue = $barcontent;
					
					$countData = count($getValue);
					if($countData != 0){
						foreach($getValue as $value){
							$month = $value->created;
							$agency = $value->agency_running_total;
							$landlord = $value->landlord_running_total;
							
							$newOptions["Agencies"][$month] = $agency;
							$newOptions["Landlord"][$month] = $landlord;

						}
					}
					
					$countListings = count($newOptions);
					$i = 0;
					foreach($newOptions as $keyListings => $valueListings){
						$i = $i + 1;
						echo"{";
							$countListings2 = count($valueListings);
							echo"name:'".$keyListings."',";
							echo"data: [";
							$a = 0;
								foreach($valueListings as $keyDate => $valueData ){
									$a = $a + 1;
									
									$arrayValues = explode("-",$keyDate);
									$getmonth = $arrayValues[1] - 1;
									$reindex = $arrayValues[0].",".$getmonth.",01";
									
									echo "[Date.UTC(".$reindex."),".$valueData."]"; 
									
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

<?php		
	/*echo"<pre>";
		print_r($newOptions);
	echo"</pre>";*/
?>

<div class="align_center">
	<div class="manualmerges_title"> <?php echo $title; ?> </div>
</div>

<?php if(count($barcontent) == 0){ ?>
	<div class="content_noavail">No Available Data</div>
<?php }else{ ?>
	<div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
<?php } ?>
<div class="align_right show_query">
	<a class='inline' href="#inline_content"><img src="assets/images/show_query.png" alt="show query" /></a>
</div>
<div style="display:none">
<div id='inline_content' style='padding:10px; background:#fff;'>
<pre>
SELECT t.created,
       (@agency_running_total := @agency_running_total + t.agency_count) AS agency_running_total,
       (@landlord_running_total := @landlord_running_total + t.landlord_count) AS landlord_running_total      
  FROM (
        SELECT LEFT(o.created_on,7) created,
               SUM(IF(o.type = 'agency',1,0)) agency_count,
               SUM(IF(o.type = 'landlord',1,0)) landlord_count     
          FROM offices o
         WHERE LEFT(o.created_on,7) != '0000-00'
         GROUP BY 1
         ORDER BY 1
       ) t, (SELECT @agency_running_total := 0 AS dummy, @landlord_running_total := 0 AS dummy2) dummy
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

