<?php

//var_dump($_SESSION['VADSRETURN']);


	//---------------------------------------------------------
	// REVISION STATUS
	//---------------------------------------------------------


		if($idRevision!=0){ 
			$htmlText=$string->removeBody($ir->html ?? '');
		}else{
			$htmlText=$string->removeBody($r->text ?? '');
		}


	//---------------------------------------------------------
	// DEFAULT PAGE
	//---------------------------------------------------------

		if($switchstatus==0){
			include($_SERVER['DOCUMENT_ROOT'].'/content/header/builder/process-init.php');	
		}


	//---------------------------------------------------------
	// ARTICLE LIST
	//---------------------------------------------------------

		if($switchstatus==2){
			include($_SERVER['DOCUMENT_ROOT'].'/content/header/builder/process-news-list.php');	
		}


	//---------------------------------------------------------
	// PAGE ARTICLE
	//---------------------------------------------------------

		if($switchstatus==3){
			include($_SERVER['DOCUMENT_ROOT'].'/content/header/builder/process-news-page.php');	
		}


	//---------------------------------------------------------
	// PAGE PHOTO GALLERY
	//---------------------------------------------------------

		if($switchstatus==5){
			include($_SERVER['DOCUMENT_ROOT'].'/content/header/builder/process-photos-gallery.php');	
		}


	//---------------------------------------------------------
	// PAGE VIDEO GALLERY
	//---------------------------------------------------------

		if($switchstatus==6){
			include($_SERVER['DOCUMENT_ROOT'].'/content/header/builder/process-videos-gallery.php');	
		}


	//---------------------------------------------------------
	// PRODUCT LIST
	//---------------------------------------------------------

		if($switchstatus==8){
			include($_SERVER['DOCUMENT_ROOT'].'/content/header/builder/process-product-list.php');
		}


	//---------------------------------------------------------
	// FORMULAIRE DE CONTACT
	//---------------------------------------------------------

		if($switchstatus==11){
			include($_SERVER['DOCUMENT_ROOT'].'/content/header/builder/process-contact.php');	
		}


	//---------------------------------------------------------
	// MAP
	//---------------------------------------------------------

		if($switchstatus==12){
			include($_SERVER['DOCUMENT_ROOT'].'/content/header/builder/process-map.php');	
		}


	//---------------------------------------------------------  
	// PAGE FORMATION
	//--------------------------------------------------------- 

		if($switchstatus==15){
			include($_SERVER['DOCUMENT_ROOT'].'/content/header/builder/process-formation.php');		
		}


?>
<style>
<?php 
	
	if($idRevision!=0){ 
		$cssText=$ir->css;
	}else{
		$cssText=$r->css;
	}

	echo $cssText;
?>
</style>