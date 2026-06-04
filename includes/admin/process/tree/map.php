<?php

/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
*  @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*  @version  Release: 3
*/

    //--------------------------------------------------------------------------------\\
	//--------------------------------------------------------------------------------\\
	// Gestion Localisation
	//--------------------------------------------------------------------------------\\
	//--------------------------------------------------------------------------------\\

    //---------------------------------------------------------
	// INIT NAMES SPACE
	//---------------------------------------------------------

		use Front\LocationManager;


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

		$location= new LocationManager($db);

	//---------------------------------------------------------  
	// FOLDER IMG INIT
	//---------------------------------------------------------
	
		function createFolder($idCategory){

			$locationPath=$_SERVER['DOCUMENT_ROOT'].'/img/location/'.$idCategory;
			
			$new_location=$locationPath.'/gallery';
			$new_location_images=$new_location.'/images';
			$new_location_square=$new_location.'/square';
			$new_location_rec=$new_location.'/rec';

			$new_img=$locationPath.'/img';
			$new_img_images=$new_img.'/images';
			$new_img_square=$new_img.'/square';
			$new_img_rec=$new_img.'/rec';
			
			if (is_dir($locationPath)) {
			  }else{
				if(mkdir($locationPath)){
				  if(mkdir($new_location)&&mkdir($new_img)){
					  if (mkdir($new_location_images)&&mkdir($new_location_square)&&mkdir($new_location_rec)){}else{}
					  if (mkdir($new_img_images)&&mkdir($new_img_square)&&mkdir($new_img_rec)){}else{}
				  }else{}
				}
			  }
		}

    //---------------------------------------------------------  
	// FOLDER IMG CREATION
	//---------------------------------------------------------

		createFolder($idCategory);


    //---------------------------------------------------------  
	// MAP ADD
	//--------------------------------------------------------- 

		if(isset($_POST['addMapStart'])){
			
			
	   //---------------------------------------------------------  
	   // Géolocalisation adresse précise
	   //--------------------------------------------------------- 

			$address = $secure->v('string', 'address', false) ?? '';
			$lattitude = $secure->v('string', 'lattitude', false) ?? '';
			$longitude = $secure->v('string', 'longitude', false) ?? '';
			$idSiteAdd  = $_SESSION['siteData']['id'] ?? 0;
			   
			$reqLocalisationAdd="INSERT INTO ".$prefixLoc."item (idSite,idCategory,address,lat,lng) VALUES(:idSite,:idCategory,:address,:lat,:lng)";
			$resLocalisationAdd=$db->prepare($reqLocalisationAdd);
			$resLocalisationAdd->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
			$resLocalisationAdd->bindValue(':address', $address, PDO::PARAM_STR);
			$resLocalisationAdd->bindValue(':lat', $lattitude, PDO::PARAM_STR);
			$resLocalisationAdd->bindValue(':lng', $longitude, PDO::PARAM_STR);
			$resLocalisationAdd->bindValue(':idSite', $idSiteAdd, PDO::PARAM_STR);
			$resLocalisationAdd->execute();
			$resLocalisationAdd->closeCursor();
			$resLocalisationAdd=NULL;
			
			$idLocation=$db->lastInsertId();

			for($i=0; $i<$nbLang;$i++){

				$idLang=$tabLang[$i]['id'];			

				$reqLocalisationLAdd="INSERT INTO ".$prefixLoc."lang (idSite,idLocation,idLang,title,idCategory) VALUES(:idSite, :idLocation, :idLang, :title, :idCategory)";
				$resLocalisationLAdd=$db->prepare($reqLocalisationLAdd);
				$resLocalisationLAdd->bindValue(':idLocation', $idLocation, PDO::PARAM_STR);
				$resLocalisationLAdd->bindValue(':idLang', $idLang, PDO::PARAM_STR);
				$resLocalisationLAdd->bindValue(':title', $siteName, PDO::PARAM_STR);
				$resLocalisationLAdd->bindValue(':idCategory', $idCategory, PDO::PARAM_STR);
				$resLocalisationLAdd->bindValue(':idSite', $idSiteAdd, PDO::PARAM_STR);
				$resLocalisationLAdd->execute();
				$resLocalisationLAdd->closeCursor();
				$resLocalisationLAdd=NULL;

				
			}
			
		}
		
		
    //---------------------------------------------------------  
	// Modification map
	//--------------------------------------------------------- 
	

		if(isset($_POST['modMapStart'])){

			$idCategory = $secure->v('int', 'idCategory', false) ?? 0;
			$modAddress = $secure->v('string', 'modAddress', false) ?? '';
			$lattitude = $secure->v('string', 'modLat', false) ?? '';
			$longitude = $secure->v('string', 'modLng', false) ?? '';
			
			$reqMod="UPDATE ".$prefixLoc."item SET address=:modAddress, lat=:modLat, lng=:modLng WHERE idCategory=:idCategory";
			$resMod=$db->prepare($reqMod);
			$resMod->bindValue(':modAddress', $modAddress, PDO::PARAM_STR);
			$resMod->bindValue(':modLat', $lattitude, PDO::PARAM_STR);
			$resMod->bindValue(':modLng', $longitude, PDO::PARAM_STR);
			$resMod->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
			$resMod->execute();
			$resMod->closeCursor();
			$resMod=NULL; 
		
		
			for($i=0; $i<$nbLang;$i++){

				$idLang=$tabLang[$i]['id'];

				$checkLocaltionLang=$location->localisationLang($modId, $idLang);

				if(isset($checkLocaltionLang)){

					$reqLocalisationMod="UPDATE ".$prefixLoc."lang SET title=:modTitle, idCategory=:idCategory WHERE idLocation=:modId and idLang=:idLang";
					$resLocalisationMod=$db->prepare($reqLocalisationMod);
					$resLocalisationMod->bindValue(':modId', $modId, PDO::PARAM_STR);
					$resLocalisationMod->bindValue(':idLang', $idLang, PDO::PARAM_STR);
					$resLocalisationMod->bindValue(':modTitle', $siteName, PDO::PARAM_STR);
					$resLocalisationMod->bindValue(':idCategory', $idCategory, PDO::PARAM_STR);
					$resLocalisationMod->execute();
					$resLocalisationMod->closeCursor();
					$resLocalisationMod=NULL;

				}else{

					$reqLocalisationAdd=$reqProdLAdd = "INSERT INTO ".$prefixLoc."lang (idLocation,idLang,title,idCategory)
														VALUES(:modId,:idLang,:modTitle,:idCategory)";
					$resLocalisationAdd=$db->prepare($reqLocalisationAdd);
					$resLocalisationAdd->bindValue(':modId', $modId, PDO::PARAM_STR);
					$resLocalisationAdd->bindValue(':idLang', $idLang, PDO::PARAM_STR);
					$resLocalisationAdd->bindValue(':modTitle', $siteName, PDO::PARAM_STR);
					$resLocalisationAdd->bindValue(':idCategory', $idCategory, PDO::PARAM_STR);
					$resLocalisationAdd->execute();
					$resLocalisationAdd->closeCursor();
					$resLocalisationAdd=NULL;

				}

			}
		
		}
	

		
    //---------------------------------------------------------  
	// DATA DISPLAY
	//--------------------------------------------------------- 
	
		if((($r?->type==1)&&($r?->switch==12))){
			
			$req = "SELECT * FROM ".$prefixLoc."item WHERE idCategory=:idCategory LIMIT 1";				
			$res = $db->prepare($req);
			$res->bindValue(':idCategory', $idCategory, PDO::PARAM_STR);
			$res->execute();
			$countMap=$res->rowCount();
			$rMap = $res->fetch(PDO::FETCH_OBJ);
			$res->closeCursor();
			$res = NULL;
			
		}