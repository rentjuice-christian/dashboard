<?php render('_header')?>

<div class="body-wrapper">
		<div class="centered">
			<div class="manualmerges_title align_center">Data Imports Over Time</div>
			<div class="align_center">Data Import over time. We assume a data import has occurred if there is at least one listing with an import_reference_id</div>
			<br />
			<div class="main_content_temp">
			
			<?php
				/*echo"<pre>";
				print_r($barcontent);
				echo"</pre>";*/
			?>
			
<script type="text/javascript">

$(function () {

	// create the chart
    var chart = new Highcharts.Chart({
        chart: {
            renderTo: 'container',
			type:'line',
			backgroundColor: '#F7F5F0'
        },
		loading: {
            labelStyle: {
                color: 'white',
				top: '45%'
            },
            style: {
                backgroundColor: 'gray'
            }
        },
		title: {
                text: ' '
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
			,tickInterval:210 * 24 * 3600 * 1000	
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
				    Highcharts.dateFormat('%B %Y', this.x) +' = '+ this.y;
					
			 }
		  },

        series: [{
			name:' Data Imports Over Time',
            data: [
					<?php
					
							$getValue = $barcontent;
							$countData = count($getValue);
							
							if($countData != 0){
								$i = 0;
								foreach($getValue as $value){
									$i = $i + 1;
									$arrayValues = explode("-",$value->createdon);
									$getmonth = $arrayValues[1] - 1;
									$reindex = $arrayValues[0].",".$getmonth.",01";
									
									echo "[Date.UTC(".$reindex."),".$value->countoffice."]";

									if($i != $countData){ echo",";}
									
								}
								$i++;
							}
					?>
				  ]
		}]
		
    }); // end new Highcharts
});// end function


</script>



<div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
<div class="align_right show_query">
	<a class='inline' href="#inline_content"><img src="assets/images/show_query.png" alt="show query" /></a>
</div>
<div style="display:none">
<div id='inline_content' style='padding:10px; background:#fff;'>
<pre>
SELECT LEFT(o.created_on,7), COUNT(DISTINCT l.office_id)
  FROM rentjuice.offices o INNER JOIN rentjuice.listings l ON l.office_id = o.id
 WHERE (l.import_reference_id IS NOT NULL AND import_reference_id != '')
   AND o.name NOT LIKE "%OLD DE ACCOUNT%"
   AND o.dataentry = 0
 GROUP BY 1
 ORDER BY 1
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

