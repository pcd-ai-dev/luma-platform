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

	if(isset($_POST['addLanguageStart'])){
		
		// Variables
		$nameLanguage = $secure->v('string', 'nameAdd', false) ?? '';
		$codeLanguage = $secure->v('string', 'codeAdd', false) ?? '';
			
	   //---------------------------------------------------------  
	   // Insertion Root DB
	   //--------------------------------------------------------- 
	
		$reqAddRoot = "INSERT INTO ".$prefixRoot."lang (name, code) VALUES(:name, :code)";
		$resAddRoot = $db->prepare($reqAddRoot);
		$resAddRoot->bindValue(':name', $nameLanguage, PDO::PARAM_STR);
		$resAddRoot->bindValue(':code', $codeLanguage, PDO::PARAM_STR);
		$resAddRoot->execute();
		$resAddRoot->closeCursor();
		$resAddRoot = NULL;

		$idLangInsert = $db->lastInsertId();

		$reqAddFooter = "INSERT INTO ".$prefixRoot."footer (idSite, idLang) VALUES(:idSite, :idLang)";
		$resAddRootFooter = $db->prepare($reqAddFooter);
		$resAddRootFooter->bindValue(':idSite', $idSite, PDO::PARAM_INT);
		$resAddRootFooter->bindValue(':idLang', $idLangInsert, PDO::PARAM_INT);
		$resAddRootFooter->execute();
		$resAddRootFooter->closeCursor();
		$resAddRootFooter = NULL;
			  
		// Msg modif
		$msgUpdate='
			<script type="text/javascript">
				showWarningMessage("builderMsgUpdate", "La langue a bien été enregistrée", "#81B929");
			</script>
		';
	}



   //---------------------------------------------------------  
   // Mod DB ADMINUSER
   //---------------------------------------------------------

	if(isset($_POST['modLanguageStart'])){
		
		// Variables
		$modNameLanguage = $secure->v('string', 'modNameLanguage', false) ?? '';
		$modCodeLanguage = $secure->v('string', 'modCodeLanguage', false) ?? '';

		//---------------------------------------------------------  
	    // Insertion DB
	    //--------------------------------------------------------- 
		
		$reqMR = "UPDATE ".$prefixRoot."lang SET name=:name, code=:code WHERE id=:modId";
		$resMR = $db->prepare($reqMR);
		$resMR->bindValue(':modId', $modId, PDO::PARAM_INT);
		$resMR->bindValue(':name', $modNameLanguage, PDO::PARAM_STR);
		$resMR->bindValue(':code', $modCodeLanguage, PDO::PARAM_STR);
		$resMR->execute();
		$resMR->closeCursor();
		$resMR = NULL;
			  
		// Msg modif
		$msgUpdate='
			<script type="text/javascript">
				showWarningMessage("builderMsgUpdate", "Modifications enregistrées", "#81B929");
			</script>
		';

	}


	//---------------------------------------------------------  
	// Variables
	//---------------------------------------------------------

		$rLang=$lang->infoLang($modId);