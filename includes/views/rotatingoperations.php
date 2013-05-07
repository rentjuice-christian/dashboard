<?php render('_header')?>

<?php
	// for the Display Values
	$arrayDisplay = array('1'=>'5000', '2'=>'10000', '3'=>'15000', '4'=>'20000', '5'=>'25000', '6'=>'30000');
	if(isset($_GET['display'])){
		if(!empty($_GET['display'])){
			if (array_key_exists($_GET['display'],$arrayDisplay)){
				$displayTime = $arrayDisplay[$_GET['display']];
			}
			else{ $displayTime = '15000'; }
		}
		else{ $displayTime = '15000'; }
	}
	else{ $displayTime = '15000'; }
	
	// for the Refresh Values
	$arrayRefresh = array('1'=>'15', '2'=>'30', '3'=>'45', '4'=>'60');
	if(isset($_GET['refresh'])){
		if(!empty($_GET['refresh'])){
			if (array_key_exists($_GET['refresh'],$arrayRefresh)){
				$displayRefresh = $arrayRefresh[$_GET['refresh']];
			}
			else{ $displayRefresh = '15'; }
		}
		else{ $displayRefresh = '15'; }
	}
	else{ $displayRefresh = '15'; }
?>


<div class="body-wrapper">

<script type="text/javascript">
	var targetTime = new Date();
	// Right now
	var now = targetTime.getTime();
	// Time in the future when you want to refresh
	targetTime.setHours(0,<?php echo $displayRefresh; ?>,0,0); // hour, minute, second, millisecond
	// Time until refresh
	var time = targetTime.getTime() - now;
	if(time > 0){
		window.setTimeout(function(){window.location.reload(true);},time);	
	}
	
</script>

<script>
	jQuery(document).ready(function(){
		
		// Set starting slide to 1
			var startSlide = 1;
			// Get slide number if it exists
			if (window.location.hash) {
				startSlide = window.location.hash.replace('#','');
			}
			
			// timer
			//var counter = 0;
//			var interval = setInterval(function() {
//				counter++;
//				// Display 'counter' wherever you want to display it.
//				if (counter == 16) {
//					counter = 1;
//				}
//				$('#number_timer').text(counter);
//			}, 1000);
			
			// Initialize Slides
			$('#slides').slides({
				preload: true,
				preloadImage: 'img/loading.gif',
				generatePagination: false,
				generateNextPrev: false,
				play: <?php echo $displayTime; ?>,
				pause: <?php echo $displayTime; ?>,
				hoverPause: false,
				// Get the starting slide
				start: startSlide,
				animationComplete: function(current){
					// Set the slide number as a hash
					window.location.hash = '#' + current;
					
				}
			});
			
		
		
	});
</script>

<script type="text/javascript">

