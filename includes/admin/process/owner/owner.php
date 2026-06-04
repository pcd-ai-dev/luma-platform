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


	if(isset($_POST['addStart'])){
	
		// Variables
		$ressourceGender   = ucwords($secure->v('string', 'gender', false) ?? '');

		$ressourceFirstName = ucfirst(mb_convert_encoding($secure->v('string', 'first_name', false) ?? '', 'UTF-8', 'ISO-8859-1'));

		$ressourceLastName = ucfirst(mb_convert_encoding($secure->v('string', 'last_name', false) ?? '', 'UTF-8', 'ISO-8859-1'));

		$ressourceType     = $secure->v('int', 'type', false) ?? 0;
		$ressourceLevel    = $secure->v('string', 'level', false) ?? '';
		$ressourceEmail    = $secure->v('string', 'emails', false) ?? '';
		$ressourcePhone    = $secure->v('string', 'phone', false) ?? '';
		$googleAgenda      = $secure->v('string', 'googleAgenda', false) ?? '';
		$ressourcePass     = $secure->v('string', 'pass', false) ?? '';
			
		$ressourceSalt=sha1($ressourceFirstName.$ressourceLastName.$ressourceType.$ressourceEmail);
		$ressourcePassSQL = sha1($ressourceSalt.$ressourcePass);
			
		$datecdt=date("Y-m-d");
		$heurecdt=date("H:i:s");
		$dateitem=$datecdt.' '.$heurecdt;
			
			
	   //---------------------------------------------------------  
	   // Insertion Owner DB
	   //--------------------------------------------------------- 
	
		  $reqadduser = "INSERT INTO ".$prefixAdmin."user (idSite,date,gender,last_name,first_name,email,phone,googleAgenda,password,level,salt) VALUES(:idSite, :dateuser, :genderuser, :lastnameuser, :firstnameuser, :emailuser, :phoneuser, :googleAgendaUser, :passuser, :leveluser, :salt)";
		  $resadduser = $db->prepare($reqadduser);
		  $resadduser->bindValue(':idSite', $adminData['idSite'], PDO::PARAM_STR);
		  $resadduser->bindValue(':genderuser', $ressourceGender, PDO::PARAM_STR);
		  $resadduser->bindValue(':dateuser', $dateitem, PDO::PARAM_STR);
		  $resadduser->bindValue(':lastnameuser', $ressourceLastName, PDO::PARAM_STR);
		  $resadduser->bindValue(':firstnameuser', $ressourceFirstName, PDO::PARAM_STR);
		  $resadduser->bindValue(':emailuser', $ressourceEmail, PDO::PARAM_STR);
		  $resadduser->bindValue(':phoneuser', $ressourcePhone, PDO::PARAM_STR);
		  $resadduser->bindValue(':googleAgendaUser', $googleAgenda, PDO::PARAM_STR);
		  $resadduser->bindValue(':passuser', $ressourcePassSQL, PDO::PARAM_STR);
		  $resadduser->bindValue(':leveluser', $ressourceLevel, PDO::PARAM_STR);
		  $resadduser->bindValue(':salt', $ressourceSalt, PDO::PARAM_STR);
		  $resadduser->execute();
		  $resadduser->closeCursor();
		  $resadduser = NULL;
			  

	
	}