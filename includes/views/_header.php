<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title> Rentjuice - Dashboard </title>
<link type="text/css" href="assets/css/mega_menu.css" rel="stylesheet" />
<link type="text/css" href="assets/css/styles.css" rel="stylesheet" />
<link type="text/css" href="assets/css/colorbox.css" rel="stylesheet" />
<link type="text/css" href="assets/css/jquery.dataTables_themeroller.css" rel="stylesheet" />
<link type="text/css" href="assets/css/jquery-ui-1.8.4.custom.css" rel="stylesheet" />
<script type="text/javascript" src="assets/js/jquery.js"></script>
<script src="assets/js/highcharts.js"></script>
<script src="assets/js/exporting.js"></script>
<script src="assets/js/jquery.hoverintent.js"></script>
<script src="assets/js/megamenu.js"></script>
<script src="assets/js/jquery.colorbox-min.js"></script>
<script src="assets/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/js/jquery.qtip-1.0.0-rc3.min.js"></script>
<script src="http://code.jquery.com/ui/1.9.0/jquery-ui.js"></script>
<script>
	jQuery(document).ready(function(){
	
		jQuery(".inline").colorbox({inline:true, width:"80%", height:"80%"});
	  	jQuery(".inline_notes").colorbox({inline:true, width:"80%", height:"80%"});

		// Clicking the menu
		$("#topnav a.topnav").click(function() {
		  	
			jQuery('#cboxOverlay').css("display","block");
			jQuery('#cboxOverlay').css("opacity","0.7");
			jQuery('#spinner').show();
			
		});
		
		$("#form_submit select").change(function() {
		  	
			jQuery('#cboxOverlay').css("display","block");
			jQuery('#cboxOverlay').css("opacity","0.7");
			jQuery('#spinner').css("display","block");
			
		});

			
	});
	

	jQuery(window).load(function() { 
	
		jQuery('#cboxOverlay').css("display","none");
		jQuery('#spinner').css("display","none");
	
	}); 
	
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
</script>
<style>
	.spinner {
	    position: fixed;
	    top: 50%;
	    left: 50%;
	    /*margin-left: -50px;*/ /* half width of the spinner gif */
		margin-left: -88px;
	    margin-top: -50px; /* half height of the spinner gif */
	    text-align:center;
	    z-index:1234;
	    overflow: auto;
	    /*width: 100px; *//* width of the spinner gif */
        /*height: 102px;*/ /*hight of the spinner gif +2px to fix IE8 issue */
		 width: 145px; /* width of the spinner gif */
        height: 120px; /*hight of the spinner gif +2px to fix IE8 issue */
		
	}
</style>
</head>

<body>

<div id="spinner" class="spinner" style="display:none;">
	 <img id="img-spinner" src="assets/images/spinner.gif" alt="Loading"/>
 	<div style=" color:#333333; font-weight:bold; font-size:12px;">Generating the Report</div>
</div>

