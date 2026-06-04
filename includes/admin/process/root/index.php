<?php

/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
*  @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*  @version  Release: 3
*/


	//---------------------------------------------------------  
	// FILE SECURE
	//---------------------------------------------------------  

		if (!$session->getAdminData()) {
			header('HTTP/1.1 403 Forbidden');
			exit('Accès interdit');
		}

		
    //---------------------------------------------------------  
	// VARIABLES
	//---------------------------------------------------------

		$idSite=$secure->v('int', 'category', false) ?? 0;
		$tabItem=$secure->v('int', 'tab', false) ?? 0;
		$statusForm=$secure->v('int', 'status', false) ?? 0;
		$modId=$secure->v('int', 'mod', false) ?? 0;

		$siteUId = uniqid('rootUId_');


   //---------------------------------------------------------  
   // TREE
   //--------------------------------------------------------- 

		if($param=="param_content"){
			include 'main.php';
			include 'language.php';
		}


	//---------------------------------------------------------  
	// CATEGORY TREE
	//--------------------------------------------------------- 

		$rt = $page->getCatTree($idSite);
		$catTree = $rt['catTree'];
		$tabCat = $rt['tab'];


    //---------------------------------------------------------  
	// LANGUAGE
	//--------------------------------------------------------- 
	
		$tabLang=$lang->listLang();
		$nbLang=count($tabLang);
		

   //---------------------------------------------------------  
   // Response
   //--------------------------------------------------------- 
		
		$modStatus="0";