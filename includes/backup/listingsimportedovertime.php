<?php render('_header')?>

<div class="body-wrapper">
		<div class="centered">
			<div class="manualmerges_title align_center"> Listings Imported Over Time</div>
			<div class="align_center">Number of listings imported each Month.</div>
			<div class="align_center">November 2010 has been removed to make the graph more useful</div>
			<div style="font-size:11px;" class="align_center">Note: This may contain a lot of junk listings that never go active in our system</div>
			<br />
			<div class="main_content_temp">
			
			<?php
				/*echo"<pre>";
				print_r($barcontent);
				echo"</pre>";*/
			?>
			
<script type="text/javascript">

function addCommas(nStr)
{
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}

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
				    Highcharts.dateFormat('%B %Y', this.x) +' = '+ addCommas(this.y);
					
			 }
		  },

        series: [{
			name:'Listings Imported Over Time',
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

<?php
	/*echo"<pre>";
		print_r($barcontent);
	echo"</pre>";*/
?>

<div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
<div class="align_right"><a class='inline' href="#inline_content">Show SQL Query</a></div>
<div style="display:none">
<div id='inline_content' style='padding:10px; background:#fff;'>
<pre>
SELECT LEFT(o.created_on,7), COUNT(l.id) FROM rentjuice.offices o INNER JOIN rentjuice.listings l ON l.office_id = o.id
WHERE (l.import_reference_id IS NOT NULL AND import_reference_id != '')
AND o.dataentry = 0
AND o.name NOT LIKE "%OLD DE ACCOUNT%"
AND o.id != 4463
AND LEFT(o.created_on,7) <> '2010-11'
GROUP BY 1
ORDER BY 1
</pre>
</div>
</div>

			</div>
			
		</div>
</div>
<?php render('_footer')?>

