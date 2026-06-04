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
   // Mod DB ADMINUSER
   //---------------------------------------------------------

	if(isset($_POST['modStart'])){
		
		// Variables

			$ressourceId      = $secure->v('int', 'adminId', false) ?? 0;
			$ressourceGender  = $secure->v('string', 'gender', false) ?? '';
			$ressourceFirstName = ucfirst(mb_convert_encoding($secure->v('string', 'first_name', false) ?? '', 'UTF-8', 'ISO-8859-1'));
			$ressourceLastName = ucfirst(mb_convert_encoding($secure->v('string', 'last_name', false) ?? '', 'UTF-8', 'ISO-8859-1'));

			$ressourceType    = $secure->v('int', 'type', false) ?? 0;
			$ressourceLevel   = $secure->v('int', 'level', false) ?? 0;
			$ressourceEmail   = $secure->v('string', 'emails', false) ?? '';
			$ressourcePhone   = $secure->v('string', 'phone', false) ?? '';
			$googleAgenda     = $secure->v('string', 'googleAgenda', false) ?? '';
			$ressourcePass    = $secure->v('string', 'pass', false) ?? '';
		
		if(!empty($ressourcePass)){
			$ressourceSalt=sha1($ressourceFirstName.$ressourceLastName.$ressourceType.$ressourceEmail);
			$ressourcePassSQL = sha1($ressourceSalt.$ressourcePass);
		}else{}
			
	
		//---------------------------------------------------------  
	    // Insertion Owner DB
	    //--------------------------------------------------------- 
		
		if(!empty($ressourcePass)){
			$reqadduser = "UPDATE ".$prefixAdmin."user SET gender=:gender, last_name=:lastname, first_name=:firstname, email=:email, phone=:phone, googleAgenda=:googleAgenda, password=:password, level=:level, type=:type, salt=:salt WHERE id=:adminId";
			$resadduser = $db->prepare($reqadduser);
			$resadduser->bindValue(':salt', $ressourceSalt, PDO::PARAM_STR);
			$resadduser->bindValue(':password', $ressourcePassSQL, PDO::PARAM_STR);
		}else{
			$reqadduser = "UPDATE ".$prefixAdmin."user SET gender=:gender, last_name=:lastname, first_name=:firstname, email=:email, phone=:phone, googleAgenda=:googleAgenda, level=:level, type=:type WHERE id=:adminId";
			$resadduser = $db->prepare($reqadduser);
		}
		
		$resadduser->bindValue(':gender', $ressourceGender, PDO::PARAM_STR);
		$resadduser->bindValue(':lastname', $ressourceLastName, PDO::PARAM_STR);
		$resadduser->bindValue(':firstname', $ressourceFirstName, PDO::PARAM_STR);
		$resadduser->bindValue(':email', $ressourceEmail, PDO::PARAM_STR);
		$resadduser->bindValue(':phone', $ressourcePhone, PDO::PARAM_STR);
		$resadduser->bindValue(':googleAgenda', $googleAgenda, PDO::PARAM_STR);
		$resadduser->bindValue(':level', $ressourceLevel, PDO::PARAM_STR);
		$resadduser->bindValue(':type', $ressourceType, PDO::PARAM_STR);
		$resadduser->bindValue(':adminId', $ressourceId, PDO::PARAM_STR);
		$resadduser->execute();
		$resadduser->closeCursor();
		$resadduser = NULL;

		// Msg modif
		$msgUpdate='
			<script type="text/javascript">
				showWarningMessage("builderMsgUpdate", "Modifications enregistrées", "#81B929");
			</script>
		';
	
	}