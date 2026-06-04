<?php

/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
*  @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*  @version  Release: 3
*/

    //---------------------------------------------------------
   	// INIT NAME SPACES
   	//---------------------------------------------------------

		use Xml\XmlManager;
		

	//---------------------------------------------------------  
	// FILE SECURE
	//---------------------------------------------------------  

		if (!$session->getAdminData()) {
			header('HTTP/1.1 403 Forbidden');
			exit('Accès interdit');
		}


    //---------------------------------------------------------  
	// INIT CLASS
	//--------------------------------------------------------- 

		$xml= new XmlManager($db,$idSite,$siteName,$siteHost, $imgDefaultSquare);

	
    //---------------------------------------------------------  
	// Vars
	//---------------------------------------------------------

		$idCategory=$secure->v('int', 'category', false) ?? 0;
		$tabItem=$secure->v('int', 'tab', false) ?? 0;
		$statusForm=$secure->v('int', 'status', false) ?? 0;
		$modId=$secure->v('int', 'mod', false) ?? 0;

		
   //---------------------------------------------------------  
   // INFO CAT
   //---------------------------------------------------------

		$r=$page->infoCat($idCategory,1);
	

    //---------------------------------------------------------  
	// LANGUAGE
	//--------------------------------------------------------- 
	
		$tabLang=$lang->activeListLang();
		$nbLang=count($tabLang);


    //---------------------------------------------------------  
	// INCLUDES
	//--------------------------------------------------------- 		
		
		include 'tree.php';
		include 'category.php';
		
		if($param=='param_content'){
			include ADMINPROCESSPATH.'tree/main.php';
			include ADMINPROCESSPATH.'tree/header.php';
			include ADMINPROCESSPATH.'tree/body.php';
			if(($string->setInt($r?->type)==3)||(($string->setInt($r?->type)==1)&&($string->setInt($r?->switch)==1))||(($string->setInt($r?->type)==1)&&($string->setInt($r?->switch)==2))){include 'post.php';}
			if(($string->setInt($r?->type)==1)&&($string->setInt($r?->switch)==5)){include 'gallery.php';}
			if(($string->setInt($r?->type)==1)&&($string->setInt($r?->switch)==6)){include 'videos_gallery.php';}
			if(($string->setInt($r?->type)==1)&&($string->setInt($r?->switch)==7)){include 'audio_gallery.php';}
			if(($string->setInt($r?->type)==1)&&($string->setInt($r?->switch)==8)){include 'product.php';}
			if(($string->setInt($r?->type)==1)&&($string->setInt($r?->switch)==10)){include 'location.php';}
			if((($string->setInt($r?->type)==1)&&($string->setInt($r?->switch)==11))){include 'contact.php';}
			if((($string->setInt($r?->type)==1)&&($string->setInt($r?->switch)==12))){include 'map.php';}
		}


    //---------------------------------------------------------  
	// STATUS
	//---------------------------------------------------------

		$modStatus=0;
		$rp=$page->infoPage($idCategory,1);
		$rc=$page->infoChildCat($idCategory);
		

	//---------------------------------------------------------  
	// CATEGORY TREE
	//--------------------------------------------------------- 

		$rt = $page->getCatTree($idSite);
		$catTree = $rt['catTree'];
		$tabcat = $rt['tab'];


    //---------------------------------------------------------  
	// LEARN LIST
	//--------------------------------------------------------- 

		if(($r?->type==1)&&($r?->switch==15)){ 
			$reqL = "SELECT * FROM ".$prefixLearn."category WHERE idSite=:idSite ORDER BY position ASC";
			$resL = $db->prepare($reqL);
			$resL->bindValue(':idSite', $idSite, PDO::PARAM_INT);
			$resL->execute();
			$nbL=$resL->rowCount();
			$tabL = $resL->fetchAll();
			$resL->closeCursor();
			$resL = NULL;
		}