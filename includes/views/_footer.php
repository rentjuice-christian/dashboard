	<div class="report_run">
		<?php 
			if(!empty($start_width)){
				
				if($start_width == 'wide'){
				
		?>
				<div class="" style="width:87%; margin:0 auto;">
		<?php
				}
				else{
		?>
				<div style="width:<?php echo $start_width; ?>; margin:0 auto;">
		<?php
				}
			}
			else{
		?>
		<div class="centered">
		<?php
			}
		?>
		<?php
		
			if(!empty($start_time) || $start_time != 0 ){
				$time = microtime();
				$time = explode(" ", $time);
				$time = $time[1] + $time[0];
				$finish = $time;
				$total = ($finish - $start_time);
				$totaltime = round($total,0);
				
				if($totaltime > 60){ echo "This report took ".gmdate ('i:s', $totaltime )." minutes to load."; }
				else{  echo "This report took ".($totaltime + 3)." seconds to load.";	}
			}
		
		?>
		<div>
			<?php
				if(!empty($GLOBALS["dbsource"])){
			?>
				This report generated from <?php echo $GLOBALS["dbsource"]; ?>.
			<?php
				}
			?>
		</div>
		</div>
		
	</div>
	<div class="footer-wrapper">
		<div class="centered">
			<div class="align_center">

				Yahoo!-Zillow Real Estate Network &copy; 2006-2012 Zillow
			
			</div>
		</div>
	</div>
	
</div><!-- end main template wrapper -->



</body></html>