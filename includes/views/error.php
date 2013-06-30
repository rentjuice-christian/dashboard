<?php

//header("HTTP/1.0 404 Not Found");
//render('_header',array('title'=>'Error'))

?>

<div style="min-width: 400px; height: 250px; margin: 0 auto"> 

	<div class="align_center bold error_title">There is an error in the page</div>
	<div class="align_center error_content"> <?php echo $error_message; ?></div>

</div>


<?php //render('_footer') ?>