$(function () {

	// create the chart
    var chart = new Highcharts.Chart({
        chart: { renderTo: 'totallistingsbymarket',type:'line',backgroundColor: '#F7F5F0' },
		loading: { labelStyle: {color: 'white',top: '45%'},style: {backgroundColor: 'gray'}},
		title: { text: ' '},
		plotOptions: {
            series: {
                marker: {
                    enabled: false,
					lineColor: null,
                    states: {
                        hover: {
                            enabled: true,
							fillColor: 'white',
							lineWidth: 2
                        }
                    }
                }
            }
        },
        xAxis: {
			title: { text: '' },
			type: 'datetime',
		  	labels: {
				formatter: function() {
					return Highcharts.dateFormat('%b %d,%Y', this.value);
				}
			}
			,tickInterval:2 * 24 * 3600 * 1000				
        },
		yAxis: {
			title: { text: '' },
			min:0
		},
		tooltip: {
			 formatter: function() {
				  return '<b>'+ this.series.name +'</b><br/>Total Listings in '+ Highcharts.dateFormat('%b %d,%Y', this.x) +'='+ this.y +'';
			 }
		  },

        series: [
			<?php
			
					$newOptions = array();
					
					$totallistingsbymarketValue = $totallistingsbymarket;
					
					$countData = count($totallistingsbymarketValue);
					if($countData != 0){
						foreach($totallistingsbymarketValue as $value){
							$region = $value->region_name;
							$day_complete = $value->day_completed;
							$total_listings = $value->total_listings;	
							if(!empty($day_complete)){
								$newOptions[$region][$day_complete] = $total_listings; // re array by region
							}
							
						}
					}
					
					$countRegion = count($newOptions);
					$i = 0;
					foreach($newOptions as $keyRegion => $valueRegion){
						$i = $i + 1;
						echo"{";
							$countListings = count($valueRegion);
							echo"name:'".$keyRegion."',";
							echo"data: [";
							$a = 0;
								foreach($valueRegion as $keyDate => $valueListings ){
									$a = $a + 1;
									
									$arrayValues = explode("-",$keyDate);
									$getmonth = $arrayValues[1] - 1;
									$reindex = $arrayValues[0].",".$getmonth.",".$arrayValues[2];
									
									echo "[Date.UTC(".$reindex."),".$valueListings."]"; 
									
									if($a != $countListings){ echo",";}			
								}
								$a++;
							echo"]";
						echo"}";
						
						if($i != $countRegion){ echo",";}
						
					}
					
					$i++;
			
			?>
		]
		
    }); // end new Highcharts
});// end function


</script>

<script type="text/javascript">

$(function () {

	// create the chart
    var chart = new Highcharts.Chart({
        chart: { renderTo: 'dataimportovertime',type:'line', backgroundColor: '#F7F5F0'  },
		loading: {
            labelStyle: { color: 'white',top: '45%' },
            style: {  backgroundColor: 'gray' }
        },
		title: { text: ' ' },
        xAxis: {
			title: {  text: '' },
			type : 'datetime',
			labels: {
				formatter: function() {	
				 return Highcharts.dateFormat('%b %Y', this.value); 
				}
			}
			,tickInterval:210 * 24 * 3600 * 1000	
        },
		yAxis: {
			title: { text: '' },
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
					
							$dataimportovertimeValue = $dataimportovertime;
							$countData2 = count($dataimportovertimeValue);
							
							if($countData2 != 0){
								$i = 0;
								foreach($dataimportovertimeValue as $value){
									$i = $i + 1;
									$arrayValues = explode("-",$value->createdon);
									$getmonth = $arrayValues[1] - 1;
									$reindex = $arrayValues[0].",".$getmonth.",01";
									
									echo "[Date.UTC(".$reindex."),".$value->countoffice."]";

									if($i != $countData2){ echo",";}
									
								}
								$i++;
							}
					?>
				  ]
		}]
		
    }); // end new Highcharts
});// end function

</script>


<script type="text/javascript">

$(function () {

	// create the chart
    var chart = new Highcharts.Chart({
       chart: { renderTo: 'dailynewtickets',type:'line', backgroundColor: '#F7F5F0'  },
		loading: {
            labelStyle: { color: 'white', top: '45%' },
            style: {  backgroundColor: 'gray'  }
        },
		title: {  text: ' ' },
		plotOptions: {
            series: {
                marker: {
                    enabled: false,
					lineColor: null,
                    states: {
                        hover: {
                            enabled: true,
							fillColor: 'white',
							lineWidth: 2
                        }
                    }
                }
            }
        },
        xAxis: {
			title: { text: '' },
			type : 'datetime',
			labels: {
				formatter: function() {
					return Highcharts.dateFormat('%b %d ,%Y', this.value);
				}
			}
			,tickInterval: 24 * 3600 * 1000	
        },
		yAxis: {
			title: { text: '' },
			min:0
		},
		tooltip: {
			  formatter: function() {
					return '<b>'+ this.series.name +'</b><br/>'+
				    Highcharts.dateFormat('%B %d - %A', this.x) +' = '+ this.y;
			 }
		  },

        series: [{
			name:'Daily Tickets',
            data: [
					<?php
					
							$dailynewticketsValue = $dailynewtickets;
							$countData3 = count($dailynewticketsValue);
							
							if($countData3 != 0){
								$i = 0;
								foreach($dailynewticketsValue as $value){
									$i = $i + 1;
									$arrayValues = explode("-",$value->created_at);
									$getmonth = $arrayValues[1] - 1;
									$reindex = $arrayValues[0].",".$getmonth.",".$arrayValues[2];
									
									echo "[Date.UTC(".$reindex."),".$value->average_tickets."]";

									if($i != $countData3){ echo",";}
									
								}
								$i++;
							}
					?>
				  ]
		}]
		
    }); // end new Highcharts
});// end function

