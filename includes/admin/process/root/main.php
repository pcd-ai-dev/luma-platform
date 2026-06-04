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
   // Mod DB ADMIN USER
   //---------------------------------------------------------

	if(isset($_POST['modStart'])){
		
		// Variables
		$siteName                  = $secure->v('string', 'name', false) ?? '';
		$varLink                   = $secure->v('string', 'varlink', false) ?? '';
		$contactRecipient          = $secure->v('string', 'contactRecipient', false) ?? '';
		$bgColor                   = $secure->v('string', 'bgColor', false) ?? '';
		$adminColor                = $secure->v('string', 'adminColor', false) ?? '#F7F7F7';
		$adminTextColor            = $secure->v('string', 'adminTextColor', false) ?? '#F7F7F7';

		$modPrivacyPageId		   = $secure->v('int', 'modPrivacyPageId', false) ?? 0;
		$modHomePageId             = $secure->v('int', 'modHomePageId', false) ?? 0;
		$modNewsPageId             = $secure->v('int', 'modNewsPageId', false) ?? 0;
		$modArtclePageId		   = $secure->v('int', 'modArtclePageId', false) ?? 0;
		$modContactSuccessPageId   = $secure->v('int', 'modContactSuccessPageId', false) ?? 0;



		//---------------------------------------------------------  
	    // Insertion DB
	    //--------------------------------------------------------- 
		
		$reqMR = "UPDATE ".$prefixRoot."site SET name=:name,
												varLink=:varlink, 
												contactRecipient=:contactRecipient,
												bgColor=:bgColor,
												adminColor=:adminColor,
												adminTextColor=:adminTextColor,
												privacyPage=:privacyPage,
												homePage=:homePage,
												newsPage=:newsPage,
												articlePage=:articlePage,
												contactSuccessPage=:contactSuccessPage
												WHERE id=:idSite";
		$resMR = $db->prepare($reqMR);
		$resMR->bindValue(':name', $siteName, PDO::PARAM_STR);
		$resMR->bindValue(':varlink', $varLink, PDO::PARAM_STR);
		$resMR->bindValue(':idSite', $idSite, PDO::PARAM_INT);
		$resMR->bindValue(':bgColor', $bgColor, PDO::PARAM_STR);
		$resMR->bindValue(':adminColor', $adminColor, PDO::PARAM_STR);
		$resMR->bindValue(':adminTextColor', $adminTextColor, PDO::PARAM_STR);
		$resMR->bindValue(':contactRecipient', $contactRecipient, PDO::PARAM_STR);
		$resMR->bindValue(':privacyPage', $modPrivacyPageId, PDO::PARAM_INT);
		$resMR->bindValue(':homePage', $modHomePageId, PDO::PARAM_INT);
		$resMR->bindValue(':newsPage', $modNewsPageId, PDO::PARAM_INT);
		$resMR->bindValue(':articlePage', $modArtclePageId, PDO::PARAM_INT);
		$resMR->bindValue(':contactSuccessPage', $modContactSuccessPageId, PDO::PARAM_INT);
		$resMR->execute();
		$resMR->closeCursor();
		$resMR = NULL;
		$_SESSION['siteData']['adminColor']=$adminColor;
		$_SESSION['siteData']['adminTextColor']=$adminTextColor;


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

		$rinfo=$root->infoSite($idSite);