<div><!-- main template wrapper -->
	<div class="header_wrapper">
		<div class="centered">
			<div>
				<div class="left"><a href="index.php"><img src="assets/images/zillow_logo.png" alt="logo" /></a></div>
				<div class="right">
					<ul id="topnav">
						<li>
							<a href="#" class="data_entry">Data Entry</a>
							<div style="opacity: 0; display: none; width: 450px;" class="sub">
								<div class="row">
									<ul>
										<li><h2>Quality Assurance</h2></li>
										<li><a href="index.php?page=queuelength" class="topnav">Queue Length</a></li>
									</ul>
								</div>
								<div class="row">
									<ul>
										<li><h2>General</h2></li>
										<li><a href="index.php?page=turnaroundtimebymarket" class="topnav">Turnaround Time by Market</a></li>
										<li><a href="index.php?page=totallistingsbymarket" class="topnav">Data Entry Volume by Market</a></li>
									</ul>
								</div>
								<div class="row">
									<ul>
										<li><h2>Filters</h2></li>
										<li><a href="index.php?page=manualmerges" class="topnav">Manual Merges</a></li>
										<li><a href="index.php?page=dataentryjobs" class="topnav">Data Entry Jobs</a></li>
									</ul>
								</div>
							</div>
						</li>
						<li>
							<a href="#" class="data_entry">Assistly</a>
							<div style="opacity: 0; display: none; width: 450px;" class="sub">
								<div class="row">
									<ul>
										<li><h2>Tickets</h2></li>
										<li><a href="index.php?page=ticketsbyweekday" class="topnav">Tickets by Weekday</a></li>
										<li><a href="index.php?page=ticketsbymonth" class="topnav">Tickets by Month</a></li>
										<li><a href="index.php?page=dailynewtickets" class="topnav">Daily New Tickets</a></li>
										<li><a href="index.php?page=onboardingtickets" class="topnav">Onboarding Tickets</a></li>
										<li><a href="index.php?page=resolvedbyagent" class="topnav">Resolved by Agent</a></li>
										<li><a href="index.php?page=ticketsinboundbyuser" class="topnav">Top 100 Users</a></li>
										<li><a href="index.php?page=ticketsinboundbyoffice" class="topnav">Top 100 Office</a></li>
										<li><a href="index.php?page=byofficetype" class="topnav">By Office Type</a></li>
										<li><a href="index.php?page=resolutiontime" class="topnav">Resolution Time</a></li>
										<li><a href="index.php?page=casesbyregion" class="topnav">Incoming Cases by Region</a></li>
										<li><h2>Super Tags</h2></li>
										<li><a href="index.php?page=supertagsdefinition" class="topnav">Definitions</a></li>
										<li><a href="index.php?page=resolvedbysupertag" class="topnav">Resolved by Super Tag</a></li>
										<li><a href="index.php?page=supertagusageover" class="topnav">Tag Usage Over Time</a></li>
										<li><a href="index.php?page=supertagusageleader" class="topnav">Tag Usage Leaderboard</a></li>
									</ul>
									<ul>
										<li><h2>Tags</h2></li>
										<li><a href="index.php?page=tagusageleaderboard" class="topnav">Tag Usage Leaderboard</a></li>
										<li><a href="index.php?page=tagusageovertime" class="topnav">Tag Usage Over Time</a></li>
										<li><h2>Search</h2></li>
										<li><a href="index.php?page=ticketsearchforoffices" class="topnav">Ticket Search for Offices</a></li>
										<li><a href="index.php?page=ticketsearchforusers" class="topnav">Ticket Search for Users</a></li>
										<li><h2>Interactions</h2></li>
										<li><a href="index.php?page=interbychannels" class="topnav">By Channel</a></li>
										<li><a href="index.php?page=interbyagent" class="topnav">By Agent</a></li>
										<li><h2>Admin</h2></li>
										<li><a href="index.php?page=syncstatus" class="topnav">Sync Status</a></li>
									</ul>
								</div>
								
							</div>
						</li>
						<li>
							<a href="#" class="data_entry">Salesforce</a>
							<div style="opacity: 0; display: none; width: 450px;" class="sub">
								<div class="row">
									<ul>
										<li><h2>Admin</h2></li>
										<li><a href="index.php?page=salesforceapimodels" class="topnav">API Models</a></li>
									</ul>
								</div>
							</div>
						</li>
						<li>
							<a href="#" class="data_entry">General</a>
							<div style="opacity: 0; display: none; width: 450px;" class="sub">
								<div class="row">
									<ul>
										<li><h2>Data Imports</h2></li>
										<li><a href="index.php?page=dataimportsovertime" class="topnav">Data Imports Over Time</a></li>
										<li><a href="index.php?page=listingsimportedovertime" class="topnav">Listings Imported Over Time</a></li>
										<li><h2>RentJuice</h2></li>
										<li><a href="index.php?page=officegrowth" class="topnav">Office Growth</a></li>
										<li><a href="index.php?page=listingsgrowth" class="topnav">Listings Growth</a></li>
										<li><a href="index.php?page=usergrowth" class="topnav">User Growth</a></li>
									</ul>
								</div>
							</div>
						</li>
					</ul>
				</div>
				<div class="clear"></div>
			</div>
			<div class="clear"></div>
		</div>
	</div>
