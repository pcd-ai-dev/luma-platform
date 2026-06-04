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


    //------------------------------------------------------------------------------------------------\\
	//------------------------------------------------------------------------------------------------\\ 
	// Add company
	//------------------------------------------------------------------------------------------------\\
    //------------------------------------------------------------------------------------------------\\


	if(isset($_POST['addSiteStart'])){
	
		// Variables
		$siteName=$secure->v('string', 'name', false) ?? '';
			
	   //---------------------------------------------------------  
	   // Insertion Root DB
	   //--------------------------------------------------------- 
	
		  $reqAddRoot = "INSERT INTO ".$prefixRoot."site (name) VALUES(:name)";
		  $resAddRoot = $db->prepare($reqAddRoot);
		  $resAddRoot->bindValue(':name', $siteName, PDO::PARAM_STR);
		  $resAddRoot->execute();
		  $resAddRoot->closeCursor();
		  $resAddRoot = NULL;
	
	}