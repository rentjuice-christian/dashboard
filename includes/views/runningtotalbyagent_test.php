<?php render('_header')?>
<?php
	
	$arrayDate = array('1'=>'24','2'=>'48','3'=>'72','4'=>'96','5'=>'120','6'=>'144','7'=>'168');
	
	if(isset($_REQUEST['timespan'])){ 
		if (!empty($_REQUEST['timespan'])){
			if (array_key_exists($_REQUEST['timespan'],$arrayDate)){
				$timeFrame = $_REQUEST['timespan'];
			}
			else{ $timeFrame = '4'; }
		}
		else{ $timeFrame = '4'; }
	}
	else{ $timeFrame = '4'; }
	
	
?>

<div class="body-wrapper">
		<div class="" style="width:87%; margin:0 auto;">
			<div class="main_content_temp">

<script type="text/javascript">

$(document).ready(function(){


var jsonURL = 'index.php?page=runningtotalbyagent_json&timespan=<?php echo $timeFrame; ?>';
var chart;
var options ;

function function_name() {

	var count = 15,
    countdown = setInterval(function () {
        $(".countdown").html(" Refresh in "+ count + " seconds ");
        if (count == 0) {
            count = 16; //since it will be reduced right after this
            //clearInterval(countdown); <-- use this if you want to stop 
            //alert('done');
			$(".querying").fadeIn();	
        }
		if (count == 15) { }
		if (count == 13) { $(".querying").fadeOut(); }
		
        count--;
    }, 1000);

   $.getJSON(jsonURL, function(data) {   
		 	   
		$.each(data, function(key,value) {
			var series = { data: []};
			$.each(value, function(key,val) {
				if (key == 'name') {
					series.name = val;
				}
				else
				{
					$.each(val, function(key2,val2) {
						var d = key2.split(",");
						var c =  parseInt(val2);
						var x = Date.UTC(d[0],d[1],d[2],d[3]);
						series.data.push([x,c]);
						
					});
				}
			});
			options.series.push(series);
		});
		
		var chart = new Highcharts.Chart(options);
		//$('#spinner').css("display","none");
		
	}); // getJSON
	
   options = {
        chart: {
            renderTo: 'container',
			type:'line',
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
			title: {
				text: ''
			},
			type: 'datetime',
			dateTimeLabelFormats: {
				minute: '%l %p',
				hour: '%l %p',
				day: '%m/%e',
				week: '%m/%e',
				month: '%b \'%y',
				year: '%Y'
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
				  var s = '<b>'+ this.series.name +'</b><br/>Running Ticket Count<br/>'+ Highcharts.dateFormat('%b %d,%Y - %I %p', this.x) +'='+ addCommas(this.y) +'';
				  return s;
			 }
		  },

      series: []
		
    }; // end new Highcharts
	
	
	//$('#spinner').show();
	
		
} // end function

	function_name();

	setInterval(function_name, 16000);
	

});// end function


</script>

<div id="test"></div>

<div class="align_center">
	<div class="manualmerges_title">Running Total by Agent</div>
</div>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="form_select" id="form_submit" >
	
	<div class="align_right">
		<input type="hidden" value="runningtotalbyagent_test" name="page" />
	<span>Select Time Frame: </span>
		<select name="timespan" onchange="submit();" class="select_time">
			<option value="1" <?php if($timeFrame == '1'){ echo'selected'; }else{ echo''; }  ?>>1 Day</option>
			<option value="2" <?php if($timeFrame == '2'){ echo'selected'; }else{ echo''; }  ?>>2 Days</option>
			<option value="3" <?php if($timeFrame == '3'){ echo'selected'; }else{ echo''; }  ?>>3 Days</option>
			<option value="4" <?php if($timeFrame == '4'){ echo'selected'; }else{ echo''; }  ?>>4 Days</option>
			<option value="5" <?php if($timeFrame == '5'){ echo'selected'; }else{ echo''; }  ?>>5 Days</option>
			<option value="6" <?php if($timeFrame == '6'){ echo'selected'; }else{ echo''; }  ?>>6 Days</option>
			<option value="7" <?php if($timeFrame == '7'){ echo'selected'; }else{ echo''; }  ?>>7 Days</option>
		</select>

	</div>
</form>

