<?php

/* This controller renders the home page */

class BarGraphs{
	
		
	public static function graphsQueueLength($value=''){
	
		global $db;
		global $db_old;
		global $dbsource;

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

			try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
		
		//$st = $db->prepare($sqlQuery);
		//$st->execute();

		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
		
	
	}
	
	public static function ticketsByWeekDay(){
	
		global $db;
		global $db_old;
		global $dbsource;

		$sqlQuery = "SELECT WEEKDAY(created_at) as weekday, DAYNAME(created_at) as dayname, COUNT(*) as count
					 FROM janak.assistly_cases
					 GROUP BY 1
					 ORDER BY 1";

		try {
			$st = $db->prepare($sqlQuery);
			$st->execute();
			$dbsource = "RJ Analytics Database";
		}
		catch (PDOException $e) {
			$st = $db_old->prepare($sqlQuery);
			$st->execute();
			$dbsource = "Slave Ops Database";
		}

		/*$st = $db->prepare($sqlQuery);
		$st->execute();*/
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	public static function ticketsByMonth(){
	
		global $db;
		global $db_old;
		global $dbsource;

		$sqlQuery = "SELECT YEAR(created_at) as year, MONTH(created_at) as month, MONTHNAME(created_at) as monthname, COUNT(*) as count
					 FROM janak.assistly_cases
					 GROUP BY 1,2
					 ORDER BY 1";

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function dailyNewTicketsAllTime(){
	
		global $db;
		global $db_old;
		global $dbsource;

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

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function dailyNewTickets($value=''){
	
		global $db;
		global $db_old;
		global $dbsource;

		$sqlQuery = "SELECT DATE(created_at) as created_at, COUNT(*) as average_tickets
					 FROM janak.assistly_cases
					 WHERE DATE_ADD(created_at, INTERVAL ".$value." DAY) >= SYSDATE()
					 GROUP BY 1
					 ORDER BY 1
					";

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function totalListingsByMarket($value=''){
	
		global $db;
		global $db_old;
		global $dbsource;
		
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

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	public static function manualMergesReport($value=''){
	
		global $db;
		global $db_old;
		global $dbsource;
		
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

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function tagUsageLeaderBoard($value=''){
	
		global $db;
		global $db_old;
		global $dbsource;
		
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

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function tagUsageFilter($value='',$tickettype=''){
	
		global $db;
		global $db_old;
		global $dbsource;

		if(!empty($value)){
			if($value != "alltime"){$strQuery = "AND DATE_ADD(created_at, INTERVAL ".$value." DAY) >= SYSDATE()"; }
			else{$strQuery ="";}
		}
		else{$strQuery = "AND DATE_ADD(created_at, INTERVAL 7 DAY) >= SYSDATE()";}
		
		if($tickettype != "alltickets"){ 
			if(!empty($tickettype)){
				$strTicketType = " AND c.channel = '".$tickettype."'";  
			}
			else{ $strTicketType =""; }
		}
		else{ $strTicketType =""; }
					 
		$sqlQuery = "
					 SELECT label_name, COUNT(*) as count_tag
					 FROM janak.assistly_case_labels cl, janak.assistly_cases c
					 WHERE cl.case_id = c.id
					 ".$strQuery."
					 ".$strTicketType."
					 GROUP BY 1
					 ORDER BY 1
					 
					";
					 
		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function tagUsageOvertime($valueTime='',$valueLabel='',$valueMove = ''){
	
		global $db;
		global $db_old;
		global $dbsource;
		
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


		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function onBoardingTicketsbyStatus($value=''){
	
		global $db;
		global $db_old;
		global $dbsource;
		
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

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function dataImportsOverTime(){
	
		global $db;
		global $db_old;
		global $dbsource;

		$sqlQuery = "SELECT LEFT(o.created_on,7) as createdon, COUNT(DISTINCT l.office_id) as countoffice
						FROM rentjuice.offices o INNER JOIN rentjuice.listings l ON l.office_id = o.id
						WHERE (l.import_reference_id IS NOT NULL AND import_reference_id != '')
						AND o.name NOT LIKE \"%OLD DE ACCOUNT%\"
						AND o.dataentry = 0
						AND o.id != 4463
						GROUP BY 1
						ORDER BY 1
					 ";

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function listingsImportedOverTime(){
	
		global $db;
		global $db_old;
		global $dbsource;

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


		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function dataEntryJobs($value = ''){
	
		global $db;
		global $db_old;
		global $dbsource;
		
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

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function selectSuperTag($date='',$tickettype=''){
	
		global $db;
		global $db_old;
		global $dbsource;
		
		$strTicketType = "";
		
		if($date != "alltime"){ 
			$strDate = " WHERE DATE_ADD(c.created_at, INTERVAL ".$date." DAY) >= SYSDATE()"; 
		}
		else{ 
			$strDate =""; 
		}
		
		if($tickettype != "alltickets"){ 
			if($date == "alltime"){ $strTicketType .= " WHERE "; }
			else{ $strTicketType .= " AND "; }
			$strTicketType .= " c.channel = '".$tickettype."'";  
		}
		else{ 
			$strTicketType =""; 
		}
		
	
		$sqlQuery = "SELECT COUNT(*) super_tag_count, super_tag
					  FROM janak.assistly_cases c
					 ".$strDate."
					 ".$strTicketType."  
					 GROUP BY super_tag
					 ORDER BY super_tag
					 ";

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function ticketsInboundByUser($date='',$tickettype='',$supertag='',$tags=''){
	
		global $db;
		global $db_old;
		global $dbsource;
		
		if($date != "alltime"){ $strDate = " AND DATE_ADD(ca.created_at, INTERVAL ".$date." DAY) >= SYSDATE()"; }
		else{ $strDate =""; }
		
		if($tickettype != "alltickets"){ $strTicketType = " AND ca.channel = '".$tickettype."'";  }
		else{ $strTicketType =""; }
		
		if($supertag != "allsupertags"){ $strSuperTag = " AND ca.super_tag = '".$supertag."'"; }
		else{ $strSuperTag =""; }
		
		if($tags != "alltags"){ 
			$strTag = " AND ca.id = cl.case_id AND cl.label_name = '".$tags."'"; 
			$strTag2 = " ,janak.assistly_case_labels cl";
		}
		else{ $strTag =""; 	}
		
		$sqlQuery = "SELECT cu.custom_user_id, CONCAT(u.first_name,' ',u.last_name) username, o.name officename, o.id office_id, COUNT(*) ticket_count, SUM(TIMESTAMPDIFF(MINUTE,first_opened_at,resolved_at)) handle_time_total
					  FROM janak.assistly_cases ca, janak.assistly_customers cu, rentjuice.users u, rentjuice.offices o ".$strTag2."
					 WHERE ca.customer_id = cu.id
					   AND u.id = cu.custom_user_id
					   AND u.office_id = o.id
					  ".$strDate."
					  ".$strTicketType."
					  ".$strSuperTag."
					  ".$strTag."
					 GROUP BY 1
					 ORDER BY COUNT(*) DESC
					 LIMIT 100
					 ";

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function ticketsInboundByUserTop20($value=''){
	
		global $db;
		global $db_old;
		global $dbsource;
		
		if($value != "alltime"){ $strQuery = "AND DATE_ADD(ca.created_at, INTERVAL ".$value." DAY) >= SYSDATE()"; }
		else{ $strQuery =""; }
		
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

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function ticketsInboundByUserOther($value=''){
	
		global $db;
		global $db_old;
		global $dbsource;
		
		if($value != "alltime"){ $strQuery = "AND DATE_ADD(ca.created_at, INTERVAL ".$value." DAY) >= SYSDATE()"; }
		else{ $strQuery =""; }
		
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

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	
	public static function ticketsInboundByOffice($date='',$tickettype='',$supertag='',$tags=''){
	
		global $db;
		global $db_old;
		global $dbsource;

		if($date != "alltime"){ $strDate = " AND DATE_ADD(ca.created_at, INTERVAL ".$date." DAY) >= SYSDATE()"; }
		else{ $strDate =""; }
		
		if($tickettype != "alltickets"){ $strTicketType = " AND ca.channel = '".$tickettype."'";  }
		else{ $strTicketType =""; }
		
		if($supertag != "allsupertags"){ $strSuperTag = " AND ca.super_tag = '".$supertag."'"; }
		else{ $strSuperTag =""; }
		
		if($tags != "alltags"){ 
			$strTag = " AND ca.id = cl.case_id AND cl.label_name = '".$tags."'"; 
			$strTag2 = " ,janak.assistly_case_labels cl";
		}
		else{ $strTag =""; 	}

		$sqlQuery = "SELECT o.id office_id, o.name as officename, COUNT(*) as ticket_count, SUM(TIMESTAMPDIFF(MINUTE,first_opened_at,resolved_at)) handle_time_total
					  FROM janak.assistly_cases ca, janak.assistly_customers cu, rentjuice.users u, rentjuice.offices o ".$strTag2."
					 WHERE ca.customer_id = cu.id
					   AND u.id = cu.custom_user_id
					   AND u.office_id = o.id
					  ".$strDate."
					  ".$strTicketType."
					  ".$strSuperTag."
					  ".$strTag."
					 GROUP BY 1
					 ORDER BY COUNT(*) DESC
					LIMIT 100
					 ";

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function ticketsInboundByOfficeTop20($value=''){
	
		global $db;
		global $db_old;
		global $dbsource;
		

		if($value != "alltime"){$strQuery = "AND DATE_ADD(ca.created_at, INTERVAL ".$value." DAY) >= SYSDATE()";}
		else{$strQuery ="";}


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

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function ticketsInboundByOfficeOther($value=''){
	
		global $db;
		global $db_old;
		global $dbsource;
		
		if($value != "alltime"){$strQuery = "AND DATE_ADD(ca.created_at, INTERVAL ".$value." DAY) >= SYSDATE()";}
		else{$strQuery ="";}

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

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function resolvedBySuperTagUsername($timespan=''){
	
		global $db;
		global $db_old;
		global $dbsource;
		
		$sqlQuery = "SELECT u.last_name, u.first_name, u.id rentjuice_user_id, COUNT(*) as user_count
					  FROM janak.assistly_cases ca, rentjuice.users u
					 WHERE ca.rentjuice_user_id = u.id
					   AND DATE_ADD(DATE(ca.resolved_at), INTERVAL ".$timespan." DAY) > SYSDATE()
					 GROUP BY rentjuice_user_id
					 ORDER BY last_name, first_name
					 ";

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function resolvedBySuperTagOffice($timespan=''){
	
		global $db;
		global $db_old;
		global $dbsource;
		
		$sqlQuery = "SELECT o.name, o.id rentjuice_office_id, COUNT(*) ticket_counts
					  FROM janak.assistly_cases ca, rentjuice.offices o
					 WHERE ca.rentjuice_office_id = o.id
					   AND DATE_ADD(DATE(ca.resolved_at), INTERVAL ".$timespan." DAY) > SYSDATE()
					 GROUP BY rentjuice_office_id
					 ORDER BY NAME
					 ";

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function resolvedBySuperTag($timespan="",$movingaverage="",$type="",$office='',$users=''){
	
		global $db;
		global $db_old;
		global $dbsource;
		
		if($type != "alltickets"){ 	$strQueryType = "AND channel = '".$type."'";  }
		else{ 	$strQueryType =""; 	}
		
		if($office != "all"){ 	$strQueryOffice = "AND c.rentjuice_office_id = ".$office."";  }
		else{ 	$strQueryOffice =""; 	}
		
		if($users != "all"){ 	$strQueryUsers = "AND c.rentjuice_user_id = ".$users."";  }
		else{ 	$strQueryUsers =""; 	}
		

		$sqlQuery = "SELECT tag_days.tag_name,
						   tag_days.resolved_at,
						   (
							SELECT COUNT(*)/IF(DATEDIFF(SYSDATE(),tag_days.resolved_at) >= ".$movingaverage.", ".$movingaverage.", DATEDIFF(SYSDATE(),tag_days.resolved_at))
							  FROM janak.assistly_cases c
							 WHERE c.super_tag = tag_days.tag_name
							  ".$strQueryType."
							  ".$strQueryOffice."
							  ".$strQueryUsers."
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

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function resolvedByAgent($timespan="",$movingaverage="",$type="",$tags=""){
	
		global $db;
		global $db_old;
		global $dbsource;
	
		
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
					 
		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function resolvedByAgent2($timespan="",$type="",$tags=""){
	
		global $db;
		global $db_old;
		global $dbsource;
		
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

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function ticketSearchForOffices($date = ""){
	
		global $db;
		global $db_old;
		global $dbsource;
		
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

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function ticketSearchForOffices2($value="", $date=""){
	
		global $db;
		global $db_old;
		global $dbsource;
		
		if($date != "alltime"){ $strQuery = "AND DATE_ADD(ca.created_at, INTERVAL ".$date.") >= SYSDATE()"; }
		else{ $strQuery ="";}
		
		$sqlQuery = "SELECT o.name,
						   ca.id ticket_id,
						   DATEDIFF(SYSDATE(),created_at) days_since,
						   ca.subject,
						   u.id user_id,
						   CONCAT(u.first_name,' ',u.last_name) username,
						   ca.preview,
						   channel, 
						   super_tag,
						   TIMESTAMPDIFF(MINUTE,first_opened_at,resolved_at) handle_time_total
					  FROM janak.assistly_cases ca, janak.assistly_customers cu, rentjuice.users u, rentjuice.offices o
					 WHERE ca.customer_id = cu.id
					   AND cu.custom_user_id = u.id
					   AND u.office_id = o.id
					   AND o.name = '".$value."'
					   ".$strQuery."
					 ORDER BY ca.created_at DESC";

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function ticketSearchForUsers($date=""){
	
		global $db;
		global $db_old;
		global $dbsource;
		
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

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function ticketSearchForUsers2($value="",$date=""){
	
		global $db;
		global $db_old;
		global $dbsource;
		
		if($date != "alltime"){ $strQuery = "AND DATE_ADD(ca.created_at, INTERVAL ".$date.") >= SYSDATE()"; }
		else{ $strQuery =""; }

		$sqlQuery = "SELECT o.name,
						   ca.id ticket_id,
						   DATEDIFF(SYSDATE(),created_at) days_since,
						   ca.subject,
						   u.id user_id,
						   CONCAT(u.first_name,' ',u.last_name) username,
						   ca.preview,
						   channel,
						   ca.super_tag,
						   TIMESTAMPDIFF(MINUTE,first_opened_at,resolved_at) handle_time_total
					  FROM janak.assistly_cases ca, janak.assistly_customers cu, rentjuice.users u, rentjuice.offices o
					 WHERE ca.customer_id = cu.id
					   AND cu.custom_user_id = u.id
					   AND u.office_id = o.id
					   AND CONCAT(u.first_name,' ',u.last_name) = '".$value."'
					   ".$strQuery."
					 ORDER BY ca.created_at DESC
					 ";

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function syncStatus(){
	
		global $db;
		global $db_old;
		global $dbsource;

		$sqlQuery = "SELECT * FROM janak.assistly_sync_logfiles
					 ORDER BY started_at desc
					 LIMIT 100
					 ";

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function salesForceSyncStatus(){
	
		global $db;
		global $db_old;
		global $dbsource;

		$sqlQuery = "SELECT * FROM janak.salesforce_sync_logfiles
					 ORDER BY started_at desc";

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function byOfficeType($move="",$time=""){
	
		global $db;
		global $db_old;
		global $dbsource;

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

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function resolutionTime($movingAverage = '',$timeSpan = '',$groupBy = ''){
	
		global $db;
		global $db_old;
		global $dbsource;

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

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function casesByRegion($movingAverage = '',$timeSpan = '',$region = ''){
	
		global $db;
		global $db_old;
		global $dbsource;
		
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

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function interByChannels($move="",$time=""){
	
		global $db;
		global $db_old;
		global $dbsource;

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

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function interByAgent($move="",$time="",$type="",$direction = ""){
	
		global $db;
		global $db_old;
		global $dbsource;
		
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

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function salesForceApiModels(){
	
		global $db;
		global $db_old;
		global $dbsource;

		$sqlQuery = "SELECT id, NAME, record_count, field_count, label, queryable, child_relationships, FIELDS
					  FROM janak.salesforce_models
					 ORDER BY NAME";

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	
	
	public static function salesForceFields($name=''){
	
		global $db;
		global $db_old;
		global $dbsource;
		
		if(!empty($name)){ $strQuery = "WHERE model_name = '".$name."'"; }
		else{ $strQuery = ""; }

		$sqlQuery = "SELECT id,model_name, NAME, TYPE, LENGTH, label, calculated, calculated_formula, picklist_values, reference_to, relationship_name
					  FROM janak.salesforce_model_fields
					 ".$strQuery."
					 ORDER BY Name
					";

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function salesForceRelationships($name=''){
	
		global $db;
		global $db_old;
		global $dbsource;
		
		if(!empty($name)){ 
			$strQuery = "WHERE s.parent_object = '".$name."'";
			$strQuery2 = "WHERE s.child_object = '".$name."'";  
		}
		else{ 
			$strQuery = ""; 
			$strQuery2 = "";
		}

		$sqlQuery = "SELECT parent_object parent, child_object child, s.relationship_name, s.parent_field
					  FROM janak.salesforce_model_relationships s
					 ".$strQuery."
					
					UNION
					
					SELECT parent_object parent, child_object child, s.relationship_name, s.parent_field
					  FROM janak.salesforce_model_relationships s
					 ".$strQuery2."
					";

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function officeGrowth(){
	
		global $db;
		global $db_old;
		global $dbsource;

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

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function listingsGrowth(){
	
		global $db;
		global $db_old;
		global $dbsource;

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

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function userGrowth(){
	
		global $db;
		global $db_old;
		global $dbsource;

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

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function superTagsDefinition(){
	
		global $db;
		global $db_old;
		global $dbsource;

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

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function superTagPriorities(){
	
		global $db;
		global $db_old;
		global $dbsource;

		$sqlQuery = "SELECT tag_name, priority FROM janak.super_tags ORDER BY priority";

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function superTagUsageOvertime($valueTime='',$valueLabel='',$valueMove = ''){
	
		global $db;
		global $db_old;
		global $dbsource;
		
		if($valueLabel !="alltags"){ $label=" AND c.super_tag = '".$valueLabel."'"; }
		else{ $label=""; }
		
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
	
						
		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function superTagUsageFilter($value=''){

		global $db;
		global $db_old;
		global $dbsource;
		
		if(!empty($value)){
			if($value != "alltime"){ $strQuery = "WHERE DATE_ADD(c.created_at, INTERVAL ".$value." DAY) >= SYSDATE()"; }
			else{ $strQuery =""; }
		}
		else{ $strQuery = "WHERE DATE_ADD(c.created_at, INTERVAL 7 DAY) >= SYSDATE()"; }
	
		$sqlQuery = "SELECT COUNT(*) super_tag_count, super_tag
					  FROM janak.assistly_cases c
					 ".$strQuery."
					 GROUP BY super_tag
					 ORDER BY super_tag";

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function superTagUsageLeader($value=''){
	
		global $db;
		global $db_old;
		global $dbsource;
		
		if(!empty($value)){
			if($value != "alltime"){ $strQuery = "WHERE DATE_ADD(created_at, INTERVAL ".$value." DAY) >= SYSDATE()"; }
			else{ $strQuery =""; }
		}
		else{ $strQuery = "WHERE DATE_ADD(created_at, INTERVAL 28 DAY) >= SYSDATE()"; }

		$sqlQuery = "SELECT c.super_tag, COUNT(*) as count_tag
					 FROM janak.assistly_cases c
					 ".$strQuery."
					 GROUP BY 1
					 ORDER BY 2 DESC
					 ";

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function duplicateAccounts(){
	
		global $db;
		global $db_old;
		global $dbsource;

		$sqlQuery = "SELECT c1.id customer_id, c1.name, c1.sforce_acct_id, c1.created_on, c1.status, c1.*
						  FROM rentjuice.customers c1,
							   (
								SELECT CAST(sforce_acct_id AS BINARY) sforce_acct_id, COUNT(*)
								  FROM rentjuice.customers
								 WHERE sforce_acct_id IS NOT NULL
								 GROUP BY 1
								 HAVING COUNT(*) > 1
								 ORDER BY COUNT(*) DESC
							   ) c2
						 WHERE c1.sforce_acct_id = c2.sforce_acct_id
						ORDER BY c1.sforce_acct_id
						";

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function badSalesforceID($status='',$show=''){
	
		global $db;
		global $db_old;
		global $dbsource;
		
		if($status == 'exclude') { $strQuery1= 'AND o.status <> "free"'; }
		else{ $strQuery1=''; }
		if($show = 'no'){ $strQuery2='AND (so.id IS NOT NULL OR sl.id IS NOT NULL)';}
		else{ $strQuery2=''; }

		$sqlQuery = 'SELECT o.sforce_acct_id, o.id, o.created_on, o.name,
						   CONCAT(IF(ISNULL(so.id),"","Salesforce ID is a Opportunity"),
								  IF(ISNULL(sl.id),"","Salesforce ID is a Lead"),
								  IF(ISNULL(sa.id),"","Salesforce ID is an Account")) notes
					  FROM rentjuice.customers o
						   LEFT OUTER JOIN
						   janak.salesforce_opportunities so
						   ON o.sforce_acct_id = so.id_15
						   LEFT OUTER JOIN
						   janak.salesforce_leads sl
						   ON o.sforce_acct_id = sl.id_15
						   LEFT OUTER JOIN
						   janak.salesforce_accounts sa
						   ON o.sforce_acct_id = sa.id_15
					 WHERE (so.id IS NOT NULL OR sl.id IS NOT NULL)
					 '.$strQuery1.' '.$strQuery2.'
					 ORDER BY o.created_on DESC
					';

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function noLastName(){
	
		global $db;
		global $db_old;
		global $dbsource;

		$sqlQuery = 'SELECT id user_id, first_name, last_name, TYPE user_type, date_joined
					   FROM rentjuice.users u
					  WHERE (last_name = "" OR last_name IS NULL)
					  ORDER BY id desc
					';

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function fonalitySalesforce(){
	
		global $db;
		global $db_old;
		global $dbsource;

		$sqlQuery = 'SELECT CONCAT_WS(",",IF(LENGTH(a.id),"Account",NULL),
                     IF(LENGTH(l.id),"Lead",NULL),
                     IF(LENGTH(c.id),"Contact",NULL),
                     IF(LENGTH(o.id),"Opportunity",NULL)) connection_type, COUNT(*) COUNT

					  FROM janak.salesforce_users u, janak.salesforce_fonality_phonecalls f
						   LEFT OUTER JOIN janak.salesforce_accounts a
						   ON f.fonality_uae_account_custom = a.id
						   LEFT OUTER JOIN janak.salesforce_contacts c
						   ON f.fonality_uae_customontact_custom = c.id
						   LEFT OUTER JOIN janak.salesforce_leads l
						   ON f.fonality_uae_lead_custom = l.id      
						   LEFT OUTER JOIN janak.salesforce_opportunities o
						   ON f.fonality_uae_opportunity_custom = o.id             
					 WHERE u.id = f.owner_id
					 GROUP BY 1
					 ORDER BY 2 DESC
					';

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function timeOnPhone($movingaverage = '', $timespan = ''){
	
		global $db;
		global $db_old;
		global $dbsource;

		$sqlQuery = 'SELECT user_days.username,
					   user_days.created_at,
				
					   (
						SELECT SUM(fonality_uae_duration_custom)/IF(DATEDIFF(SYSDATE(),user_days.created_at) >= '.$movingaverage.', '.$movingaverage.', DATEDIFF(SYSDATE(),user_days.created_at))
						  FROM janak.salesforce_fonality_phonecalls
						 WHERE owner_id = user_days.owner_id
						   AND user_days.created_at <= DATE(created_date) AND DATE(created_date) <= DATE_ADD(user_days.created_at, INTERVAL '.($movingaverage - 1).' DAY) 
					   ) ticket_count
				  FROM  
					   (
						SELECT *
						  FROM
							   (SELECT DISTINCT(owner_id) owner_id, u.name username
								  FROM janak.salesforce_fonality_phonecalls f, janak.salesforce_users u
								 WHERE DATE_ADD(f.created_date, INTERVAL '.$timespan.' DAY) > SYSDATE()
								   AND u.id = f.owner_id
							   ) users,
							   (SELECT DATE_SUB(DATE(SYSDATE()),INTERVAL id DAY) created_at
								  FROM janak.counter_records
								 WHERE id <= '.$timespan.'
							   ) days
					   ) user_days
				 ORDER BY user_days.created_at, username ASC 
					';

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function numberOfCalls($movingaverage = '', $timespan = ''){
	
		global $db;
		global $db_old;
		global $dbsource;

		$sqlQuery = 'SELECT user_days.username,
						   user_days.created_at,
					
						   (
							SELECT COUNT(*)/IF(DATEDIFF(SYSDATE(),user_days.created_at) >= '.$movingaverage.', '.$movingaverage.', DATEDIFF(SYSDATE(),user_days.created_at))
							  FROM janak.salesforce_fonality_phonecalls
							 WHERE owner_id = user_days.owner_id
							   AND user_days.created_at <= DATE(created_date) AND DATE(created_date) <= DATE_ADD(user_days.created_at, INTERVAL '.($movingaverage - 1).' DAY)  
						   ) ticket_count
					  FROM   
						   (
							SELECT *
							  FROM
								   (SELECT DISTINCT(owner_id) owner_id, u.name username
									  FROM janak.salesforce_fonality_phonecalls f, janak.salesforce_users u
									 WHERE DATE_ADD(f.created_date, INTERVAL '.$timespan.' DAY) > SYSDATE()
									   AND u.id = f.owner_id
								   ) users,
								   (SELECT DATE_SUB(DATE(SYSDATE()),INTERVAL id DAY) created_at
									  FROM janak.counter_records
									 WHERE id <= '.$timespan.'
								   ) days
						   ) user_days
					 ORDER BY user_days.created_at, username ASC ';

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function longestCalls($time=''){
	
		global $db;
		global $db_old;
		global $dbsource;

		$sqlQuery = "SELECT u.name,
					   fonality_uae_phone_number_custom,
					   fonality_uae_duration_custom,
					   a.name account_name,
					   c.name contact_name,
					   l.name lead_name,
					   o.name opportunity_name,
					   a.id account_id,
					   c.id contact_id,
					   l.id lead_id,
					   o.id opportunity_id
				  FROM janak.salesforce_users u, janak.salesforce_fonality_phonecalls f
					   LEFT OUTER JOIN janak.salesforce_accounts a
					   ON f.fonality_uae_account_custom = a.id
					   LEFT OUTER JOIN janak.salesforce_contacts c
					   ON f.fonality_uae_customontact_custom = c.id
					   LEFT OUTER JOIN janak.salesforce_leads l
					   ON f.fonality_uae_lead_custom = l.id       
					   LEFT OUTER JOIN janak.salesforce_opportunities o
					   ON f.fonality_uae_opportunity_custom = o.id              
				 WHERE DATE_ADD(f.created_date, INTERVAL ".$time." DAY) > SYSDATE()
				   AND u.id = f.owner_id
				 ORDER BY fonality_uae_duration_custom DESC
				 LIMIT 1000
				";

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function listingSourcesOvertime($time='',$status=''){
	
		global $db;
		global $db_old;
		global $dbsource;
		
		if($status =="active"){ $sqlQuery="l.active = 1 AND "; }
		if($status =="all"){ $sqlQuery=""; }		

		$sqlQuery = 'SELECT days.created_on,
					   (
						SELECT 
							   COUNT(l.id)/IF(DATEDIFF(SYSDATE(),days.created_on) >= 7, 7, DATEDIFF(SYSDATE(),days.created_on)) feed_sync_count
						  FROM rentjuice.listings l
						 WHERE '.$sqlQuery.' l.listing_source IN ("nsync","zif")
						   AND days.created_on <= DATE(l.created) AND DATE(l.created) <= DATE_ADD(days.created_on, INTERVAL 6 DAY) 
					   ) feed_sync_count,
					   (       
						SELECT 
							   COUNT(l.id)/IF(DATEDIFF(SYSDATE(),days.created_on) >= 7, 7, DATEDIFF(SYSDATE(),days.created_on)) import_count
						  FROM rentjuice.listings l
						 WHERE '.$sqlQuery.' l.listing_source IN ("custom import","ditch","diw","postlets")
						   AND days.created_on <= DATE(l.created) AND DATE(l.created) <= DATE_ADD(days.created_on, INTERVAL 6 DAY)
					   ) import_count,
					   (
						SELECT 
							   COUNT(l.id)/IF(DATEDIFF(SYSDATE(),days.created_on) >= 7, 7, DATEDIFF(SYSDATE(),days.created_on)) data_entry_count
						  FROM rentjuice.listings l
						 WHERE '.$sqlQuery.' l.listing_source = "data entry"
						   AND days.created_on <= DATE(l.created) AND DATE(l.created) <= DATE_ADD(days.created_on, INTERVAL 6 DAY) 
					   ) data_entry_count,
					   (
						SELECT 
							   COUNT(l.id)/IF(DATEDIFF(SYSDATE(),days.created_on) >= 7, 7, DATEDIFF(SYSDATE(),days.created_on)) manual_count
						  FROM rentjuice.listings l
						 WHERE '.$sqlQuery.' l.listing_source = "manual"
						   AND days.created_on <= DATE(l.created) AND DATE(l.created) <= DATE_ADD(days.created_on, INTERVAL 6 DAY)          
					   ) manual_count
				  FROM  
					   (SELECT DATE_SUB(DATE(SYSDATE()),INTERVAL id DAY) created_on
						  FROM janak.counter_records
						 WHERE id <= '.$time.') days
				 ORDER BY days.created_on ';

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function listingsBySource($time=''){
	
		global $db;
		global $db_old;
		global $dbsource;

		$sqlQuery = 'SELECT "feed sync" AS "source", COUNT(l.id) AS count_data
					  FROM rentjuice.listings l
					 WHERE l.active = 1
					   AND l.listing_source IN ("nsync","zif")
					   AND DATE(l.created) >= DATE_SUB(CURDATE() , INTERVAL '.$time.' DAY)
					UNION
					SELECT "import" AS "source", COUNT(l.id) AS count_data
					  FROM rentjuice.listings l
					 WHERE l.active = 1
					   AND l.listing_source IN ("custom import","ditch","diw","postlets")
					   AND DATE(l.created) >= DATE_SUB(CURDATE() , INTERVAL '.$time.' DAY)
					UNION
					SELECT "data entry" AS "source", COUNT(l.id) AS count_data
					  FROM rentjuice.listings l
					 WHERE l.active = 1
					   AND l.listing_source = "data entry"
					   AND DATE(l.created) >= DATE_SUB(CURDATE() , INTERVAL '.$time.' DAY)
					UNION
					SELECT "manual" AS "source", COUNT(l.id) AS count_data
					  FROM rentjuice.listings l
					 WHERE l.active = 1
					   AND l.listing_source = "manual"
					   AND DATE(l.created) >= DATE_SUB(CURDATE() , INTERVAL '.$time.' DAY) 
				';

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	public static function ticketsCustomerType($time=''){
	
		global $db;
		global $db_old;
		global $dbsource;

		$sqlQuery = "SELECT day_customer_type.created_on,
						   day_customer_type.customer_type,
						   (
							SELECT 
								   COUNT(*)/IF(DATEDIFF(SYSDATE(),day_customer_type.created_on) >= 7, 7, DATEDIFF(SYSDATE(),day_customer_type.created_on)) ticket_count
							  FROM janak.assistly_cases ca
							 WHERE ca.rentjuice_customer_type = day_customer_type.customer_type
							   AND day_customer_type.created_on <= DATE(ca.created_at) AND DATE(ca.created_at) <= DATE_ADD(day_customer_type.created_on, INTERVAL 6 DAY) 
						   ) ticket_count
					  FROM  
						   (SELECT *
							  FROM
								   (SELECT DATE_SUB(DATE(SYSDATE()),INTERVAL id DAY) created_on
									  FROM janak.counter_records
									 WHERE id <= ".$time.") days,
								   ( SELECT DISTINCT(rentjuice_customer_type) customer_type
									   FROM janak.assistly_cases
									  WHERE INSTR(rentjuice_customer_type,'free') <> 0
										 OR INSTR(rentjuice_customer_type,'paid') <> 0) customer_type
						   ) day_customer_type                   
					 ORDER BY day_customer_type.created_on
				     ";

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
	
	public static function runningTotalByAgent($time=''){
	
		global $db;
		global $db_old;
		global $dbsource;

		$sqlQuery = "SELECT resolved_at,
					   username,
					   CASE
						   WHEN username = 'Simon Kwan' AND HOUR(resolved_at) = 0 THEN (@simon_running_total := 0)   
						   WHEN username = 'Phillip Spiegel' AND HOUR(resolved_at) = 0 THEN (@phillip_running_total := 0)   
						   WHEN username = 'Kevin Burriss' AND HOUR(resolved_at) = 0 THEN (@kevin_running_total := 0)   
						   WHEN username = 'Eric Montero' AND HOUR(resolved_at) = 0 THEN (@eric_running_total := 0)   
						   WHEN username = 'Jennifer Wong' AND HOUR(resolved_at) = 0 THEN (@jen_running_total := 0)   
						   WHEN username = 'Ian Long' AND HOUR(resolved_at) = 0 THEN (@ian_running_total := 0)                           
					   END AS daily_reset,
					   CASE
						   WHEN username = 'Simon Kwan' THEN (@simon_running_total := @simon_running_total + ticket_count)
						   WHEN username = 'Phillip Spiegel' THEN (@phillip_running_total := @phillip_running_total + ticket_count)
						   WHEN username = 'Kevin Burriss' THEN (@kevin_running_total := @kevin_running_total + ticket_count)
						   WHEN username = 'Eric Montero' THEN (@eric_running_total := @eric_running_total + ticket_count)
						   WHEN username = 'Jennifer Wong' THEN (@jen_running_total := @jen_running_total + ticket_count)
						   WHEN username = 'Ian Long' THEN (@ian_running_total := @ian_running_total + ticket_count)
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
										 WHERE id <= ".$time."
									   ) hours
							   ) user_hours
							   LEFT OUTER JOIN
							   (SELECT username, DATE_FORMAT(resolved_at, '%Y-%m-%d %h:00:00') resolved_at, COUNT(*) ticket_count
								  FROM janak.assistly_cases ca
								 WHERE DATE_SUB(SYSDATE(), INTERVAL ".$time." HOUR) <= ca.resolved_at
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
				     ";

		try {
				$st = $db->prepare($sqlQuery);
				$st->execute();
				$dbsource = "RJ Analytics Database";
			}
			catch (PDOException $e) {
				$st = $db_old->prepare($sqlQuery);
				$st->execute();
				$dbsource = "Slave Ops Database";
			}
	
		return $st->fetchAll(PDO::FETCH_CLASS, "BarGraphs");
	
	}
	
}

?>