</script>


<script type="text/javascript">

$(function () {

	// create the chart
    var chart = new Highcharts.Chart({
        chart: { renderTo: 'resolvedbyagent', type:'area', backgroundColor: '#F7F5F0' },
		loading: { labelStyle: {color: 'white',top: '45%'}, style: {backgroundColor: 'gray'} },
		title: { text: ' ' },
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
		legend: {
			
			layout: 'vertical',
			align: 'right',
			verticalAlign: 'top',
			x: 0,
			y: 30,
			backgroundColor: '#FFFFFF',
			borderWidth: 1
		},
        xAxis: {
			title: { text: '' },
			type: 'datetime',
		  	labels: { formatter: function() { return Highcharts.dateFormat('%b %d,%Y', this.value); } }
			, tickInterval:24 * 3600 * 1000

        },
		yAxis: {
			title: { text: '' },
			min:0
			
		},
		tooltip: {
			 formatter: function() {  return '<b>'+ this.series.name +'</b><br/>Total Counts for Tickets in '+ Highcharts.dateFormat('%b %d,%Y', this.x) +'='+ this.y +'';  }
		  },

        series: [
			<?php
			
					$newOptions = array();
					
					$resolvedbyagentValue = $resolvedbyagent;
					
					$countData4 = count($resolvedbyagentValue);
					if($countData4 != 0){
						
						foreach($resolvedbyagentValue as $value){
							$username = trim($value->username);
							$resolved = trim($value->resolved_at);
							$countdata = trim($value->ticket_count);
							if(!empty($resolved)){
								$newOptions[$username][$resolved] =  $countdata; // re array by region
							}
							
						}
					}
					
					$countUser = count($newOptions);
					$i = 0;
					
					$result = array_map('array_sum', $newOptions);
					arsort($result);
					
					$result2 = array_merge($result,  $newOptions);
					
					foreach($result2 as $keyUser => $valueUser){
					
						if(array_sum($valueUser) != 0){
						$i = $i + 1;
						echo"{";
							$countListings = count($valueUser);
							echo"name:'".$keyUser."',";
							echo"data: [";
							$a = 0;
								foreach($valueUser as $keyDate => $valueListings ){
									$a = $a + 1;
									
									$arrayValues = explode("-",$keyDate);
									$getmonth = $arrayValues[1] - 1;
									$reindex = $arrayValues[0].",".$getmonth.",".$arrayValues[2];
									
									echo "[Date.UTC(".$reindex."),". round($valueListings,1)."]"; 
									
									if($a != $countListings){ echo",";}			
								}
								$a++;
							echo"]";
						echo"}";
						
						if($i != $countUser){ echo",";}
						}
						
					}
					$i++;
			?>
		]
		
    }); // end new Highcharts
});// end function


</script>


<script type="text/javascript">