<?php if(count($barcontent) == 0){ ?>
	<div class="content_noavail">No Available Data</div>
<?php }else{ ?>
	<div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
<?php } ?>
<div class="align_right show_query ">
	<div class="right"><a class='inline' href="#inline_content"><img src="assets/images/show_query.png" alt="show query" /></a></div>
	<div class="left">
		<span class="countdown"></span>
		<span class="querying" style="display:none;">&nbsp;&nbsp;&nbsp;&nbsp;(Querying most recent data)</span>
	</div>
	<div class="clear"></div>
</div>
<div style="display:none">
<div id='inline_content' style='padding:10px; background:#fff;'>
<?php

	if(isset($_REQUEST['timespan'])){ 
		if (!empty($_REQUEST['timespan'])){
			if (array_key_exists($_REQUEST['timespan'],$arrayDate)){
				$timeSpan = $arrayDate[$_REQUEST['timespan']];
			}
			else{ $timeSpan = '96'; }
		}
		else{ $timeSpan = '96'; }
	}
	else{ $timeSpan = '96'; }

?>
<pre>
SELECT resolved_at,
       username,
       CASE
           WHEN username = "Simon Kwan" AND HOUR(resolved_at) = 0 THEN (@simon_running_total := 0)    
           WHEN username = "Phillip Spiegel" AND HOUR(resolved_at) = 0 THEN (@phillip_running_total := 0)    
           WHEN username = "Kevin Burriss" AND HOUR(resolved_at) = 0 THEN (@kevin_running_total := 0)    
           WHEN username = "Eric Montero" AND HOUR(resolved_at) = 0 THEN (@eric_running_total := 0)    
           WHEN username = "Jennifer Wong" AND HOUR(resolved_at) = 0 THEN (@jen_running_total := 0)    
           WHEN username = "Ian Long" AND HOUR(resolved_at) = 0 THEN (@ian_running_total := 0)                            
       END AS daily_reset,
       CASE 
           WHEN username = "Simon Kwan" THEN (@simon_running_total := @simon_running_total + ticket_count)
           WHEN username = "Phillip Spiegel" THEN (@phillip_running_total := @phillip_running_total + ticket_count)
           WHEN username = "Kevin Burriss" THEN (@kevin_running_total := @kevin_running_total + ticket_count)
           WHEN username = "Eric Montero" THEN (@eric_running_total := @eric_running_total + ticket_count)
           WHEN username = "Jennifer Wong" THEN (@jen_running_total := @jen_running_total + ticket_count)
           WHEN username = "Ian Long" THEN (@ian_running_total := @ian_running_total + ticket_count)
           ELSE 0
       END AS running_ticket_count
  FROM 
       (SELECT user_hours.username, user_hours.resolved_at, IFNULL(cases.ticket_count,0) ticket_count
          FROM 
               (SELECT *
                  FROM
                       (SELECT username
                          FROM janak.assistly_cases
                         WHERE username IS NOT NULL
                           AND DATE_SUB(SYSDATE(), INTERVAL 168 HOUR) <= resolved_at
                           AND username IN ('Simon Kwan','Phillip Spiegel','Kevin Burriss','Eric Montero','Jennifer Wong','Ian Long')
                         GROUP BY 1
                       ) users,
                       (SELECT DATE_SUB(DATE_FORMAT(SYSDATE(), '%Y-%m-%d %h:00:00'), INTERVAL id HOUR) resolved_at
                          FROM janak.counter_records
                         WHERE id <= <?php echo $timeSpan; ?> 
                       ) hours
               ) user_hours 
               LEFT OUTER JOIN 
               (SELECT username, DATE_FORMAT(resolved_at, '%Y-%m-%d %h:00:00') resolved_at, COUNT(*) ticket_count
                  FROM janak.assistly_cases ca
                 WHERE DATE_SUB(SYSDATE(), INTERVAL <?php echo $timeSpan; ?> HOUR) <= ca.resolved_at
                 GROUP BY 1,2
                 ORDER BY 2
               ) cases
               ON user_hours.resolved_at = cases.resolved_at AND 
                  user_hours.username = cases.username 
         ORDER BY user_hours.resolved_at
       ) tickets, 
       (
        SELECT @simon_running_total := 0,
               @phillip_running_total := 0,
               @kevin_running_total := 0,
               @eric_running_total := 0,
               @jen_running_total := 0,
               @ian_running_total := 0
       ) dummy


</pre>
</div>
</div>

</div>
</div>
</div>
<?php 
	$start = array('start_time'=>$start_time,'start_width'=>'wide');
	render('_footer',$start)
?>

