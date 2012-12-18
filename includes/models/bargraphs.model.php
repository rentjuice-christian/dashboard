<?php

/* This controller renders the home page */

class BarGraphs{
	
		
	public static function graphsQueueLength($value=''){
	
		global $db;

		$sqlQuery = "SELECT h AS the_hour,
					  SUM(IF(ts.de_qa_started, 1, 0)) AS num_in_de_qa_at_hour
					  FROM
						   (SELECT id, DATE_FORMAT(DATE_SUB(NOW(), INTERVAL id HOUR), '%Y,%m,%d,%H') AS h
							  FROM dataentry.threads
							 WHERE id <= ".$value."
							 ORDER BY id) AS hours
							
							LEFT OUTER JOIN (       
							SELECT t.id,
								   ts1.completed_on AS de_qa_started,
								   ts2.created_on AS de_qa_finished
							  FROM dataentry.threads t
								   LEFT OUTER JOIN dataentry.thread_sessions ts1
									  ON ts1.thread_id = t.id
									  AND ts1.id = (SELECT id FROM dataentry.thread_sessions WHERE thread_id = t.id ORDER BY id ASC LIMIT  1)
								   LEFT OUTER JOIN dataentry.thread_sessions ts2
									  ON ts2.thread_id = t.id
									  AND ts2.id = (SELECT id FROM dataentry.thread_sessions WHERE thread_id = t.id ORDER BY id ASC LIMIT  1, 1)
							 WHERE deleted = 0
							   AND t.created_on > DATE_SUB(NOW(), INTERVAL ".$value." HOUR)
							 GROUP BY t.id
							 ) AS ts
							 ON hours.h > ts.de_qa_started AND (hours.h < ts.de_qa_finished OR ts.de_qa_finished IS NULL)
					 GROUP BY hours.h
					 ORDER BY hours.h ASC";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function ticketsByWeekDay(){
	
		global $db;

		$sqlQuery = "SELECT WEEKDAY(created_at) as weekday, DAYNAME(created_at) as dayname, COUNT(*) as count
					 FROM janak.assistly_cases
					 GROUP BY 1
					 ORDER BY 1";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	public static function ticketsByMonth(){
	
		global $db;

		$sqlQuery = "SELECT YEAR(created_at) as year, MONTH(created_at) as month, MONTHNAME(created_at) as monthname, COUNT(*) as count
					 FROM janak.assistly_cases
					 GROUP BY 1,2
					 ORDER BY 1";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function dailyNewTicketsAllTime(){
	
		global $db;

		$sqlQuery = "SELECT all_data.created_at,
					  (SELECT COUNT(*)/COUNT(DISTINCT(DATE(created_at)))
						 FROM janak.assistly_cases
						WHERE all_data.created_at <= DATE(created_at) AND DATE(created_at) <= DATE_ADD(all_data.created_at, INTERVAL 7 DAY)) average_tickets
					FROM
					(SELECT DATE(created_at) created_at, COUNT(*)
					FROM janak.assistly_cases
					GROUP BY 1
					ORDER BY 1 ) all_data
					";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function dailyNewTickets($value=''){
	
		global $db;

		$sqlQuery = "SELECT DATE(created_at) as created_at, COUNT(*) as average_tickets
					 FROM janak.assistly_cases
					 WHERE DATE_ADD(created_at, INTERVAL ".$value." DAY) >= SYSDATE()
					 GROUP BY 1
					 ORDER BY 1
					";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function totalListingsByMarket($value=''){
	
		global $db;
		
		if($value != "alltime"){
			$strQuery = "AND DATE(t.applied_on) >= DATE_SUB(CURDATE() , INTERVAL ".$value." DAY)";
		}
		else { $strQuery = ""; }
		
	
		$sqlQuery = "SELECT
						DATE(t.applied_on) AS day_completed ,
						r.name AS region_name ,
						COUNT(l.id) AS total_listings ,
						AVG(TIMESTAMPDIFF(HOUR , t.created_on , t.applied_on)) AS hour_turn_around
					FROM rentjuice.offices o
					INNER JOIN rentjuice.regions r ON o.region_id = r.id
					INNER JOIN dataentry.threads t ON o.id = t.office_id
						AND t.completed = 1
						AND t.deleted = 0
						AND t.completed_reason IS NULL
							".$strQuery."
							INNER JOIN(SELECT MAX( id ) AS session_id , thread_id
							FROM dataentry.thread_sessions ts
							GROUP BY thread_id) AS max_sessions
							ON max_sessions.thread_id = t.id INNER JOIN dataentry.updates l
							ON l.session_id = max_sessions.session_id
					WHERE o.dataentry = 1
					GROUP BY o.region_id ,YEAR(t.applied_on), MONTH(t.applied_on), DAY(t.applied_on)
					ORDER BY
						YEAR( t.applied_on ) ASC ,
						MONTH( t.applied_on ) ASC ,
						DAY(t.applied_on) ASC , o.region_id ASC
					 ";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	public static function manualMergesReport($value=''){
	
		global $db;
		
		if($value == ""){
			$strQuery = "AND DATE_ADD(t.created_on, INTERVAL 2 WEEK) > SYSDATE()";
		}
		else{
			if($value != "alltime"){
				$strQuery = "AND DATE_ADD(t.created_on, INTERVAL ".$value." WEEK) > SYSDATE()";
			}
			else{
				$strQuery = "";
			}
		}

		$sqlQuery = "SELECT COUNT(thread_id) as threadcount, SUM(had_manual_merge) as summerge, NAME as officename, office_id
						FROM (
							SELECT t.id AS thread_id, IF(a.id, 1, 0) AS had_manual_merge, o.name AS NAME, o.id office_id
							FROM dataentry.emails e
							INNER JOIN dataentry.threads t ON t.id = e.thread_id AND
							e.content_office_id > 0 ".$strQuery."
							LEFT OUTER JOIN dataentry.activities a ON a.thread_id = t.id AND
							a.action LIKE '%merged into this thread'
							INNER JOIN rentjuice.offices o ON t.office_id = o.id
						) AS TMP
						GROUP BY 3
						HAVING SUM(had_manual_merge) > 0
						ORDER BY 2 desc
					 ";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function tagUsageLeaderBoard($value=''){
	
		global $db;
		
		if(!empty($value)){
			if($value != "alltime"){
				$strQuery = "AND DATE_ADD(created_at, INTERVAL ".$value." DAY) >= SYSDATE()";
			}
			else{
				$strQuery ="";
			}
		}
		else{
			$strQuery = "AND DATE_ADD(created_at, INTERVAL 28 DAY) >= SYSDATE()";
		}

		$sqlQuery = "SELECT label_name, COUNT(*) as count_tag
					    FROM janak.assistly_case_labels cl, janak.assistly_cases c
						WHERE cl.case_id = c.id
						 ".$strQuery."
						 GROUP BY 1
						 ORDER BY 2 desc
					 ";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function tagUsageFilter($value=''){
	
		global $db;

		if(!empty($value)){
			if($value != "alltime"){
				$strQuery = "AND DATE_ADD(created_at, INTERVAL ".$value." DAY) >= SYSDATE()";
			}
			else{
				$strQuery ="";
			}
		}
		else{
			$strQuery = "AND DATE_ADD(created_at, INTERVAL 7 DAY) >= SYSDATE()";
		}
					 
		$sqlQuery = "
					 SELECT label_name, COUNT(*) as count_tag
					 FROM janak.assistly_case_labels cl, janak.assistly_cases c
					 WHERE cl.case_id = c.id
					 ".$strQuery."
					 GROUP BY 1
					 ORDER BY 1
					 
					";
					 
		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function tagUsageOvertime($valueTime='',$valueLabel='',$valueMove = ''){
	
		global $db;
		
		if($valueLabel !="alltags"){
			$moveString1="FROM janak.assistly_case_labels cl, janak.assistly_cases c";
			$moveString2="AND cl.case_id = c.id
					      AND label_name = '".$valueLabel."'";
		}
		else{
			$moveString1="FROM janak.assistly_cases c";
			$moveString2="";
		}
	 
		$sqlQuery = "SELECT
				   days.created_at,
				   (
					SELECT COUNT(*)/IF(DATEDIFF(SYSDATE(),days.created_at) >= ".$valueMove.", ".$valueMove.", DATEDIFF(SYSDATE(),days.created_at))
					  ".$moveString1."
					 WHERE days.created_at <= DATE(c.created_at) AND DATE(c.created_at) <= DATE_ADD(days.created_at, INTERVAL ".($valueMove - 1)." DAY)         
					  ".$moveString2."  
					) ticket_count
				FROM
					  (SELECT DATE_SUB(DATE(SYSDATE()),INTERVAL id DAY) created_at
					  FROM janak.counter_records
					  WHERE id <= ".$valueTime.") days
					 ORDER BY days.created_at";


		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function onBoardingTicketsbyStatus($value=''){
	
		global $db;
		
		if(!empty($value)){
			if($value != "alltime"){
				$strQuery = " AND DATE_ADD(c.created_at, INTERVAL ".$value." DAY) >= SYSDATE()";
			}
			else{
				$strQuery ="";
			}
		}
		else{
			$strQuery = "AND DATE_ADD(c.created_at, INTERVAL 28 DAY) >= SYSDATE()";
		}

		$sqlQuery = "SELECT label_name, 
							COUNT(*),
						   SUM(IF(c.case_status_type = 'New',1,0)) new,
						   SUM(IF(c.case_status_type = 'Open',1,0)) open,
						   SUM(IF(c.case_status_type = 'Pending',1,0)) pending,
						   SUM(IF(c.case_status_type = 'Resolved',1,0)) resolved
					 FROM janak.assistly_case_labels cl, janak.assistly_cases c
					 WHERE cl.label_name IN ('Database', 'Data Import', 'Feeds', 'Forms', 'Merge', 'Offices', 'Onboarding', 'PM Onboarding', 'Training', 'Webinar')
					  AND cl.case_id = c.id
					 ".$strQuery."
					 GROUP BY 1
					 ORDER BY 2 desc
					 ";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function dataImportsOverTime(){
	
		global $db;

		$sqlQuery = "SELECT LEFT(o.created_on,7) as createdon, COUNT(DISTINCT l.office_id) as countoffice
						FROM rentjuice.offices o INNER JOIN rentjuice.listings l ON l.office_id = o.id
						WHERE (l.import_reference_id IS NOT NULL AND import_reference_id != '')
						AND o.name NOT LIKE \"%OLD DE ACCOUNT%\"
						AND o.dataentry = 0
						AND o.id != 4463
						GROUP BY 1
						ORDER BY 1
					 ";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function listingsImportedOverTime(){
	
		global $db;

		$sqlQuery = "SELECT LEFT(o.created_on,7) as MONTH,
						   IF(l.active = 1,'Active Listings', 'Inactive Listings') as listing_status,
						   COUNT(l.id) as count_data
					  FROM rentjuice.offices o
						   INNER JOIN rentjuice.listings l
							 ON l.office_id = o.id
					 WHERE (l.import_reference_id IS NOT NULL AND import_reference_id != '')
					   AND o.dataentry = 0
					   AND o.name NOT LIKE '%OLD DE ACCOUNT%'
					   AND o.id != 4463
					   AND LEFT(o.created_on,7) <> '2010-11'
					 GROUP BY 1,2
					 ORDER BY 1,2
					 ";


		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function dataEntryJobs($value = ''){
	
		global $db;
		
		if(!empty($value)){
			if($value != "alltime"){
				$strQuery = "AND DATE_ADD(t.created_on, INTERVAL ".$value.") > SYSDATE()";
				$strQuery2 = "WHERE DATE_ADD(t.created_on, INTERVAL ".$value.") > SYSDATE()";
			}
			else{
				$strQuery ="";
				$strQuery2 ="";
			}
		}
		else{
			$strQuery = "AND DATE_ADD(t.created_on, INTERVAL 1 WEEK) > SYSDATE()";
			$strQuery2 = "WHERE DATE_ADD(t.created_on, INTERVAL 1 WEEK) > SYSDATE()";
		}

		$sqlQuery = "SELECT the_date,
						   SUM(the_count) AS filters,
						   SUM(the_count2) AS threads_preferred,
						   SUM(the_count3) AS threads_auto,
						   SUM(the_count4) AS total
					  FROM (
							SELECT LEFT(t.created_on, 10) AS the_date,
								   0 AS the_count,
								   0 AS the_count2,
								   0 AS the_count3,
								   COUNT(t.id) AS the_count4,
								   'total' AS src
							  FROM dataentry.threads t
								   INNER JOIN rentjuice.offices o ON o.id = t.office_id
							 WHERE o.region_id = 2
							   AND t.created_on > \"2012-03-01 00:00:00\"
							   ".$strQuery."
							 GROUP BY 1
					
							 UNION
							
							SELECT LEFT(e.received_on, 10) AS the_date,
								   COUNT(e.id) AS the_count,
								   0 AS the_count2,
								   0 AS the_count3,
								   0 AS the_count4,
								   'filters' AS src
							  FROM dataentry.emails e
								   INNER JOIN dataentry.threads t ON t.id = e.thread_id
										  AND e.content_office_id > 0
										  AND t.created_on > \"2012-03-01 00:00:00\"
								   INNER JOIN rentjuice.offices o ON o.id = t.office_id
										  AND o.region_id = 2
							 ".$strQuery2."
							 GROUP BY 1
					
							 UNION
							
							SELECT LEFT(t.created_on, 10) AS the_date,
								   0 AS the_count,
								   COUNT(t.id) AS the_count2,
								   0 AS the_count3,
								   0 AS the_count4,
								   'threads_preferred' AS src 
							  FROM dataentry.threads t
								   INNER JOIN rentjuice.offices o ON o.id = t.office_id
										  AND o.region_id = 2
							 WHERE t.preferred = 1
							   ".$strQuery."
							 GROUP BY 1
							
							 UNION
					
							SELECT LEFT(t.created_on, 10) AS the_date,
								   0 AS the_count, 0 AS
								   the_count2,
								   COUNT(t.id) AS the_count3,
								   0 AS the_count4,
								   'threads_auto' AS src
							  FROM dataentry.threads t
								   INNER JOIN rentjuice.offices o ON o.id = t.office_id
										  AND o.region_id = 2
							 WHERE t.auto_job = 1
							   ".$strQuery."
							 GROUP BY 1
							
							 ) AS tmp
					
					 GROUP BY the_date
					 ";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function ticketsInboundByUser($value=''){
	
		global $db;
		
		if($value != "alltime"){
			$strQuery = "AND DATE_ADD(ca.created_at, INTERVAL ".$value.") >= SYSDATE()";
		}
		else{
			$strQuery ="";
		}

		$sqlQuery = "SELECT cu.custom_user_id, CONCAT(u.first_name,' ',u.last_name) username, o.name officename, o.id office_id, COUNT(*) ticket_count
					  FROM janak.assistly_cases ca, janak.assistly_customers cu, rentjuice.users u, rentjuice.offices o
					 WHERE ca.customer_id = cu.id
					   AND u.id = cu.custom_user_id
					   AND u.office_id = o.id
					  ".$strQuery."
					 GROUP BY 1
					 ORDER BY COUNT(*) DESC
					 LIMIT 100
					 ";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function ticketsInboundByUserTop20($value=''){
	
		global $db;
		
		if($value != "alltime"){
			$strQuery = "AND DATE_ADD(ca.created_at, INTERVAL ".$value.") >= SYSDATE()";
		}
		else{
			$strQuery ="";
		}

		$sqlQuery = "SELECT SUM(ticket_count) ticket_count, COUNT(custom_user_id) user_count
						FROM (
						SELECT cu.custom_user_id, CONCAT(u.first_name,' ',u.last_name) username, o.name officename, o.id office_id, COUNT(*) ticket_count
						  FROM janak.assistly_cases ca, janak.assistly_customers cu, rentjuice.users u, rentjuice.offices o
						 WHERE ca.customer_id = cu.id
						   AND u.id = cu.custom_user_id
						   AND u.office_id = o.id
						   ".$strQuery."
						 GROUP BY 1
						 ORDER BY COUNT(*) DESC
						LIMIT 20) a
					 ";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function ticketsInboundByUserOther($value=''){
	
		global $db;
		
		if($value != "alltime"){
			$strQuery = "AND DATE_ADD(ca.created_at, INTERVAL ".$value.") >= SYSDATE()";
		}
		else{
			$strQuery ="";
		}

		$sqlQuery = "SELECT SUM(ticket_count) ticket_count, COUNT(custom_user_id) user_count
					FROM (
					
					SELECT cu.custom_user_id, CONCAT(u.first_name,' ',u.last_name) username, o.name officename, o.id office_id, COUNT(*) ticket_count
					  FROM janak.assistly_cases ca, janak.assistly_customers cu, rentjuice.users u, rentjuice.offices o
					 WHERE ca.customer_id = cu.id
					   AND u.id = cu.custom_user_id
					   AND u.office_id = o.id
					    ".$strQuery."
					 GROUP BY 1
					 ORDER BY COUNT(*) DESC
					LIMIT 1000000 OFFSET 20 ) a
					 ";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	
	public static function ticketsInboundByOffice($value=''){
	
		global $db;
		
		if($value != "alltime"){
			$strQuery = "AND DATE_ADD(ca.created_at, INTERVAL ".$value.") >= SYSDATE()";
		}
		else{
			$strQuery ="";
		}

		$sqlQuery = "SELECT o.id office_id, o.name as officename, COUNT(*) as ticket_count
					  FROM janak.assistly_cases ca, janak.assistly_customers cu, rentjuice.users u, rentjuice.offices o
					 WHERE ca.customer_id = cu.id
					   AND u.id = cu.custom_user_id
					   AND u.office_id = o.id
					  ".$strQuery."
					 GROUP BY 1
					 ORDER BY COUNT(*) DESC
					LIMIT 100
					 ";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function ticketsInboundByOfficeTop20($value=''){
	
		global $db;
		

		if($value != "alltime"){
			$strQuery = "AND DATE_ADD(ca.created_at, INTERVAL ".$value.") >= SYSDATE()";
		}
		else{
			$strQuery ="";
		}


		$sqlQuery = "SELECT SUM(ticket_count) as ticket_count, COUNT(office_id) office_count
					FROM (
					SELECT o.id office_id, o.name, COUNT(*) ticket_count
					
					  FROM janak.assistly_cases ca, janak.assistly_customers cu, rentjuice.users u, rentjuice.offices o
					 WHERE ca.customer_id = cu.id
					   AND u.id = cu.custom_user_id
					   AND u.office_id = o.id
					  ".$strQuery."
					 GROUP BY 1
					 ORDER BY COUNT(*) DESC
					LIMIT 20) a
					 ";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function ticketsInboundByOfficeOther($value=''){
	
		global $db;
		
		if($value != "alltime"){
			$strQuery = "AND DATE_ADD(ca.created_at, INTERVAL ".$value.") >= SYSDATE()";
		}
		else{
			$strQuery ="";
		}

		$sqlQuery = "SELECT SUM(ticket_count) as ticket_count, COUNT(office_id) office_count
					FROM (
					SELECT o.id office_id, o.name, COUNT(*) ticket_count
					
					  FROM janak.assistly_cases ca, janak.assistly_customers cu, rentjuice.users u, rentjuice.offices o
					 WHERE ca.customer_id = cu.id
					   AND u.id = cu.custom_user_id
					   AND u.office_id = o.id
					  ".$strQuery."
					 GROUP BY 1
					 ORDER BY COUNT(*) DESC
					LIMIT 1000000 offset 20) a
					 ";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function resolvedBySuperTag($timespan="",$movingaverage="",$type=""){
	
		global $db;
		
		if($type != "alltickets"){ 	$strQueryType = "AND channel = '".$type."'";  }
		else{ 	$strQueryType =""; 	}
		

		$sqlQuery = "SELECT tag_days.tag_name,
						   tag_days.resolved_at,
						   (
							SELECT COUNT(*)/IF(DATEDIFF(SYSDATE(),tag_days.resolved_at) >= ".$movingaverage.", ".$movingaverage.", DATEDIFF(SYSDATE(),tag_days.resolved_at))
							  FROM janak.assistly_cases c
							 WHERE c.super_tag = tag_days.tag_name
							  ".$strQueryType."
							   AND tag_days.resolved_at <= DATE(resolved_at) AND DATE(resolved_at) <= DATE_ADD(tag_days.resolved_at, INTERVAL ". ($movingaverage - 1) ." DAY) 
						   ) ticket_count
					  FROM  
						   (
							SELECT *
							  FROM
								   (SELECT tag_name
									  FROM janak.super_tags                
								   ) tags,
								   (SELECT DATE_SUB(DATE(SYSDATE()),INTERVAL id DAY) resolved_at
									  FROM janak.counter_records
									 WHERE id <= ".$timespan."
								   ) days
						   ) tag_days
					 ORDER BY tag_days.resolved_at, tag_days.tag_name ASC ";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function resolvedByAgent($timespan="",$movingaverage="",$type="",$tags=""){
	
		global $db;
	
		
		if($type != "alltickets"){ 	$strQueryType = "AND channel = '".$type."'";  }
		else{ 	$strQueryType =""; 	}
		
		if($tags !="alltags"){
			$tagsString=" FROM janak.assistly_cases c, janak.assistly_case_labels cl";
			$tagsString2=" AND c.id = cl.case_id
					      AND cl.label_name = '".$tags."'";
		}
		else{
			$tagsString=" FROM janak.assistly_cases c";
			$tagsString2="";
		}
		

		$sqlQuery="SELECT user_days.username,
				   user_days.resolved_at,
				   (
					  SELECT COUNT(*)/IF(DATEDIFF(SYSDATE(),user_days.resolved_at) >= ".$movingaverage.", ".$movingaverage.", DATEDIFF(SYSDATE(),user_days.resolved_at))
						".$tagsString."
					   WHERE username = user_days.username
					   	".$tagsString2."
					    ".$strQueryType."
						 AND user_days.resolved_at <= DATE(resolved_at) AND DATE(resolved_at) <= DATE_ADD(user_days.resolved_at, INTERVAL ". ($movingaverage - 1) ." DAY)          
				   ) ticket_count
			  FROM
				   (
					SELECT *
					  FROM
						   (SELECT username
							  FROM janak.assistly_cases
							 WHERE username IS NOT NULL
							   AND DATE_ADD(resolved_at, INTERVAL ".$timespan." DAY) > SYSDATE()
							 GROUP BY 1
						   ) users,
							   (SELECT DATE_SUB(DATE(SYSDATE()),INTERVAL id DAY) resolved_at
									FROM janak.counter_records
								   WHERE id <= ".$timespan.") days
				   ) user_days
			 ORDER BY user_days.resolved_at, username ASC";
					 
		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function resolvedByAgent2($timespan="",$type="",$tags=""){
	
		global $db;
		
		if($timespan != "alltime"){
			$strQueryTime = "AND DATE_ADD(resolved_at, INTERVAL ".$timespan." DAY) > SYSDATE()";
		}
		else{ $strQueryTime =""; }
		
		if($type != "alltickets"){
			$strQueryType = "AND channel = '".$type."'";
		}
		else{ $strQueryType =""; }
		
		if($tags !="alltags"){
			$tagsString=" FROM janak.assistly_cases c, janak.assistly_case_labels cl";
			$tagsString2=" AND c.id = cl.case_id
					      AND cl.label_name = '".$tags."'";
		}
		else{
			$tagsString=" FROM janak.assistly_cases c";
			$tagsString2="";
		}

		$sqlQuery = "SELECT username, COUNT(*) as countData
					 ".$tagsString."
					 WHERE username IS NOT NULL 
					 ".$tagsString2."
					 ".$strQueryType."
					 ".$strQueryTime."
					 GROUP BY 1
					 ORDER BY 2 DESC
					 ";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function ticketSearchForOffices($date = ""){
	
		global $db;
		
		if($date != "alltime"){ $strQuery = "AND DATE_ADD(ca.created_at, INTERVAL ".$date.") >= SYSDATE()"; }
		else{ $strQuery ="";}

		$sqlQuery = "SELECT o.name, COUNT(*) ticket_count
					  FROM janak.assistly_cases ca, janak.assistly_customers cu, rentjuice.users u, rentjuice.offices o
					 WHERE ca.customer_id = cu.id
					   AND cu.custom_user_id = u.id
					   AND u.office_id = o.id
					  ".$strQuery."
					 GROUP BY 1
					 ORDER BY 1
					 ";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function ticketSearchForOffices2($value="", $date=""){
	
		global $db;
		
		if($date != "alltime"){ $strQuery = "AND DATE_ADD(ca.created_at, INTERVAL ".$date.") >= SYSDATE()"; }
		else{ $strQuery ="";}
		
		$sqlQuery = "SELECT o.name,
						   ca.id ticket_id,
						   DATEDIFF(SYSDATE(),created_at) days_since,
						   ca.subject,
						   u.id user_id,
						   CONCAT(u.first_name,' ',u.last_name) username,
						   ca.preview
					  FROM janak.assistly_cases ca, janak.assistly_customers cu, rentjuice.users u, rentjuice.offices o
					 WHERE ca.customer_id = cu.id
					   AND cu.custom_user_id = u.id
					   AND u.office_id = o.id
					   AND o.name = '".$value."'
					   ".$strQuery."
					 ORDER BY ca.created_at DESC";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function ticketSearchForUsers($date=""){
	
		global $db;
		
		if($date != "alltime"){ $strQuery = "AND DATE_ADD(ca.created_at, INTERVAL ".$date.") >= SYSDATE()"; }
		else{ $strQuery =""; }

		$sqlQuery = "SELECT CONCAT(u.first_name,' ',u.last_name) username, COUNT(*) ticket_count
					  FROM janak.assistly_cases ca, janak.assistly_customers cu, rentjuice.users u, rentjuice.offices o
					 WHERE ca.customer_id = cu.id
					   AND cu.custom_user_id = u.id
					   AND u.office_id = o.id
					   ".$strQuery."
					 GROUP BY 1
					 ORDER BY 1
					 ";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function ticketSearchForUsers2($value="",$date=""){
	
		global $db;
		
		if($date != "alltime"){ $strQuery = "AND DATE_ADD(ca.created_at, INTERVAL ".$date.") >= SYSDATE()"; }
		else{ $strQuery =""; }

		$sqlQuery = "SELECT o.name,
						   ca.id ticket_id,
						   DATEDIFF(SYSDATE(),created_at) days_since,
						   ca.subject,
						   u.id user_id,
						   CONCAT(u.first_name,' ',u.last_name) username,
						   ca.preview
					  FROM janak.assistly_cases ca, janak.assistly_customers cu, rentjuice.users u, rentjuice.offices o
					 WHERE ca.customer_id = cu.id
					   AND cu.custom_user_id = u.id
					   AND u.office_id = o.id
					   AND CONCAT(u.first_name,' ',u.last_name) = '".$value."'
					   ".$strQuery."
					 ORDER BY ca.created_at DESC
					 ";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function syncStatus(){
	
		global $db;

		$sqlQuery = "SELECT * FROM janak.assistly_sync_logfiles
					 ORDER BY started_at desc";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function byOfficeType($move="",$time=""){
	
		global $db;

		$sqlQuery = "SELECT office_type_days.office_type,
					   office_type_days.created_at,
						/* The complex divide statement is so that moving averages work at the end of the data set
						   We either want to divide by the number of days for the moving average, for example 7 for a 7 day moving average.
						   or we want to divide by the number of days between now and the end of the data set      
						*/
					   (
						  SELECT COUNT(*)/IF(DATEDIFF(SYSDATE(),office_type_days.created_at) >= ".$move.", ".$move.", DATEDIFF(SYSDATE(),office_type_days.created_at))
							FROM janak.assistly_cases ca, janak.assistly_customers cu, rentjuice.offices o
						   WHERE ca.customer_id = cu.id
							 AND cu.custom_office_id = o.id
							 AND o.type = office_type_days.office_type
							 AND office_type_days.created_at <= DATE(created_at) AND DATE(created_at) <= DATE_ADD(office_type_days.created_at, INTERVAL ".($move - 1)." DAY)         
					   ) ticket_count2
				  FROM
					   (
						SELECT *
						  FROM
							   (SELECT code_value office_type
								  FROM janak.data_codes
								 WHERE code_name = 'office_type'
							   ) office_types,
							   (SELECT DATE_SUB(DATE(SYSDATE()),INTERVAL id DAY) created_at
								  FROM janak.counter_records
								 WHERE id <= ".$time.") days
					   ) office_type_days
				 ORDER BY office_type_days.created_at, office_type_days.office_type";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function resolutionTime($movingAverage = '',$timeSpan = '',$groupBy = ''){
	
		global $db;

		$sqlQuery = "SELECT days.created_at,

					   /* In order to get a moving average, we can't just take the average of resolution times for tickets
						  in the last 7 days. This is because an individual ticket is weighted more heavily if it is 1 of 2 tickets
						  on a paricular day, or 1 of 100. So we need to first calculate the daily values and then average those values
						  to get the moving average value.
					   */
					   (
						SELECT AVG(daily_resolution_time) moving_average
						  FROM (
								SELECT AVG(TIMESTAMPDIFF(MINUTE, created_at, resolved_at)/60) daily_resolution_time, ".$groupBy."    
								  FROM janak.assistly_cases
								 GROUP BY DATE(".$groupBy.")
							   ) daily_times
						  WHERE days.created_at <= DATE(".$groupBy.") AND DATE(".$groupBy.") <= DATE_ADD(days.created_at, INTERVAL ".$movingAverage." DAY)                        
					   ) hours_to_resolve
				  FROM
							 (SELECT DATE_SUB(DATE(SYSDATE()),INTERVAL id DAY) created_at
								  FROM janak.counter_records
								 WHERE id <= ".$timeSpan.") days
				 ORDER BY days.created_at ASC
				";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function casesByRegion($movingAverage = '',$timeSpan = '',$region = ''){
	
		global $db;
		
		$strQuery="";
		
		if($region != "viewall"){
			$strQuery ="SELECT r.name region, COUNT(*) ticket_count
					  FROM janak.assistly_cases ca, janak.assistly_customers cu, rentjuice.offices o, rentjuice.regions r
					 WHERE ca.customer_id = cu.id
					   AND cu.custom_office_id = o.id
					   AND o.region_id = r.id
					   AND DATE_ADD(created_at, INTERVAL ".$timeSpan." DAY) > SYSDATE()
					 GROUP BY 1
					 ORDER BY 2 DESC
					 LIMIT ".$region."";
		}
		else{
			$strQuery =" SELECT DISTINCT(r.name) region
                      FROM janak.assistly_cases ca, janak.assistly_customers cu, rentjuice.offices o, rentjuice.regions r
                     WHERE ca.customer_id = cu.id
                       AND cu.custom_office_id = o.id
                       AND o.region_id = r.id
                       AND DATE_ADD(created_at, INTERVAL ".$timeSpan." DAY) > SYSDATE()";
		}		   
				   

		$sqlQuery = "    SELECT region_days.region,
           region_days.created_at,
          
            /* The complex divide statement is so that moving averages work at the end of the data set
               We either want to divide by the number of days for the moving average, for example 7 for a 7 day moving average.
               or we want to divide by the number of days between now and the end of the data set      
            */
           (
              SELECT COUNT(*)/IF(DATEDIFF(SYSDATE(),region_days.created_at) >= ".$movingAverage.", ".$movingAverage.", DATEDIFF(SYSDATE(),region_days.created_at))
                FROM janak.assistly_cases ca, janak.assistly_customers cu, rentjuice.offices o, rentjuice.regions r
               WHERE ca.customer_id = cu.id
                 AND cu.custom_office_id = o.id
                 AND o.region_id = r.id            
                 AND r.name = region_days.region
                 AND region_days.created_at <= DATE(created_at) AND DATE(created_at) <= DATE_ADD(region_days.created_at, INTERVAL ".( $movingAverage - 1 )." DAY)         
           ) ticket_count2
      FROM
           (
            SELECT *
              FROM
                   (         
                   ".$strQuery."
                   ) regions,
                   (SELECT DATE_SUB(DATE(SYSDATE()),INTERVAL id DAY) created_at
                      FROM janak.counter_records
                     WHERE id <= ".$timeSpan.") days
           ) region_days
     ORDER BY region_days.created_at, region_days.region

				";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function interByChannels($move="",$time=""){
	
		global $db;

		$sqlQuery = "SELECT
					   channel_days.created_at,
					   channel_days.channel,
					   (
						SELECT COUNT(*)/IF(DATEDIFF(SYSDATE(),channel_days.created_at) >= ".$move.", ".$move.", DATEDIFF(SYSDATE(),channel_days.created_at))
						  FROM janak.assistly_interactions i
						 WHERE channel_days.created_at <= DATE(i.created_at) AND DATE(i.created_at) <= DATE_ADD(channel_days.created_at, INTERVAL ".($move - 1)." DAY)        
						   AND i.channel = channel_days.channel
						) interaction_count
					FROM
					   (
						SELECT *
						  FROM
							   (SELECT channel
									FROM janak.assistly_interactions
								   WHERE DATE_ADD(created_at, INTERVAL ".$time." DAY) > SYSDATE()
								   GROUP BY 1
							   ) channels,
								 (SELECT DATE_SUB(DATE(SYSDATE()),INTERVAL id DAY) created_at
									  FROM janak.counter_records
									 WHERE id <= ".$time.") days
					   ) channel_days
					   ORDER BY channel_days.created_at";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function interByAgent($move="",$time="",$type="",$direction = ""){
	
		global $db;
		
		if($type != "alltickets"){ 	$strQueryType = "AND i.channel = '".$type."'";  }
		else{ 	$strQueryType =""; 	}
		
		if($direction !="all"){ $strQueryDir = "AND i.direction = '".$direction."'"; }
		else{ $strQueryDir = ""; }

		$sqlQuery = "SELECT user_days.username,
					   user_days.created_at,
					   (
						  SELECT COUNT(*)/IF(DATEDIFF(SYSDATE(),user_days.created_at) >= ".$move.", ".$move.", DATEDIFF(SYSDATE(),user_days.created_at))
							FROM janak.assistly_interactions i, janak.assistly_users u
						   WHERE i.user_x = u.id
						   ".$strQueryType."
						   ".$strQueryDir."
							 AND u.name = user_days.username
								AND user_days.created_at <= DATE(i.created_at) AND DATE(i.created_at) <= DATE_ADD(user_days.created_at, INTERVAL ".($move - 1)." DAY)         
					   ) ticket_count
					FROM
					   (
						SELECT *
						  FROM
							   (SELECT u.name username
								  FROM janak.assistly_interactions i, janak.assistly_users u
								 WHERE u.name IS NOT NULL
								   AND i.user_x = u.id
								   ".$strQueryType."
								   ".$strQueryDir."
								   AND DATE_ADD(i.created_at, INTERVAL ".$time." DAY) > SYSDATE()        
								   GROUP BY 1
							   ) users,
								 (SELECT DATE_SUB(DATE(SYSDATE()),INTERVAL id DAY) created_at
									  FROM janak.counter_records
									 WHERE id <= ".$time.") days
					   ) user_days
					ORDER BY created_at, username ASC";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function salesForceApiModels(){
	
		global $db;

		$sqlQuery = "SELECT id, NAME, record_count, field_count, label, queryable, child_relationships, FIELDS
					  FROM janak.salesforce_models
					 ORDER BY NAME";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	
	
	public static function salesForceFields($id=''){
	
		global $db;

		$sqlQuery = "SELECT id,model_name, NAME, TYPE, LENGTH, label, calculated, calculated_formula, picklist_values, reference_to, relationship_name
					  FROM janak.salesforce_model_fields
					 WHERE model_id = ".$id."
					 ORDER BY Name
					";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function officeGrowth(){
	
		global $db;

		$sqlQuery = "SELECT t.created,
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
					";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function listingsGrowth(){
	
		global $db;

		$sqlQuery = "SELECT t.created,
						   (@agency_running_total := @agency_running_total + t.agency_count) AS agency_running_total,
						   (@landlord_running_total := @landlord_running_total + t.landlord_count) AS landlord_running_total      
					  FROM (
							SELECT LEFT(l.created,7) created,
								   SUM(IF(o.type = 'agency',1,0)) agency_count,
								   SUM(IF(o.type = 'landlord',1,0)) landlord_count              
							  FROM listings l, offices o
							 WHERE LEFT(l.created,7) != '0000-00'
							   AND o.id = l.office_id
							 GROUP BY 1
							 ORDER BY 1
						   ) t, (SELECT @agency_running_total := 0 AS dummy, @landlord_running_total := 0 AS dummy2) dummy
					";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function userGrowth(){
	
		global $db;

		$sqlQuery = "SELECT t.created,
						   (@agency_running_total := @agency_running_total + t.agency_count) AS agency_running_total,
						   (@landlord_running_total := @landlord_running_total + t.landlord_count) AS landlord_running_total      
					  FROM (
							SELECT LEFT(u.date_joined,7) created,
								   SUM(IF(o.type = 'agency',1,0)) agency_count,
								   SUM(IF(o.type = 'landlord',1,0)) landlord_count              
							  FROM users u, offices o
							 WHERE LEFT(u.date_joined,7) != '0000-00'
							   AND o.id = u.office_id
							 GROUP BY 1
							 ORDER BY 1
						   ) t, (SELECT @agency_running_total := 0 AS dummy, @landlord_running_total := 0 AS dummy2) dummy
					";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function superTagsDefinition(){
	
		global $db;

		$sqlQuery = "SELECT tag_count, cl.tag_name, t.tag_name super_tag_name
					  FROM ( 
							SELECT COUNT(*) tag_count, label_name tag_name
							  FROM janak.assistly_case_labels
							 GROUP BY label_name
						   ) cl
						   
						   LEFT OUTER JOIN janak.super_taggings st
							 ON cl.tag_name = st.label_name
						   LEFT OUTER JOIN janak.super_tags t
							 ON st.super_tag_id = t.id
					  
					 ORDER BY cl.tag_count desc";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function superTagPriorities(){
	
		global $db;

		$sqlQuery = "SELECT tag_name, priority FROM janak.super_tags ORDER BY priority";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function superTagUsageOvertime($valueTime='',$valueLabel='',$valueMove = ''){
	
		global $db;
		
		if($valueLabel !="alltags"){
			$label=" AND c.super_tag = '".$valueLabel."'";
		}
		else{
			$label="";
		}
		
		$sqlQuery = "SELECT
					   days.created_at,
					   (
						SELECT COUNT(*)/IF(DATEDIFF(SYSDATE(),days.created_at) >= ".$valueMove.", ".$valueMove.", DATEDIFF(SYSDATE(),days.created_at))
						  FROM janak.assistly_cases c
					
						 WHERE days.created_at <= DATE(c.created_at) AND DATE(c.created_at) <= DATE_ADD(days.created_at, INTERVAL ".($valueMove - 1)." DAY)            
						    ".$label."
					
						) ticket_count
					FROM
					
						  (SELECT DATE_SUB(DATE(SYSDATE()),INTERVAL id DAY) created_at
							   FROM janak.counter_records
							WHERE id <= ".$valueTime.") days
					
					ORDER BY days.created_at";
	
						
		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function superTagUsageFilter($value=''){

		global $db;
		
		if(!empty($value)){
			if($value != "alltime"){
				$strQuery = "WHERE DATE_ADD(c.created_at, INTERVAL ".$value." DAY) >= SYSDATE()";
			}
			else{
				$strQuery ="";
			}
		}
		else{
			$strQuery = "WHERE DATE_ADD(c.created_at, INTERVAL 7 DAY) >= SYSDATE()";
		}
	
		$sqlQuery = "SELECT COUNT(*) super_tag_count, super_tag
					  FROM janak.assistly_cases c
					 ".$strQuery."
					 GROUP BY super_tag
					 ORDER BY super_tag";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function superTagUsageLeader($value=''){
	
		global $db;
		
		if(!empty($value)){
			if($value != "alltime"){
				$strQuery = "WHERE DATE_ADD(created_at, INTERVAL ".$value." DAY) >= SYSDATE()";
			}
			else{
				$strQuery ="";
			}
		}
		else{
			$strQuery = "WHERE DATE_ADD(created_at, INTERVAL 28 DAY) >= SYSDATE()";
		}

		$sqlQuery = "SELECT c.super_tag, COUNT(*) as count_tag
					 FROM janak.assistly_cases c
					 ".$strQuery."
					 GROUP BY 1
					 ORDER BY 2 DESC
					 ";

		$st = $db->prepare($sqlQuery);
		$st->execute();
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
}

?>