$(function () {

    var chart = new Highcharts.Chart({
        chart: { renderTo: 'tagusageovertime',type:'line',backgroundColor: '#F7F5F0' },
		loading: { labelStyle: {color: 'white',top: '45%'},style: {backgroundColor: 'gray'} },
		title: { text: ' ' },
		plotOptions: {
            series: {
                marker: {
                    enabled: false,
					lineColor: null,
                    states: {
                        hover: {
                            enabled: true,
							fillColor: 'white',
							lineWidth: 2
                        }
                    }
                }
            }
        },
        xAxis: {
			title: { text: '' },
			type : 'datetime',
			labels: {
				formatter: function() {
					return Highcharts.dateFormat('%b %d, %Y', this.value);
				}
			}
			, tickInterval:5 * 24 * 3600 * 1000
        },
		yAxis: {
			title: { text: '' },
			min:0,
			allowDecimals: false
		},
		tooltip: {
			 formatter: function() {
				  return '<b>'+ this.series.name +'</b><br/>'+
				  Highcharts.dateFormat('%B %d,%Y', this.x) +' = '+ this.y;
			 }
		  },

        series: [{
			name:'Tag: Data Entry',
            data: [
					<?php
						$tagusageovertimeValue = $tagusageovertime;
						$countData5 = count($tagusageovertimeValue);
						if($countData5 != 0){
							$i = 0;
							foreach($tagusageovertimeValue as $value){
								$i = $i + 1;
								$arrayValues = explode("-",$value->created_at);
								$getmonth = $arrayValues[1] - 1;
								$reindex = $arrayValues[0].",".$getmonth.",".$arrayValues[2];
								echo "[Date.UTC(".$reindex."),".round($value->ticket_count,1)."]";
								if($i != $countData5){ echo",";}
							}
							$i++;
						}	
					?>
				  ]
		}]
    }); // end new Highcharts
});// end function

</script>

<script type="text/javascript">

$(function () {

    var chart = new Highcharts.Chart({
        chart: { renderTo: 'tagusageovertimebug',type:'line',backgroundColor: '#F7F5F0' },
		loading: { labelStyle: {color: 'white',top: '45%'},style: {backgroundColor: 'gray'} },
		title: { text: ' ' },
		plotOptions: {
            series: {
                marker: {
                    enabled: false,
					lineColor: null,
                    states: {
                        hover: {
                            enabled: true,
							fillColor: 'white',
							lineWidth: 2
                        }
                    }
                }
            }
        },
        xAxis: {
			title: { text: '' },
			type : 'datetime',
			labels: {
				formatter: function() {
					return Highcharts.dateFormat('%b %d, %Y', this.value);
				}
			}
			, tickInterval:5 * 24 * 3600 * 1000
        },
		yAxis: {
			title: { text: '' },
			min:0,
			allowDecimals: false
		},
		tooltip: {
			 formatter: function() {
				  return '<b>'+ this.series.name +'</b><br/>'+
				  Highcharts.dateFormat('%B %d,%Y', this.x) +' = '+ this.y;
			 }
		  },

        series: [{
			name:'Tag: Bug',
            data: [
					<?php
						$tagusageovertimebugValue = $tagusageovertimebug;
						$countData6 = count($tagusageovertimebugValue);
						if($countData6 != 0){
							$i = 0;
							foreach($tagusageovertimebugValue as $value){
								$i = $i + 1;
								$arrayValues = explode("-",$value->created_at);
								$getmonth = $arrayValues[1] - 1;
								$reindex = $arrayValues[0].",".$getmonth.",".$arrayValues[2];
								echo "[Date.UTC(".$reindex."),".round($value->ticket_count,1)."]";
								if($i != $countData6){ echo",";}
							}
							$i++;
						}	
					?>
				  ]
		}]
    }); // end new Highcharts
});// end function

</script>

<script type="text/javascript">

