
<!-- JS Files -->
<script type="text/javascript">
	var idCategory="<?= (isset($idCategory))? intval($idCategory) : 0; ?>";
	var idSite="<?= (isset($idSite))? intval($idSite) : 0; ?>";
	var varLink="<?= (isset($is->varLink))? htmlspecialchars($is->varLink, ENT_QUOTES, 'UTF-8') : ''; ?>";
	var randLearn="<?= (isset($randLearn))? htmlspecialchars($randLearn, ENT_QUOTES, 'UTF-8') : ''; ?>";
	var formationAmount="<?= (isset($formationAmount))? htmlspecialchars($formationAmount, ENT_QUOTES, 'UTF-8') : ''; ?>";
	
</script>
<!-- /JS Files -->

<?php

	//---------------------------------------------------------  
	// DEFAULT PAGE
	//--------------------------------------------------------- 

		if($switchstatus==0){
			include($_SERVER['DOCUMENT_ROOT'].'/content/footer/builder/process-init.php');	
		}

		
	//---------------------------------------------------------  
	// HOME PAGE
	//--------------------------------------------------------- 

		if($switchstatus==1){
			include($_SERVER['DOCUMENT_ROOT'].'/content/footer/builder/process-home.php');	
		}

	//---------------------------------------------------------  
	// ARTICLE LIST
	//--------------------------------------------------------- 

		if($switchstatus==2){
			include($_SERVER['DOCUMENT_ROOT'].'/content/footer/builder/process-news-list.php');	
		}


	//---------------------------------------------------------  
	// PAGE ARTICLE
	//--------------------------------------------------------- 

		if($switchstatus==3){
			include($_SERVER['DOCUMENT_ROOT'].'/content/footer/builder/process-news-page.php');	
		}


	//---------------------------------------------------------  
	// PAGE PHOTO GALLERY
	//--------------------------------------------------------- 

		if($switchstatus==5){
			include($_SERVER['DOCUMENT_ROOT'].'/content/footer/builder/process-photos-gallery.php');	
		}


	//---------------------------------------------------------  
	// PAGE VIDEO GALLERY
	//--------------------------------------------------------- 

		if($switchstatus==6){
			include($_SERVER['DOCUMENT_ROOT'].'/content/footer/builder/process-videos-gallery.php');	
		}


	//---------------------------------------------------------  
	// PRODUCT LIST
	//---------------------------------------------------------

		if($switchstatus==8){
			include($_SERVER['DOCUMENT_ROOT'].'/content/footer/builder/process-product-list.php');
		}


	//---------------------------------------------------------  
	// PRODUCT PAGE
	//--------------------------------------------------------- 

		if($switchstatus==9){
			include($_SERVER['DOCUMENT_ROOT'].'/content/footer/builder/process-product-page.php');	
		}


	//---------------------------------------------------------  
	// IMPLANTATION
	//--------------------------------------------------------- 

		if($switchstatus==10){
			include($_SERVER['DOCUMENT_ROOT'].'/content/footer/builder/process-location.php');
		}

	//---------------------------------------------------------  
	// FORMULAIRE DE CONTACT
	//--------------------------------------------------------- 

		if($switchstatus==11){
			include($_SERVER['DOCUMENT_ROOT'].'/content/footer/builder/process-contact.php');	
		}

	//---------------------------------------------------------  
	// MAP
	//--------------------------------------------------------- 

		if($switchstatus==12){
			include($_SERVER['DOCUMENT_ROOT'].'/content/footer/builder/process-map.php');	
		}

	//---------------------------------------------------------
	// SUMMARY
	//---------------------------------------------------------

		if($switchstatus==14){
			include($_SERVER['DOCUMENT_ROOT'].'/content/footer/builder/process-summary.php');	
		}