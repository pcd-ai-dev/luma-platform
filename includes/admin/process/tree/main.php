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
	// Modification de la page
	//--------------------------------------------------------- 

	//---------------------------------------------------------  
	// INIT FOLDER
	//--------------------------------------------------------- 

		$Rep_Page=$_SERVER['DOCUMENT_ROOT'].'/img/pages/'.$idCategory;
		$Rep_Header=$Rep_Page.'/header';
		$Rep_Header_Images=$Rep_Page.'/header/images';
		$Rep_Header_Rec = $Rep_Page.'/header/rec';
		$Rep_Header_Square = $Rep_Page.'/header/square';

		$Rep_Page_Gallery=$Rep_Page.'/gallery';
		$Rep_Page_Gallery_Images=$Rep_Page_Gallery.'/images';
		$Rep_Page_Gallery_Rec = $Rep_Page_Gallery.'/rec';
		$Rep_Page_Gallery_Square = $Rep_Page_Gallery.'/square';
		
		if (is_dir($Rep_Page)) {
	  	}else{
        	if (mkdir($Rep_Page)){

				if (mkdir($Rep_Header)){
					if (mkdir($Rep_Header_Images)){}
					if (mkdir($Rep_Header_Rec)){}
					if (mkdir($Rep_Header_Square)){}
				}

				if (mkdir($Rep_Page_Gallery)){
					if (mkdir($Rep_Page_Gallery_Images)){}
					if (mkdir($Rep_Page_Gallery_Rec)){}
					if (mkdir($Rep_Page_Gallery_Square)){}
				}
          }else{}
		}

	//---------------------------------------------------------  
	// MOD PROCESS
	//--------------------------------------------------------- 
   
		if(isset($_POST['modCategoryStart'])){

		// Récupération variable
	
		$parent = $secure->v('int', 'parent', false) ?? 0;
		$idCategory = $secure->v('int', 'idCategory', false) ?? 0;
		$link = $secure->v('string', 'link', false) ?? '';
		$type = $secure->v('int', 'type', false) ?? 0;
		$switch = $secure->v('int', 'switch', false) ?? 0;
		$checkPosition = $secure->v('int', 'checkPosition', false) ?? 0;
		$idPage = $secure->v('int', 'idPage', false) ?? 0;
		$idLearn = $secure->v('int', 'formation', false) ?? 0;

		$name = ucfirst($secure->v('string', 'name', false) ?? '');
	
	
   //---------------------------------------------------------  
   // Modification db category
   //---------------------------------------------------------

		
		$reqcmodif = "UPDATE ".$prefixRoot."category SET link=:link, idPage=:idPage, type=:type, switch=:switch, idLearn=:idLearn WHERE id=:idCategory";
		$rescmodif = $db->prepare($reqcmodif);
		$rescmodif->bindValue(':link', $link, PDO::PARAM_STR);
		$rescmodif->bindValue(':idPage', $idPage, PDO::PARAM_INT);
		$rescmodif->bindValue(':idLearn', $idLearn, PDO::PARAM_INT);
		$rescmodif->bindValue(':type', $type, PDO::PARAM_INT);
		$rescmodif->bindValue(':switch', $switch, PDO::PARAM_INT);
		$rescmodif->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
		$rescmodif->execute();
		$rescmodif->closeCursor();
		$rescmodif = NULL;
		
		
		for($i=0; $i<$nbLang;$i++){
			
			$idLang=$tabLang[$i]['id'];
			$name='name_'.$idLang;
			${$name} = ucfirst($secure->v('string', $name, false) ?? '');
		
		
	//---------------------------------------------------------  
	// Check idLang
	//---------------------------------------------------------
	
			$reql = "SELECT * FROM ".$prefixRoot."category_lang WHERE idCategory=:idCategory AND idLang=:idLang";			 
			$resl = $db->prepare($reql);
			$resl->bindValue(':idCategory', $idCategory, PDO::PARAM_STR);
			$resl->bindValue(':idLang', $idLang, PDO::PARAM_STR);
			$resl->execute();
			$nbl=$resl->rowCount();
			$resl->closeCursor();
			$resl = NULL;
			
			
			if($nbl==0){
			
			//---------------------------------------------------------  
			// Insertion db langues category
			//---------------------------------------------------------
				
		   //---------------------------------------------------------  
		   // Insertion db langue category
		   //---------------------------------------------------------
		   
				$reqcaddl = "INSERT INTO ".$prefixRoot."category_lang (idCategory,name,idLang) VALUES(:idCategory, :name, :idLang)";
				$rescaddl = $db->prepare($reqcaddl);
				$rescaddl->bindValue(':name', ${$name}, PDO::PARAM_STR);
				$rescaddl->bindValue(':idCategory', $idCategory, PDO::PARAM_STR);
				$rescaddl->bindValue(':idLang', $idLang, PDO::PARAM_STR);
				$rescaddl->execute();
				$rescaddl->closeCursor();
				$rescaddl = NULL;
				
				
		   //---------------------------------------------------------  
		   // Insertion db nouvelle  page
		   //---------------------------------------------------------
		
				$reqpadd = "INSERT INTO ".$prefixRoot."pages (idCategory,name,idLang) VALUES(:idCategory,:name,:idLang)";
				$respadd = $db->prepare($reqpadd);
				$respadd->bindValue(':idCategory', $idCategory, PDO::PARAM_STR);
				$respadd->bindValue(':name', ${$name}, PDO::PARAM_STR);
				$respadd->bindValue(':idLang', $idLang, PDO::PARAM_STR);
				$respadd->execute();
				$respadd->closeCursor();
				$respadd = NULL;
		
			}else{
		
		   //---------------------------------------------------------  
		   // Modification db langues category
		   //---------------------------------------------------------
					
				$reqcaddl = "UPDATE ".$prefixRoot."category_lang SET name=:name WHERE idCategory=:idCategory AND idLang=:idLang";
				$rescaddl = $db->prepare($reqcaddl);
				$rescaddl->bindValue(':name', ${$name}, PDO::PARAM_STR);
				$rescaddl->bindValue(':idCategory', $idCategory, PDO::PARAM_STR);
				$rescaddl->bindValue(':idLang', $idLang, PDO::PARAM_STR);
				$rescaddl->execute();
				$rescaddl->closeCursor();
				$rescaddl = NULL;
			
			
		   //---------------------------------------------------------  
		   // modification db page
		   //---------------------------------------------------------
			
			if(empty($_POST['revision_'.$idLang])){
				
				$reqpadd = "UPDATE ".$prefixRoot."pages SET name=:name WHERE idCategory=:idCategory AND idLang=:idLang";
				$respadd = $db->prepare($reqpadd);
				$respadd->bindValue(':idCategory', $idCategory, PDO::PARAM_STR);
				$respadd->bindValue(':name', ${$name}, PDO::PARAM_STR);
				$respadd->bindValue(':idLang', $idLang, PDO::PARAM_STR);
				$respadd->execute();
				$respadd->closeCursor();
				$respadd = NULL;
				
			}else{
				
				// revision
				
				$ir=$page->infoRevision($_POST['revision_'.$idLang], $idLang);
				
				$reqpadd = "UPDATE ".$prefixRoot."pages SET name=:name, text=:html, css=:css, components=:components, styles=:styles WHERE idCategory=:idCategory AND idLang=:idLang";
				$respadd = $db->prepare($reqpadd);
				$respadd->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
				$respadd->bindValue(':name', ${$name}, PDO::PARAM_STR);
				$respadd->bindValue(':idLang', $idLang, PDO::PARAM_INT);
				$respadd->bindValue(':html', (isset($ir->html))? $ir->html: '', PDO::PARAM_STR);
				$respadd->bindValue(':css', (isset($ir->css))? $ir->css : '', PDO::PARAM_STR);
				$respadd->bindValue(':components', (isset($ir->components))? $ir->components : '', PDO::PARAM_STR);
				$respadd->bindValue(':styles', (isset($ir->styles))? $ir->styles : '', PDO::PARAM_STR);
				$respadd->execute();
				$respadd->closeCursor();
				$respadd = NULL;
				
			}
		}
	}
		
	// Msg modif
	$msgUpdate='
		<script type="text/javascript">
			showWarningMessage("builderMsgUpdate", "Modifications enregistrées", "#81B929");
		</script>
	';

	$r=$page->infoCat($idCategory,1);
	$xml->updateSitemap();

}