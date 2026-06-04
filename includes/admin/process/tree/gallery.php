<?php

/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
*  @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*  @version  Release: 3
*/

    //---------------------------------------------------------
   	// INIT NAME SPACES
   	//---------------------------------------------------------

		use Front\PhotoManager;

	//---------------------------------------------------------  
	// FILE SECURE
	//---------------------------------------------------------  

		if (!$session->getAdminData()) {
			header('HTTP/1.1 403 Forbidden');
			exit('Accès interdit');
		}


    //--------------------------------------------------------------------------------\\
	//--------------------------------------------------------------------------------\\
	// PHOTOS GALLERY MANAGER
	//--------------------------------------------------------------------------------\\
	//--------------------------------------------------------------------------------\\

	//---------------------------------------------------------  
	// INIT CLASS
	//---------------------------------------------------------

		$photo= new PhotoManager($db,[
            'table_item' => $prefixGallery.'item',
            'table_lang' => $prefixGallery.'lang',
            'table_img' => $prefixGallery.'img',
            'table_img_lang' => $prefixGallery.'img_lang'
			]
		);

		$photoUId = uniqid('photoUId_');



    //---------------------------------------------------------  
	// FOLDER IMG INIT
	//---------------------------------------------------------
	
		function createFolder($galleryPath){
			
			$new_gal=$galleryPath;
			
			$new_gallery=$galleryPath.'/gallery';
			$new_gallery_images=$new_gallery.'/images';
			$new_gallery_square=$new_gallery.'/square';
			$new_gallery_rec=$new_gallery.'/rec';

			$new_img=$galleryPath.'/img';
			$new_img_images=$new_img.'/images';
			$new_img_square=$new_img.'/square';
			$new_img_rec=$new_img.'/rec';
			
			if (is_dir($new_gal)) {
			  }else{
				if(mkdir($new_gal)){
				  if(mkdir($new_gallery)&&mkdir($new_img)){
					  if (mkdir($new_gallery_images)&&mkdir($new_gallery_square)&&mkdir($new_gallery_rec)){}else{}
					  if (mkdir($new_img_images)&&mkdir($new_img_square)&&mkdir($new_img_rec)){}else{}
				  }else{}
				}
			  }
		}

    //---------------------------------------------------------  
	// FOLDER IMG CREATION
	//---------------------------------------------------------
		
		$Rep_Photos=GALERYPHOTOPATH.$idCategory;

		createFolder($Rep_Photos);
		

    //---------------------------------------------------------  
	// GALLERY ADD
	//--------------------------------------------------------- 

		if(isset($_POST['addGalleryStart'])){
			
			$reqgalleryadd = "INSERT INTO ".$prefixGallery."item (idCategory) VALUES(:idCategory)";
			$resgalleryadd = $db->prepare($reqgalleryadd);
			$resgalleryadd->bindValue(':idCategory', $idCategory, PDO::PARAM_STR);
			$resgalleryadd->execute();
			$resgalleryadd->closeCursor();
			$resgalleryadd = NULL;

			$idGallery=$db->lastInsertId();
		
			for($i=0; $i<$nbLang;$i++){
			
				$idLang=$tabLang[$i]['id'];

				$titleAdd = 'titleAdd_' . $idLang;
				${$titleAdd} = $secure->v('string', $titleAdd, false) ?? '';

				$descriptionAdd = 'descriptionAdd_' . $idLang;
				${$descriptionAdd} = $secure->v('string', $descriptionAdd, false) ?? '';
			
			
				$reqGallerylAdd = "INSERT INTO ".$prefixGallery."lang (idGallery,idLang,title,description,idCategory) VALUES(:idGallery, :idLang, :title, :description, :idCategory)";
				$resgalleryladd = $db->prepare($reqGallerylAdd);
				$resgalleryladd->bindValue(':idGallery', $idGallery, PDO::PARAM_STR);
				$resgalleryladd->bindValue(':idLang', $idLang, PDO::PARAM_STR);
				$resgalleryladd->bindValue(':title', ${$titleAdd}, PDO::PARAM_STR);
				$resgalleryladd->bindValue(':description', ${$descriptionAdd}, PDO::PARAM_STR);
				$resgalleryladd->bindValue(':idCategory', $idCategory, PDO::PARAM_STR);
				$resgalleryladd->execute();
				$resgalleryladd->closeCursor();
				$resgalleryladd = NULL;
		

			}
		}


    //---------------------------------------------------------  
	// GALLERY MOD
	//--------------------------------------------------------- 

		if(isset($_POST['modGalleryStart'])){
			
			$modUrl="";
			$modId=$secure->v('int', 'modId', false) ?? 0;

			$reqmod = "UPDATE ".$prefixGallery."item SET url=:modUrl WHERE id=:modId";
			$resmod = $db->prepare($reqmod);
			$resmod->bindValue(':modUrl', $modUrl, PDO::PARAM_STR);
			$resmod->bindValue(':modId', $modId, PDO::PARAM_STR);
			$resmod->execute();
			$resmod->closeCursor();
			$resmod = NULL; 
			
			for($i=0; $i<$nbLang;$i++){
				
				$idLang=$tabLang[$i]['id'];
						
				$modTitle = 'modTitleGallery_' . $idLang;
				${$modTitle} = $secure->v('string', $modTitle, false) ?? '';

				$modDescription = 'modDescriptionGallery_' . $idLang;
				${$modDescription} = $secure->v('string', $modDescription, false) ?? '';

				$reqgallerymod = "UPDATE ".$prefixGallery."lang SET title=:modTitle, description=:modDescription, idCategory=:idCategory WHERE idGallery=:modId AND idLang=:idLang";
				$resgallerymod = $db->prepare($reqgallerymod);
				$resgallerymod->bindValue(':modId', $modId, PDO::PARAM_STR);
				$resgallerymod->bindValue(':idLang', $idLang, PDO::PARAM_STR);
				$resgallerymod->bindValue(':modTitle', ${$modTitle}, PDO::PARAM_STR);
				$resgallerymod->bindValue(':modDescription', ${$modDescription}, PDO::PARAM_STR);
				$resgallerymod->bindValue(':idCategory', $idCategory, PDO::PARAM_STR);
				$resgallerymod->execute();
				$resgallerymod->closeCursor();
				$resgallerymod = NULL;
			
			}

			// Msg modif
		$msgUpdate='
			<script type="text/javascript">
				showWarningMessage("builderMsgUpdate", "Modifications enregistrées", "#81B929");
			</script>
		';

		$modId=0;
			
		}

	
    //---------------------------------------------------------  
	// MOD DATA
	//--------------------------------------------------------- 

		if(isset($_GET['mod'])) {
			$reqsmod = "SELECT * FROM ".$prefixGallery."item WHERE id=:mod";
			$ressmod = $db->prepare($reqsmod);
			$ressmod->execute(array(':mod'=>$modId));
			$rsmod=$ressmod->fetch(PDO::FETCH_OBJ);
			$ressmod->closeCursor();
			$ressmod = NULL;
		}
		

    //---------------------------------------------------------  
	// GALLERY LIST
	//--------------------------------------------------------- 

		$tabGallery=$photo->listGalleryPhoto($idCategory);