$(function () {

    var chart = new Highcharts.Chart({
        chart: { renderTo: 'tagusageovertimecraiglist',type:'line',backgroundColor: '#F7F5F0' },
		loading: { labelStyle: {color: 'white',top: '45%'},style: {backgroundColor: 'gray'} },
		title: { text: ' ' },
		plotOptions: {
            series: {
                marker: {
                    enabled: false,
					lineColor: null,
                    states: {
                        hover: {
                            enabled: true,
							fillColor: 'white',
							lineWidth: 2
                        }
                    }
                }
            }
        },
        xAxis: {
			title: { text: '' },
			type : 'datetime',
			labels: {
				formatter: function() {
					return Highcharts.dateFormat('%b %d, %Y', this.value);
				}
			}
			, tickInterval:5 * 24 * 3600 * 1000
        },
		yAxis: {
			title: { text: '' },
			min:0,
			allowDecimals: false
		},
		tooltip: {
			 formatter: function() {
				  return '<b>'+ this.series.name +'</b><br/>'+
				  Highcharts.dateFormat('%B %d,%Y', this.x) +' = '+ this.y;
			 }
		  },

        series: [{
			name:'Tag: Craiglist',
            data: [
					<?php
						$tagusageovertimecraiglistValue = $tagusageovertimecraiglist;
						$countData7 = count($tagusageovertimecraiglistValue);
						if($countData7 != 0){
							$i = 0;
							foreach($tagusageovertimecraiglistValue as $value){
								$i = $i + 1;
								$arrayValues = explode("-",$value->created_at);
								$getmonth = $arrayValues[1] - 1;
								$reindex = $arrayValues[0].",".$getmonth.",".$arrayValues[2];
								echo "[Date.UTC(".$reindex."),".round($value->ticket_count,1)."]";
								if($i != $countData7){ echo",";}
							}
							$i++;
						}	
					?>
				  ]
		}]
    }); // end new Highcharts
});// end function

</script>

<script type="text/javascript">

$(function () {

    var chart = new Highcharts.Chart({
        chart: { renderTo: 'tagusageovertimesyndication',type:'line',backgroundColor: '#F7F5F0' },
		loading: { labelStyle: {color: 'white',top: '45%'},style: {backgroundColor: 'gray'} },
		title: { text: ' ' },
		plotOptions: {
            series: {
                marker: {
                    enabled: false,
					lineColor: null,
                    states: {
                        hover: {
                            enabled: true,
							fillColor: 'white',
							lineWidth: 2
                        }
                    }
                }
            }
        },
        xAxis: {
			title: { text: '' },
			type : 'datetime',
			labels: {
				formatter: function() {
					return Highcharts.dateFormat('%b %d, %Y', this.value);
				}
			}
			, tickInterval:5 * 24 * 3600 * 1000
        },
		yAxis: {
			title: { text: '' },
			min:0,
			allowDecimals: false
		},
		tooltip: {
			 formatter: function() {
				  return '<b>'+ this.series.name +'</b><br/>'+
				  Highcharts.dateFormat('%B %d,%Y', this.x) +' = '+ this.y;
			 }
		  },

        series: [{
			name:'Tag: Syndication',
            data: [
					<?php
						$tagusageovertimesyndicationValue = $tagusageovertimesyndication;
						$countData8 = count($tagusageovertimesyndicationValue);
						if($countData8 != 0){
							$i = 0;
							foreach($tagusageovertimesyndicationValue as $value){
								$i = $i + 1;
								$arrayValues = explode("-",$value->created_at);
								$getmonth = $arrayValues[1] - 1;
								$reindex = $arrayValues[0].",".$getmonth.",".$arrayValues[2];
								echo "[Date.UTC(".$reindex."),".round($value->ticket_count,1)."]";
								if($i != $countData8){ echo",";}
							}
							$i++;
						}	
					?>
				  ]
		}]
    }); // end new Highcharts
});// end function

</script>

<script type="text/javascript">

