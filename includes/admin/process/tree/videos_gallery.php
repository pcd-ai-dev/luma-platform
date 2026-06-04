<?php

/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
*  @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*  @version  Release: 3
*/


    //--------------------------------------------------------------------------------\\
	//--------------------------------------------------------------------------------\\
	// Gestion WEBTV
	//--------------------------------------------------------------------------------\\
	//--------------------------------------------------------------------------------\\


    //---------------------------------------------------------
   	// INIT NAME SPACES
   	//---------------------------------------------------------

		use Front\VideoManager;

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


		$video= new VideoManager($db);

		$videoUId = uniqid('videoUId_');


    //---------------------------------------------------------  
	// Ajout  gallery
	//--------------------------------------------------------- 

		if(isset($_POST['addGalleryStart'])){

			$reqGalleryAdd = "INSERT INTO ".$prefixVideo."gallery (idCategory) VALUES(:idCategory)";
			$resGalleryAdd = $db->prepare($reqGalleryAdd);
			$resGalleryAdd->bindValue(':idCategory', $idCategory, PDO::PARAM_STR);
			$resGalleryAdd->execute();
			$resGalleryAdd->closeCursor();
			$resGalleryAdd = NULL;

			$idGallery=$db->lastInsertId();

		
			for($i=0; $i<$nbLang;$i++){
			
				$idLang=$tabLang[$i]['id'];
	
				$titleAdd = 'titleAdd_'.$idLang;
				${$titleAdd} = $secure->v('string', $titleAdd, false) ?? '';

				$descriptionAdd = 'descriptionAdd_' . $idLang;
				${$descriptionAdd} = $secure->v('string', $descriptionAdd, false) ?? '';
				
	
				$reqGallerylAdd = "INSERT INTO ".$prefixVideo."gallery_lang (idGallery,idLang,title,description,idCategory) VALUES(:idGallery, :idLang, :title, :description, :idCategory)";
				$resGallerylAdd = $db->prepare($reqGallerylAdd);
				$resGallerylAdd->bindValue(':idGallery',$idGallery, PDO::PARAM_STR);
				$resGallerylAdd->bindValue(':idLang', $idLang, PDO::PARAM_STR);
				$resGallerylAdd->bindValue(':title', ${$titleAdd}, PDO::PARAM_STR);
				$resGallerylAdd->bindValue(':description', ${$descriptionAdd}, PDO::PARAM_STR);
				$resGallerylAdd->bindValue(':idCategory', $idCategory, PDO::PARAM_STR);
				$resGallerylAdd->execute();
				$resGallerylAdd->closeCursor();
				$resGallerylAdd = NULL;
		
			}
			
		}


    //---------------------------------------------------------  
	// Modification gallery
	//--------------------------------------------------------- 
	
		if(isset($_POST['modGalleryStart'])){
		
    //---------------------------------------------------------  
	// Modification gallery db
	//--------------------------------------------------------- 
			
		$modUrl="";
		$modId=$secure->v('int','modId', false) ?? 0;

		$reqMod = "UPDATE ".$prefixVideo."gallery SET url=:modUrl WHERE id=:modId";
		$resMod = $db->prepare($reqMod);
		$resMod->bindValue(':modUrl', $modUrl, PDO::PARAM_STR);
		$resMod->bindValue(':modId', $modId, PDO::PARAM_STR);
		$resMod->execute();
		$resMod->closeCursor();
		$resMod = NULL; 
		
		for($i=0; $i<$nbLang;$i++){
			
			$idLang=$tabLang[$i]['id'];
				
			$modTitle = 'modTitleGallery_' . $idLang;
			${$modTitle} = $secure->v('string', $modTitle, false) ?? '';

			$modDescription = 'modDescriptionGallery_' . $idLang;
			${$modDescription} = $secure->v('string', $modDescription, false) ?? '';
	

			$reqGalleryMod = "UPDATE ".$prefixVideo."gallery_lang SET title=:modTitle, description=:modDescription, idCategory=:idCategory WHERE idGallery=:modId AND idLang=:idLang";
			$resGalleryMod = $db->prepare($reqGalleryMod);
			$resGalleryMod->bindValue(':modId', $modId, PDO::PARAM_STR);
			$resGalleryMod->bindValue(':idLang', $idLang, PDO::PARAM_STR);
			$resGalleryMod->bindValue(':modTitle', ${$modTitle}, PDO::PARAM_STR);
			$resGalleryMod->bindValue(':modDescription', ${$modDescription}, PDO::PARAM_STR);
			$resGalleryMod->bindValue(':idCategory', $idCategory, PDO::PARAM_STR);
			$resGalleryMod->execute();
			$resGalleryMod->closeCursor();
			$resGalleryMod = NULL;
		
		}
		
		$modStatus=0;
		$modId=0;

        // Msg modif
		$msgUpdate='
			<script type="text/javascript">
				showWarningMessage("builderMsgUpdate", "Modifications enregistrées", "#81B929");
			</script>
		';
		
	}



    //---------------------------------------------------------  
	// Sélection mod gallery
	//--------------------------------------------------------- 

		if(isset($_GET['mod'])) {
			$reqsmod = "SELECT * FROM ".$prefixVideo."gallery WHERE id=:mod";
			$ressmod = $db->prepare($reqsmod);
			$ressmod->execute(array(':mod'=>$modId));
			$rsmod=$ressmod->fetch(PDO::FETCH_OBJ);
			$ressmod->closeCursor();
			$ressmod = NULL;

		}
		
		
		
    //---------------------------------------------------------  
	// Sélection affichage
	//--------------------------------------------------------- 

		$req = "SELECT p.id, p.url, p.date, p.idCategory, p.status, l.id, l.idGallery, l.idLang, l.title, l.description
				FROM ".$prefixVideo."gallery p
				INNER JOIN ".$prefixVideo."gallery_lang l
				ON p.id=l.idGallery
				AND l.idLang='1'
				WHERE p.idCategory=:idCategory
				ORDER BY p.position asc";
		$res = $db->prepare($req);
		$res->bindParam(':idCategory', $idCategory, PDO::PARAM_INT);
		$res->execute();
		$nb=$res->rowCount();
		$tab = $res->fetchAll();
		$res->closeCursor();
		$res = NULL;