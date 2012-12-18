<?php

/* This controller renders the home page */

class PageController{

	var $page;
	
	
	function __construct($page_name,$start){
		$this->page = $page_name;
		$this->start = $start; // get the start time
	}

	public function handleRequest(){
	
		/*$barcontent = "";
		$sub_barcontent = "";*/
		$barcontent = array();
		
		
		if(isset($_GET)){
			// Queue Length Page
			if($this->page == "queuelength"){
				if(isset($_REQUEST['hours'])){ $hours = $_REQUEST['hours']; } 
				else { $hours = 72; }
				$barcontent['barcontent'] = BarGraphs::graphsQueueLength($hours);
			}
			// Tickets by Weekday Page
			if($this->page == "ticketsbyweekday"){
				$barcontent['barcontent'] = BarGraphs::ticketsByWeekDay();
			}
			// Tickets by Month Page
			if($this->page == "ticketsbymonth"){
				$barcontent['barcontent'] = BarGraphs::ticketsByMonth();
			}
			// Daily New Tickets Page
			if($this->page == "dailynewtickets"){
			
				if(isset($_REQUEST['time_frame'])){
					if($_REQUEST['time_frame'] == '7'){ 
						$barcontent['barcontent'] = BarGraphs::dailyNewTickets('7');	
					}
					else if($_REQUEST['time_frame'] == '14'){ 
						$barcontent['barcontent'] = BarGraphs::dailyNewTickets('14');	
					}
					else{ 
						$barcontent['barcontent'] = BarGraphs::dailyNewTicketsAllTime();
					}
				}
				else{ $barcontent['barcontent'] =  BarGraphs::dailyNewTickets('14'); }
			}
			
			// Total Listings by Market Page
			if($this->page == "totallistingsbymarket"){
			
				
				$arrayDate = array('1'=>'7','2'=>'14','3'=>'21','4'=>'28','5'=>'30','6'=>'60','7'=>'90','alltime'=>'alltime');
				if(isset($_REQUEST['totallistings_time'])){
					if(!empty($_REQUEST['totallistings_time'])){ 
						if (array_key_exists($_REQUEST['totallistings_time'],$arrayDate)){
							$timeFrame2 = $arrayDate[$_REQUEST['totallistings_time']];
						}
						else{ $timeFrame2 = "14"; }
					}
					else{ $timeFrame2 = "14"; }
				}
				else{ $timeFrame2 = "14"; }
				
				$barcontent['barcontent'] = BarGraphs::totalListingsByMarket($timeFrame2); 
				
			}
			
			//Turnaround Time By Market Page
			if($this->page == "turnaroundtimebymarket"){
				
				$arrayDate = array('1'=>'7','2'=>'14','3'=>'21','4'=>'28','5'=>'30','6'=>'60','7'=>'90','alltime'=>'alltime');
				if(isset($_REQUEST['turnaround_time'])){
					if(!empty($_REQUEST['turnaround_time'])){ 
						if (array_key_exists($_REQUEST['turnaround_time'],$arrayDate)){
							$timeFrame2 = $arrayDate[$_REQUEST['turnaround_time']];
						}
						else{ $timeFrame2 = "14"; }
					}
					else{ $timeFrame2 = "14"; }
				}
				else{ $timeFrame2 = "14"; }
				
				$barcontent['barcontent'] = BarGraphs::totalListingsByMarket($timeFrame2); 
				
			}
			
			//Manual Merges Page
			if($this->page == "manualmerges"){
				if(isset($_REQUEST['manualmerges_time'])){	
					$barcontent['barcontent'] = BarGraphs::manualMergesReport($_REQUEST['manualmerges_time']); 
				}
				else{
					$barcontent['barcontent'] = BarGraphs::manualMergesReport('2');
				}
			}
			
			// Tag Usage Leader Board Page
			if($this->page == "tagusageleaderboard"){
				if(isset($_REQUEST['leaderboard_time'])){ $barcontent['barcontent'] = BarGraphs::tagUsageLeaderBoard($_REQUEST['leaderboard_time']); }
				else{ $barcontent['barcontent'] = BarGraphs::tagUsageLeaderBoard('28'); }
			}
			
			// Tage Usage Overtime Page
			if($this->page == "tagusageovertime"){
			
				$arrayDate = array('1'=>'7','2'=>'14','3'=>'21','4'=>'28','5'=>'30','6'=>'60','7'=>'90','8'=>'120','9'=>'150','10'=>'180','11'=>'360','12'=>'720');
				
				if(isset($_REQUEST['moving_average'])){
					if (!empty($_REQUEST['moving_average'])){
						if( $_REQUEST['moving_average'] < 30 ){
							$dateMove = $_REQUEST['moving_average'];
						}
						else{ $dateMove = "7";	}
					}
					else{ $dateMove = "7"; }
				}
				else{ $dateMove = "7"; }

				if(isset($_REQUEST['tagusage_label'])){
				
					if(!empty($_REQUEST['tagusage_label'])){ $tagLabel= urldecode($_REQUEST['tagusage_label']); }
					else{ $tagLabel= "Data Entry"; }
				}
				else{ $tagLabel= "Data Entry"; }
				
				if(isset($_REQUEST['tagusage_time'])){
					if(!empty($_REQUEST['tagusage_time'])){ 
						if (array_key_exists($_REQUEST['tagusage_time'],$arrayDate)){
							$tagTime = $arrayDate[$_REQUEST['tagusage_time']];
						}
						else{ $tagTime= "28"; }
					}
					else{ $tagTime= "28"; }
				}
				else{ $tagTime= "28"; }
				
				$barcontent['barcontent'] = BarGraphs::tagUsageOvertime($tagTime,$tagLabel,$dateMove);
				$barcontent['sub_barcontent'] = BarGraphs::tagUsageFilter($tagTime); // for filtering the label data
				
			}
			
			// Page for the Onboarding Tickets by Status
			if($this->page == "onboardingtickets"){
				if(isset($_REQUEST['onboarding_tickets'])){ $barcontent['barcontent'] = BarGraphs::onBoardingTicketsbyStatus($_REQUEST['onboarding_tickets']); }
				else{ $barcontent['barcontent'] = BarGraphs::onBoardingTicketsbyStatus('28'); }
				
			}
			
			// Page for the Data Imports Over Time
			if($this->page == "dataimportsovertime"){
				$barcontent['barcontent'] = BarGraphs::dataImportsOverTime(); 
			}
			
			// Page for the Data Imports Over Time
			if($this->page == "listingsimportedovertime"){
				$barcontent['barcontent'] = BarGraphs::listingsImportedOverTime(); 
			}
			
			// Page for the Data Entry Jobs
			if($this->page == "dataentryjobs"){
				
				$arrayDate = array('1'=>'1 WEEK','2'=>'2 WEEK','3'=>'3 WEEK','4'=>'1 MONTH','5'=>'2 MONTH','6'=>'3 MONTH','7'=>'4 MONTH','8'=>'alltime');
				
				if(isset($_REQUEST['dataentryjobs_time'])){
				
					if (array_key_exists($_REQUEST['dataentryjobs_time'],$arrayDate)){
				  		$date = $arrayDate[$_REQUEST['dataentryjobs_time']];
				    }
					else{
					 	$date = "1 WEEK";
					}
				
				}
				else{
					$date = "1 WEEK";
				}
				$barcontent['barcontent'] = BarGraphs::dataEntryJobs($date); 
			}
			
			// Tickets Inbound by User Page
			if($this->page == "ticketsinboundbyuser"){
			
				$arrayDate = array('1'=>'1 WEEK','2'=>'2 WEEK','3'=>'3 WEEK','4'=>'4 WEEK','5'=>'2 MONTH','6'=>'3 MONTH','7'=>'4 MONTH','8'=>'alltime');
				
				if(isset($_REQUEST['ticketsinboundbyuser_time'])){
				
					if (array_key_exists($_REQUEST['ticketsinboundbyuser_time'],$arrayDate)){ $date = $arrayDate[$_REQUEST['ticketsinboundbyuser_time']]; }
					else{ $date = "4 WEEK";	}
				
				}
				else{ $date = "4 WEEK"; }
				 
				$barcontent['barcontent'] = BarGraphs::ticketsInboundByUser($date); 
				$barcontent['sub_barcontent'] = BarGraphs::ticketsInboundByUserTop20($date);  
				$barcontent['sub_barcontent2'] = BarGraphs::ticketsInboundByUserOther($date); 

			}
			
			// Tickets Inbound by User Page
			if($this->page == "ticketsinboundbyoffice"){
			
				$arrayDate = array('1'=>'1 WEEK','2'=>'2 WEEK','3'=>'3 WEEK','4'=>'4 WEEK','5'=>'2 MONTH','6'=>'3 MONTH','7'=>'4 MONTH','8'=>'alltime');
				
				if(isset($_REQUEST['ticketsinboundbyoffice_time'])){
				
					if (array_key_exists($_REQUEST['ticketsinboundbyoffice_time'],$arrayDate)){
				  		$date = $arrayDate[$_REQUEST['ticketsinboundbyoffice_time']];
				    }
					else{
					 	$date = "4 WEEK";
					}
				
				}
				else{
					$date = "4 WEEK";
				}
				 
				$barcontent['barcontent'] = BarGraphs::ticketsInboundByOffice($date); 
				$barcontent['sub_barcontent'] = BarGraphs::ticketsInboundByOfficeTop20($date);  
				$barcontent['sub_barcontent2'] = BarGraphs::ticketsInboundByOfficeOther($date); 
			}
			
		} // end GET
		
		//Resolved by Agent Page
		if($this->page == "resolvedbyagent"){
		
			$arrayDate = array('1'=>'7','2'=>'14','3'=>'21','4'=>'28','5'=>'30','6'=>'60','7'=>'90','8'=>'120','9'=>'150','10'=>'180','11'=>'360','12'=>'720');
			$arrayType = array('1'=>'alltickets','2'=>'email','3'=>'phone call','4'=>'chat','5'=>'tweet','6'=>'qna');
				
			if(isset($_REQUEST['tags'])){
				if (!empty($_REQUEST['tags'])){ $tags = urldecode($_REQUEST['tags']); }
				else{ $tags = "alltags"; }
			}
			else{ $tags = "alltags"; }	
				
			if(isset($_REQUEST['timespan'])){
					if(!empty($_REQUEST['timespan'])){ 
						if (array_key_exists($_REQUEST['timespan'],$arrayDate)){
							$timespan = $arrayDate[$_REQUEST['timespan']];
						}
						else{ $timespan= "30"; }
					}
					else{ $timespan= "30"; }
			}
			else{ $timespan= "30"; }
			
			if(isset($_REQUEST['movingaverage'])){
			
				if (!empty($_REQUEST['movingaverage'])){
					if( $_REQUEST['movingaverage'] < 30 ){
						$movingaverage = $_REQUEST['movingaverage'];
					}
					else{ $movingaverage = "7";	}
				}
				else{ $movingaverage = "7"; }
				
			}
			else{ $movingaverage = "7"; }
			
			if(isset($_REQUEST['type'])){
			
				if (array_key_exists($_REQUEST['type'],$arrayType)){
					$type = $arrayType[$_REQUEST['type']];
				}
				else{ $type = "alltickets"; }
			}
			else{ $type = "alltickets"; }
			
			$barcontent['barcontent'] = BarGraphs::resolvedByAgent($timespan,$movingaverage,$type,$tags);
			$barcontent['sub_barcontent'] = BarGraphs::resolvedByAgent2($timespan,$type,$tags);
			$barcontent['tag_barcontent'] = BarGraphs::tagUsageFilter($timespan); // for filtering the label data
		}
				
		// For ticket search for offices
		if($this->page == "ticketsearchforoffices"){
		
			$arrayDate = array('1'=>'1 WEEK','2'=>'2 WEEK','3'=>'3 WEEK','4'=>'4 WEEK','5'=>'2 MONTH','6'=>'3 MONTH','7'=>'4 MONTH','8'=>'alltime');
			
			if(isset($_REQUEST['officename'])){
				if(!empty($_REQUEST['officename'])){ 
					$explode = explode("(", urldecode($_REQUEST['officename']));
					$value= urldecode($explode[0]); 
				}
				else{ $value='159 Real Estate'; }
			}
			else{ $value='159 Real Estate'; }
			
			if(isset($_REQUEST['ticketsearch_time'])){
			
				if (array_key_exists($_REQUEST['ticketsearch_time'],$arrayDate)){
					$date = $arrayDate[$_REQUEST['ticketsearch_time']];
				}
				else{ $date = "alltime"; }
			}
			else{ $date = "alltime"; }
			
			$barcontent['barcontent'] = BarGraphs::ticketSearchForOffices($date);
			$barcontent['sub_barcontent'] = BarGraphs::ticketSearchForOffices2($value,$date);
			
		}
		
		// For ticket search for users
		if($this->page == "ticketsearchforusers"){
		
			$arrayDate = array('1'=>'1 WEEK','2'=>'2 WEEK','3'=>'3 WEEK','4'=>'4 WEEK','5'=>'2 MONTH','6'=>'3 MONTH','7'=>'4 MONTH','8'=>'alltime');
			
			if(isset($_REQUEST['username'])){
				if(!empty($_REQUEST['username'])){ 
					$explode = explode("(", urldecode($_REQUEST['username']));
					$value= urldecode($explode[0]); 
				}
				else{ $value='Aaron Barrett'; }
			}
			else{ $value='Aaron Barrett'; }
			
			if(isset($_REQUEST['ticketsearch_time'])){
			
				if (array_key_exists($_REQUEST['ticketsearch_time'],$arrayDate)){
					$date = $arrayDate[$_REQUEST['ticketsearch_time']];
				}
				else{ $date = "alltime"; }
			
			}
			else{ $date = "alltime"; }
			
			$barcontent['barcontent'] = BarGraphs::ticketSearchForUsers($date);
			$barcontent['sub_barcontent'] = BarGraphs::ticketSearchForUsers2($value,$date);
			
		}
		
		// For ticket search for users
		if($this->page == "syncstatus"){
			$barcontent['barcontent'] = BarGraphs::syncStatus();
		}
		
		// For By Office Type
		if($this->page == "byofficetype"){
		
			$arrayDate = array('1'=>'7','2'=>'14','3'=>'21','4'=>'28','5'=>'30','6'=>'60','7'=>'90','8'=>'120','9'=>'150','10'=>'180','11'=>'360','12'=>'720');
		
			if(isset($_REQUEST['movingaverage'])){
				if (!empty($_REQUEST['movingaverage'])){
					if( $_REQUEST['movingaverage'] < 30 ){
						$movingAverage = $_REQUEST['movingaverage'];
					}
					else{ $movingAverage = "7";	}
				}
				else{ $movingAverage = "7"; }
			}
			else{ $movingAverage = "7"; }
			
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
			
			$barcontent['barcontent'] = BarGraphs::byOfficeType($movingAverage,$timeSpan);
		}
		
		// For Resolution Time
		if($this->page == "resolutiontime"){
			$arrayDate = array('1'=>'7','2'=>'14','3'=>'21','4'=>'28','5'=>'30','6'=>'60','7'=>'90','8'=>'120','9'=>'150','10'=>'180','11'=>'360','12'=>'720');
		
			if(isset($_REQUEST['movingaverage'])){
				if (!empty($_REQUEST['movingaverage'])){
					if( $_REQUEST['movingaverage'] < 30 ){
						$movingAverage = $_REQUEST['movingaverage'];
					}
					else{ $movingAverage = "7";	}
				}
				else{ $movingAverage = "7"; }
			}
			else{ $movingAverage = "7"; }
			
			if(isset($_REQUEST['timespan'])){
				if(!empty($_REQUEST['timespan'])){ 
					if (array_key_exists($_REQUEST['timespan'],$arrayDate)){
						$timeSpan = $arrayDate[$_REQUEST['timespan']];
					}
					else{ $timeSpan= "30"; }
				}
				else{ $timeSpan= "30"; }
			}
			else{ $timeSpan= "30"; }
			
			if(isset($_REQUEST['groupby'])){
	
				if(!empty($_REQUEST['groupby'])){
					if($_REQUEST['groupby'] == "datecreated"){
						$groupBy = "created_at"; 
					}
					else if($_REQUEST['groupby'] == "dateresolved"){
						$groupBy = "resolved_at"; 
					}
					else{
						$groupBy = 'created_at';
					}
				}
				else{$groupBy = 'created_at';}
				
			}
			else{ $groupBy = 'created_at'; }
			
			
			$barcontent['barcontent'] = BarGraphs::resolutionTime($movingAverage,$timeSpan,$groupBy);

		}
		
		// For cases By Region
		if($this->page == "casesbyregion"){
			$arrayDate = array('1'=>'7','2'=>'14','3'=>'21','4'=>'28','5'=>'30','6'=>'60','7'=>'90','8'=>'120','9'=>'150','10'=>'180','11'=>'360','12'=>'720');
			$arrayRegion = array('1'=>'5','2'=>'10','3'=>'20','viewall'=>'viewall');
		
			if(isset($_REQUEST['movingaverage'])){
				if (!empty($_REQUEST['movingaverage'])){
					if( $_REQUEST['movingaverage'] < 30 ){
						$movingAverage = $_REQUEST['movingaverage'];
					}
					else{ $movingAverage = "7";	}
				}
				else{ $movingAverage = "7"; }
			}
			else{ $movingAverage = "7"; }
			
			if(isset($_REQUEST['timespan'])){
				if(!empty($_REQUEST['timespan'])){ 
					if (array_key_exists($_REQUEST['timespan'],$arrayDate)){
						$timeSpan = $arrayDate[$_REQUEST['timespan']];
					}
					else{ $timeSpan= "30"; }
				}
				else{ $timeSpan= "30"; }
			}
			else{ $timeSpan= "30"; }
			
			if(isset($_REQUEST['regions'])){
				if(!empty($_REQUEST['regions'])){ 
					if (array_key_exists($_REQUEST['regions'],$arrayRegion)){
						$region = $arrayRegion[$_REQUEST['regions']];
					}
					else{ $region= "5"; }
				}
				else{ $region= "5"; }
			}
			else{ $region= "5"; }
			
			$barcontent['barcontent'] = BarGraphs::casesByRegion($movingAverage,$timeSpan,$region);

		}
		
		// For Interactions by Channel Report
		if($this->page == "interbychannels"){
		
			$arrayDate = array('1'=>'7','2'=>'14','3'=>'21','4'=>'28','5'=>'30','6'=>'60','7'=>'90','8'=>'120','9'=>'150','10'=>'180','11'=>'360','12'=>'720');
		
			if(isset($_REQUEST['movingaverage'])){
				if (!empty($_REQUEST['movingaverage'])){
					if( $_REQUEST['movingaverage'] < 30 ){
						$movingAverage = $_REQUEST['movingaverage'];
					}
					else{ $movingAverage = "7";	}
				}
				else{ $movingAverage = "7"; }
			}
			else{ $movingAverage = "7"; }
			
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
			
			$barcontent['barcontent'] = BarGraphs::interByChannels($movingAverage,$timeSpan);
		}
		
		// For Interactions by Agent Report
		if($this->page == "interbyagent"){
		
			$arrayDate = array('1'=>'7','2'=>'14','3'=>'21','4'=>'28','5'=>'30','6'=>'60','7'=>'90','8'=>'120','9'=>'150','10'=>'180','11'=>'360','12'=>'720');
			$arrayType = array('1'=>'alltickets','2'=>'email','3'=>'phone call','4'=>'chat','5'=>'tweet','6'=>'qna');
		
			if(isset($_REQUEST['movingaverage'])){
				if (!empty($_REQUEST['movingaverage'])){
					if( $_REQUEST['movingaverage'] < 30 ){
						$movingAverage = $_REQUEST['movingaverage'];
					}
					else{ $movingAverage = "7";	}
				}
				else{ $movingAverage = "7"; }
			}
			else{ $movingAverage = "7"; }
			
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
			
			if(isset($_REQUEST['type'])){
			
				if (array_key_exists($_REQUEST['type'],$arrayType)){
					$type = $arrayType[$_REQUEST['type']];
				}
				else{ $type = "alltickets"; }
			}
			else{ $type = "alltickets"; }
			
			if(isset($_REQUEST['direction'])){
				if(!empty($_REQUEST['direction'])){
					if($_REQUEST['direction'] == "in"){ $direction ="in"; }
					else if($_REQUEST['direction'] == "out" ){ $direction ="out"; }
					else{  $direction ="all"; }
				}
				else{ $direction ="all"; }
			}
			else{ $direction ="all"; }
			
			$barcontent['barcontent'] = BarGraphs::interByAgent($movingAverage,$timeSpan,$type,$direction);
		}
		
		// For Sales Force Api Models
		if($this->page == "salesforceapimodels"){
			$barcontent['barcontent'] = BarGraphs::salesForceApiModels();
		}
		
		// For Sales Force Fields
		if($this->page == "salesforcefields"){
		
			if(isset($_REQUEST['model_id'])){
				if(!empty($_REQUEST['model_id'])){
					$id = $_REQUEST['model_id'];
				}
				else{ $id = "603"; }
			}
			else{ $id = "603"; }
			
			$barcontent['barcontent'] = BarGraphs::salesForceFields($id);
		}
		
		// For Rentjuice Office Growth
		if($this->page == "officegrowth"){
			$barcontent['barcontent'] = BarGraphs::officeGrowth();
		}
		
		// For Rentjuice Listings Growth
		if($this->page == "listingsgrowth"){
			$barcontent['barcontent'] = BarGraphs::listingsGrowth();
		}
		
		// For Rentjuice User Growth
		if($this->page == "usergrowth"){
			$barcontent['barcontent'] = BarGraphs::userGrowth();
		}
		
		// For Rentjuice Super Tags Definition
		if($this->page == "supertagsdefinition"){
			$barcontent['barcontent'] = BarGraphs::superTagsDefinition();
			$barcontent['sub_barcontent'] = BarGraphs::superTagPriorities();
		}
		
		// For Rentjuice Super Tags Definition
		if($this->page == "resolvedbysupertag"){
		
			$arrayDate = array('1'=>'7','2'=>'14','3'=>'21','4'=>'28','5'=>'30','6'=>'60','7'=>'90','8'=>'120','9'=>'150','10'=>'180','11'=>'360','12'=>'720');
			$arrayType = array('1'=>'alltickets','2'=>'email','3'=>'phone call','4'=>'chat','5'=>'tweet','6'=>'qna');
				
				
			if(isset($_REQUEST['timespan'])){
					if(!empty($_REQUEST['timespan'])){ 
						if (array_key_exists($_REQUEST['timespan'],$arrayDate)){
							$timespan = $arrayDate[$_REQUEST['timespan']];
						}
						else{ $timespan= "30"; }
					}
					else{ $timespan= "30"; }
			}
			else{ $timespan= "30"; }
			
			if(isset($_REQUEST['movingaverage'])){
			
				if (!empty($_REQUEST['movingaverage'])){
					if( $_REQUEST['movingaverage'] < 30 ){
						$movingaverage = $_REQUEST['movingaverage'];
					}
					else{ $movingaverage = "7";	}
				}
				else{ $movingaverage = "7"; }
				
			}
			else{ $movingaverage = "7"; }
			
			if(isset($_REQUEST['type'])){
			
				if (array_key_exists($_REQUEST['type'],$arrayType)){
					$type = $arrayType[$_REQUEST['type']];
				}
				else{ $type = "alltickets"; }
			}
			else{ $type = "alltickets"; }
			
			
			$barcontent['barcontent'] = BarGraphs::resolvedBySuperTag($timespan,$movingaverage,$type);;
		}
		
		// Super Tag Usage over time Page
		
		if($this->page == "supertagusageover"){
		
			$arrayDate = array('1'=>'7','2'=>'14','3'=>'21','4'=>'28','5'=>'30','6'=>'60','7'=>'90','8'=>'120','9'=>'150','10'=>'180','11'=>'360','12'=>'720');
			
			if(isset($_REQUEST['moving_average'])){
				if (!empty($_REQUEST['moving_average'])){
					if( $_REQUEST['moving_average'] < 30 ){
						$dateMove = $_REQUEST['moving_average'];
					}
					else{ $dateMove = "7";	}
				}
				else{ $dateMove = "7"; }
			}
			else{ $dateMove = "7"; }

			if(isset($_REQUEST['tagusage_label'])){
			
				if(!empty($_REQUEST['tagusage_label'])){ $tagLabel= urldecode($_REQUEST['tagusage_label']); }
				else{ $tagLabel= "alltags"; }
			}
			else{ $tagLabel= "alltags"; }
			
			if(isset($_REQUEST['tagusage_time'])){
				if(!empty($_REQUEST['tagusage_time'])){ 
					if (array_key_exists($_REQUEST['tagusage_time'],$arrayDate)){
						$tagTime = $arrayDate[$_REQUEST['tagusage_time']];
					}
					else{ $tagTime= "28"; }
				}
				else{ $tagTime= "28"; }
			}
			else{ $tagTime= "28"; }
			
			$barcontent['barcontent'] = BarGraphs::superTagUsageOvertime($tagTime,$tagLabel,$dateMove);
			$barcontent['sub_barcontent'] = BarGraphs::superTagUsageFilter($tagTime); // for filtering the label data
			
		}
		
		// Super Tag Usage Leader Board Page
		if($this->page == "supertagusageleader"){
		
			if(isset($_REQUEST['leaderboard_time']) && !empty($_REQUEST['leaderboard_time'])){ 
				$leaderTime = $_REQUEST['leaderboard_time']; 
			}
			else{ $leaderTime ="28"; }
			
			$barcontent['barcontent'] = BarGraphs::superTagUsageLeader($leaderTime);
			
		}
		
		$barcontent['start_time'] = $this->start; // pass teh start time valkue into barcontent array
			
		render($this->page,$barcontent);
		
	}
	
}

?>