$(function () {

    var chart = new Highcharts.Chart({
        chart: { renderTo: 'tagusageovertimeonboarding',type:'line',backgroundColor: '#F7F5F0' },
		loading: { labelStyle: {color: 'white',top: '45%'},style: {backgroundColor: 'gray'} },
		title: { text: ' ' },
		plotOptions: {
            series: {
                marker: {
                    enabled: false,
					lineColor: null,
                    states: {
                        hover: {
                            enabled: true,
							fillColor: 'white',
							lineWidth: 2
                        }
                    }
                }
            }
        },
        xAxis: {
			title: { text: '' },
			type : 'datetime',
			labels: {
				formatter: function() {
					return Highcharts.dateFormat('%b %d, %Y', this.value);
				}
			}
			, tickInterval:5 * 24 * 3600 * 1000
        },
		yAxis: {
			title: { text: '' },
			min:0,
			allowDecimals: false
		},
		tooltip: {
			 formatter: function() {
				  return '<b>'+ this.series.name +'</b><br/>'+
				  Highcharts.dateFormat('%B %d,%Y', this.x) +' = '+ this.y;
			 }
		  },

        series: [{
			name:'Tag: Syndication',
            data: [
					<?php
						$tagusageovertimeonboardingValue = $tagusageovertimeonboarding;
						$countData9 = count($tagusageovertimeonboardingValue);
						if($countData9 != 0){
							$i = 0;
							foreach($tagusageovertimeonboardingValue as $value){
								$i = $i + 1;
								$arrayValues = explode("-",$value->created_at);
								$getmonth = $arrayValues[1] - 1;
								$reindex = $arrayValues[0].",".$getmonth.",".$arrayValues[2];
								echo "[Date.UTC(".$reindex."),".round($value->ticket_count,1)."]";
								if($i != $countData9){ echo",";}
							}
							$i++;
						}	
					?>
				  ]
		}]
    }); // end new Highcharts
});// end function

</script>
<script type="text/javascript" charset="utf-8">
		$(document).ready(function() {
			$('#tablegrid').dataTable( {
				"bJQueryUI": true,
				"sPaginationType": "full_numbers",
				"bSort":false,
				"iDisplayLength": 100
			} );
		} );
</script>

<?php
	/*echo"<pre>";
	print_r($tagusageovertime);
	echo"</pre>";*/
	
