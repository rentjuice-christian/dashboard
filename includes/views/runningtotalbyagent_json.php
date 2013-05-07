
<?php
	
	$newOptions = array();

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

	

	$getValue = $barcontent;

	foreach($getValue as $value){

		$arrayValues = explode(" ",$value->resolved_at);
				
		$arrayValuesDate = explode("-",$arrayValues[0]);
		$arrayValuesTime = explode(":",$arrayValues[1]);
		
		$getmonth = $arrayValuesDate[1] - 1;
		$reindex = $arrayValuesDate[0].",".$getmonth.",".$arrayValuesDate[2].",".$arrayValuesTime[0];
		
		$newOptions[$value->username][$reindex] = $value->running_ticket_count;

		 
	}

	$i = -1;
    foreach($newOptions as $key2 => $value2){
		//echo $key2."=>".$value2."<br/>";
		$i = $i + 1;
		$newOptions2[$i]["name"]=$key2;
		$newOptions2[$i]["data"]=$value2;
	}
   $i++;

	print json_encode($newOptions2);
	/*echo"<pre>";
		print_r($newOptions2);
	echo"</pre>";*/
?>

