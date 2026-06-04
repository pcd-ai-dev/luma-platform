<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */

	//---------------------------------------------------------  
	// REVISION STATUS
	//--------------------------------------------------------- 

?>


<?php if(($switchstatus=="5")||($switchstatus=="1")):?>
<?php else:?>
<div id="builderContainer">
<?php endif; ?>
<?php 
	
	//---------------------------------------------------------  
	// DEFAULT PAGE
	//--------------------------------------------------------- 

		if($switchstatus==0){
			include($_SERVER['DOCUMENT_ROOT'].'/content/pages/builder/process-init.php');	
		}


	//---------------------------------------------------------  
	// ARTICLE LIST
	//--------------------------------------------------------- 

		if($switchstatus==2){
			include($_SERVER['DOCUMENT_ROOT'].'/content/pages/builder/process-news-list.php');	
		}


	//---------------------------------------------------------  
	// PAGE ARTICLE
	//--------------------------------------------------------- 

		if($switchstatus==3){
			include($_SERVER['DOCUMENT_ROOT'].'/content/pages/builder/process-news-page.php');	
		}


	//---------------------------------------------------------  
	// PAGE PHOTO GALLERY
	//--------------------------------------------------------- 

		if($switchstatus==5){
			include($_SERVER['DOCUMENT_ROOT'].'/content/pages/builder/process-photos-gallery.php');	
		}


	//---------------------------------------------------------  
	// PAGE VIDEO GALLERY
	//--------------------------------------------------------- 

		if($switchstatus==6){
			include($_SERVER['DOCUMENT_ROOT'].'/content/pages/builder/process-videos-gallery.php');	
		}


	//---------------------------------------------------------  
	// FORMULAIRE DE CONTACT
	//--------------------------------------------------------- 

		if($switchstatus==11){
			include($_SERVER['DOCUMENT_ROOT'].'/content/pages/builder/process-contact.php');	
		}

	//---------------------------------------------------------  
	// MAP
	//--------------------------------------------------------- 

		if($switchstatus==12){
			include($_SERVER['DOCUMENT_ROOT'].'/content/pages/builder/process-map.php');	
		}


	//---------------------------------------------------------  
	// EDITION
	//--------------------------------------------------------- 	

	
		echo (isset($textBuilder))? $textBuilder : '';

?>

</div>