<?php

/*
	This is the index file of our simple website.
	It routes request to the appropriate controllers
*/

require_once "includes/main.php";

try {
	
	if($_GET['page']){
		
		// get the start time of teh page
		$time = microtime();
		$time = explode(" ", $time);
		$time = $time[1] + $time[0];
		$start = $time;
		
		$c = new PageController($_GET['page'],$start);
	}
	else if(empty($_GET)){
		$c = new HomeController();
	}
	else throw new Exception('Wrong page!');
	
	$c->handleRequest();
	
}
catch(Exception $e) {

	// Display the error page using the "render()" helper function:
	render('error',array('message'=>$e->getMessage()));
	
}

?>