?>

	<div id="container_slides">
	
			<div id="slides">
				<div class="slides_container">
					<div class="slide">
						<div class="manualmerges_title align_center">Data Entry Volume by Market</div>
						<div id="totallistingsbymarket"  class="graph_container container_border"></div>
						<div class="light_grey font10">Parameters: totallistings_time=1</div>
						<div class="light_grey font10">values by days ('1'=>'7','2'=>'14','3'=>'21','4'=>'28','5'=>'30','6'=>'60','7'=>'90','alltime'=>'alltime')</div>
					</div>
					<div class="slide">
						<div class="manualmerges_title align_center">Data Imports Over Time</div>
						<div class="align_center">Data Import over time. We assume a data import has occurred if there is at least one listing with an import_reference_id</div>
						<br />
						<div id="dataimportovertime"  class="graph_container container_border"></div>
					</div>
					<div class="slide">
						<div class="manualmerges_title align_center">Daily New Tickets</div>
						<div id="dailynewtickets" class="graph_container container_border"></div>
						<div class="light_grey font10">Parameters: time_frame=7</div>
						<div class="light_grey font10">values by days (7,14')</div>
					</div>
					
					<div class="slide">
						<div class="manualmerges_title align_center">Resolved by Agent</div>
						<div id="resolvedbyagent" class="graph_container container_border"></div>
						<div class="light_grey font10">Parameters: timespan=7,movingaverage=1,type=1,tags=alltags</div>
						<div class="light_grey font10">timespan: values by days ('1'=>'7','2'=>'14','3'=>'21','4'=>'28','5'=>'30','6'=>'60','7'=>'90','8'=>'120','9'=>'150','10'=>'180','11'=>'360','12'=>'720')</div>
						<div class="light_grey font10">type ('1'=>'alltickets','2'=>'email','3'=>'phone call','4'=>'chat','5'=>'tweet','6'=>'qna')</div>

						
					</div>
					<div class="slide">
						<div class="manualmerges_title align_center">Tag: Data Entry</div>
						<div id="tagusageovertime" class="graph_container container_border"></div>
						<div class="light_grey font10">Parameters: tagusage_time=4,tagusage_label=Data+Entry,moving_average=1</div>
						<div class="light_grey font10">tagusage_time: values by days('1'=>'7','2'=>'14','3'=>'21','4'=>'28','5'=>'30','6'=>'60','7'=>'90','8'=>'120','9'=>'150','10'=>'180','11'=>'360','12'=>'720');</div>
					</div>
					<div class="slide">
						<div class="manualmerges_title align_center">Tag: Bug</div>
						<div id="tagusageovertimebug"  class="graph_container container_border"></div>
						<div class="light_grey font10">Parameters: tagusage_time=4,tagusage_label=Bug,moving_average=1</div>
						<div class="light_grey font10">tagusage_time: values by days('1'=>'7','2'=>'14','3'=>'21','4'=>'28','5'=>'30','6'=>'60','7'=>'90','8'=>'120','9'=>'150','10'=>'180','11'=>'360','12'=>'720');</div>
					</div>
					<div class="slide">
						<div class="manualmerges_title align_center">Tag: Craigslist</div>
						<div id="tagusageovertimecraiglist" class="graph_container container_border"></div>
						<div class="light_grey font10">Parameters: tagusage_time=4,tagusage_label=Craiglist,moving_average=1</div>
						<div class="light_grey font10">tagusage_time: values by days('1'=>'7','2'=>'14','3'=>'21','4'=>'28','5'=>'30','6'=>'60','7'=>'90','8'=>'120','9'=>'150','10'=>'180','11'=>'360','12'=>'720');</div>
					</div>
					<div class="slide">
						<div class="manualmerges_title align_center">Tag: Syndication</div>
						<div id="tagusageovertimesyndication" class="graph_container container_border"></div>
						<div class="light_grey font10">Parameters: tagusage_time=4,tagusage_label=Syndication,moving_average=1</div>
						<div class="light_grey font10">tagusage_time: values by days('1'=>'7','2'=>'14','3'=>'21','4'=>'28','5'=>'30','6'=>'60','7'=>'90','8'=>'120','9'=>'150','10'=>'180','11'=>'360','12'=>'720');</div>
					</div>
					<div class="slide">
						<div class="manualmerges_title align_center">Tag: Onboarding</div>
						<div id="tagusageovertimeonboarding" class="graph_container container_border"></div>
						<div class="light_grey font10">Parameters: tagusage_time=4,tagusage_label=Onboarding,moving_average=1</div>
						<div class="light_grey font10">tagusage_time: values by days('1'=>'7','2'=>'14','3'=>'21','4'=>'28','5'=>'30','6'=>'60','7'=>'90','8'=>'120','9'=>'150','10'=>'180','11'=>'360','12'=>'720');</div>
					</div>
					<div class="slide">
						<div class="manualmerges_title align_center">Onboarding Tickets by Status</div>
						<table cellpadding="0" cellspacing="0" border="0" class="display" id="tablegrid" width="100%">
						<thead>
							<tr>
								<th><div class="bold">Tag Name</div></th>
								<th><div class="bold">New</div></th>
								<th><div class="bold">Open</div></th>
								<th><div class="bold">Pending</div></th>
								<th><div class="bold">Resolved</div></th>
							</tr>
						</thead>
						<tbody>
						
						<?php
								$onboardingticketsbystatusValue = $onboardingticketsbystatus;
								$countData10 = count($onboardingticketsbystatusValue);
								if($countData10 != 0){
									$i = 0;
									foreach($onboardingticketsbystatusValue as $value){
									$i = $i + 1;
									 if($i%2 == 0){ $class = 'even';  }
									 else{ $class = 'odd'; }
					
									?>
										<tr class="<?php echo $class; ?>">
											<td style="border-left:1px solid #cccccc;"><?php echo $value->label_name; ?></td>
											<td class="center"  style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; "><?php if($value->new != 0){ echo $value->new; } ?></td>
											<td class="center"  style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; "><?php if($value->open != 0){ echo $value->open; } ?></td>
											<td class="center"  style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; "><?php if($value->pending != 0){ echo $value->pending; } ?></td>
											<td class="center"  style="border-left:1px solid #cccccc;border-right:1px solid #cccccc; "><?php if($value->resolved != 0){ echo $value->resolved; } ?></td>
										</tr>
									<?php
									}
									$i++;
								}
							?>
					
						</tbody>
						<tfoot>
					</table>
						<div style="margin-top:10px;" class="light_grey font10">Parameters: onboarding_tickets=alltime</div>
					</div>
					
				</div>
				<a href="#" class="prev"><img src="assets/images/arrow-prev.png" width="24" height="43" alt="Arrow Prev"></a>
				<a href="#" class="next"><img src="assets/images/arrow-next.png" width="24" height="43" alt="Arrow Next"></a>
			</div>
			<!--<img src="img/example-frame.png" width="739" height="341" alt="Example Frame" id="frame">-->
			
			<div class="rotating_reports_botttom group">
				<div class="left light_grey font10">
					<div>Graph Parameters: display=3, refresh=1</div>
					<div>display | values in milliseconds('1'=>'5000', '2'=>'10000', '3'=>'15000', '4'=>'20000', '5'=>'25000', '6'=>'30000')</div>
					<div>refresh | values in minutes('1'=>'15', '2'=>'30', '3'=>'45', '4'=>'60')</div>
				</div>
				<div class="right">
					<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="form_select" id="form_submit" >
						<div class="align_right">
							<input type="hidden" value="rotatingoperations" name="page" />
							<span>Display Graphs every : </span>
							<select name="display" onchange="submit();" class="display_graph">
								<option value="1" <?php if($displayTime == '5000'){ echo 'selected';} else{ echo''; } ?>>5 Seconds</option>
								<option value="2" <?php if($displayTime == '10000'){ echo 'selected';} else{ echo''; } ?>>10 Seconds</option>
								<option value="3" <?php if($displayTime == '15000'){ echo 'selected';} else{ echo''; } ?>>15 Seconds</option>
								<option value="4" <?php if($displayTime == '20000'){ echo 'selected';} else{ echo''; } ?>>20 Seconds</option>
								<option value="5" <?php if($displayTime == '25000'){ echo 'selected';} else{ echo''; } ?>>25 Seconds</option>
								<option value="6" <?php if($displayTime == '30000'){ echo 'selected';} else{ echo''; } ?>>30 Seconds</option>
							</select>
							&nbsp;&nbsp;&nbsp;
							<span>Refresh Graphs every : </span>
							<select name="refresh" onchange="submit();" class="refresh_graph">
								<option value="1" <?php if($displayRefresh == '15'){ echo 'selected';} else{ echo''; } ?>>15 Minutes</option>
								<option value="2" <?php if($displayRefresh == '30'){ echo 'selected';} else{ echo''; } ?>>30 Minutes</option>
								<option value="3" <?php if($displayRefresh == '45'){ echo 'selected';} else{ echo''; } ?>>45 Minutes</option>
								<option value="4" <?php if($displayRefresh == '60'){ echo 'selected';} else{ echo''; } ?>>60 Minutes</option>
							</select>
						</div>
					</form>
					<!--<div id="number_timer" style="font-size:30px; font-weight:bold; color:#999999;"></div>-->
				</div>
				
			</div>
			
	
			
		</div> <!-- end container slides -->
		
			
</div> <!-- end body wrapper -->
<?php 
	$start = array('start_time'=>$start_time,'start_width'=>'wide');
	render('_footer',$start)
?>

