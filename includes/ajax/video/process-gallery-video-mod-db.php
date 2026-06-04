<?php declare(strict_types=1);

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */


    //---------------------------------------------------------
   	// INIT NAME SPACES
   	//---------------------------------------------------------

		use Tools\SecureManager;
		use Front\StringManager;
		use Front\LangManager;
		use Tools\ImgManager;
		

   //---------------------------------------------------------
   // CONNEXIONS
   //---------------------------------------------------------

		include($_SERVER['DOCUMENT_ROOT'].'/config.php');


   //---------------------------------------------------------
   // INIT CLASS
   //---------------------------------------------------------

		$secure = new SecureManager(requirePost: true, requireCsrf: true);
		$string= new StringManager($db,['table_tags' => $prefixParam.'tags']);
		$img= new ImgManager($db);
		$lang= new LangManager($db);


    //---------------------------------------------------------  
	// VARIABLES
	//---------------------------------------------------------

		$modId = $secure->v('int', 'modId', false) ?? 0;
		$idCategory=$secure->v('int', 'idCategory', false) ?? 0;
		$idGallery=$secure->v('int', 'idGallery', false) ?? 0;
		$modVideoStart=$secure->v('int', 'modVideoStart', false) ?? false;
		$getPhoto=$secure->v('int', 'getPhoto', false) ?? 0;
		$hdStatus="";
		$video_title="";

		
    //---------------------------------------------------------  
	// LANGUAGE
	//---------------------------------------------------------

		$tabLang=$lang->activeListLang();
		$nbLang=count($tabLang);

		
    //---------------------------------------------------------
	// FUNCTION
	//---------------------------------------------------------
	
		function get_http_response_code($url) {
			$headers = get_headers($url);
			return substr($headers[0], 9, 3);
		}


    //---------------------------------------------------------
	// PROCESS
	//---------------------------------------------------------

		if(isset($modVideoStart)){
			
			$modId=$secure->v('int', 'modId', false) ?? 0;
			$modUrl=$secure->v('string', 'modUrl', false) ?? '';
			
			$modTags = $secure->v('string', 'tags', false) ?? '';
				
				if(empty($modTags)){}else{
					$tabTag=explode("," , $modTags);
					$nbTag=count($tabTag);
					if($nbTag==0){}else{
						foreach ($tabTag as $valueTag) {
							$value=trim($valueTag);
							$verifTag=$string->verifyTag($valueTag);
							if($verifTag==0){
								$reqa = "INSERT INTO ".$prefixParam."tags (tag) VALUES (:tag)";			 
								$resa = $db->prepare($reqa);
								$resa->bindValue(':tag', $valueTag, PDO::PARAM_STR);
								$resa->execute();
								$resa->closeCursor();
								$resa = NULL;
							}
						}
					}
		
				}
			
			$url_video=$secure->v('string', 'modUrl', false) ?? '';
			$url_audio=$secure->v('string', 'modUrlAudio', false) ?? '';
			
			if($getPhoto==1){
			
			if(is_numeric($url_video)){
				
			// Vimeo
			
				$hash = unserialize(file_get_contents("https://vimeo.com/api/v2/video/$url_video.php"));
				$imgWeb=$hash[0]['thumbnail_small'];
				$imgWebLarge=$hash[0]['thumbnail_large'];
				$video_title=mb_convert_encoding($hash[0]['title'], 'ISO-8859-1', 'UTF-8');	
				$video_description = mb_convert_encoding(str_replace(array("<br>", "<br/>", "<br />"), '', $hash[0]['description']), 'ISO-8859-1', 'UTF-8');
					
			}else{
				
			// Youtube
			
				$imgWeb='https://img.youtube.com/vi/'.$url_video.'/mqdefault.jpg';
				$imgWebLarge='https://img.youtube.com/vi/'.$url_video.'/maxresdefault.jpg';
					if (get_http_response_code($imgWeb) == "200") {
						$url = "https://www.googleapis.com/youtube/v3/videos?id=".$url_video.
							"&key=".GOOGLE_API_KEY.
							"&fields=items(snippet(title,description))".
							"&part=snippet";

						$videoInfoUrl = @file_get_contents($url);

						if ($videoInfoUrl === false) {
							// Log erreur API
							error_log("Erreur API YouTube pour la vidéo ".$url_video);
							$video_title = '';
							$video_description = '';
						} else {
							$jsonv = json_decode($videoInfoUrl, true);

							if (!empty($jsonv['items'][0]['snippet'])) {
								$video_title = mb_convert_encoding(
									$jsonv['items'][0]['snippet']['title'],
									'ISO-8859-1',
									'UTF-8'
								);
								$video_description = $jsonv['items'][0]['snippet']['description'];
							} else {
								$video_title = '';
								$video_description = '';
							}
						}
					}
				
			}
		
			// Upload
			
			$filNameWeb='Video-'.rand().".jpg";
			$fileNameWebUrl =  $_SERVER['DOCUMENT_ROOT'].'/img/videos/images/'.$filNameWeb;

			$baseRepIMGSquare = $_SERVER['DOCUMENT_ROOT'].'/img/videos/square/';
			$baseRepIMGReq = $_SERVER['DOCUMENT_ROOT'].'/img/videos/rec/';
			

			if(get_http_response_code($imgWeb) != "200"){
				$hdStatus=0;
			}else{
				if(file_get_contents($imgWeb)) {
					$imgContent = file_get_contents($imgWeb);
					$file = fopen($fileNameWebUrl, 'w+');
					fputs($file, $imgContent);
					fclose($file);

					$img->imgProfile($fileNameWebUrl,$baseRepIMGSquare,$filNameWeb,300, 300, 300, 300, "#151515", 100);
                	$img->imgRectangle($fileNameWebUrl,$baseRepIMGReq,$filNameWeb,1024, 576, 1024, 576, "#151515", 100);
					
					$hdStatus=0;
					
				}else {$hdStatus=0;}
			}
				
			if(get_http_response_code($imgWebLarge) != "200"){
				$hdStatus=0;
			}else{
				if(file_get_contents($imgWebLarge)) {
					$img2 = file_get_contents($imgWebLarge);
					$file2 = fopen($fileNameWebUrl, 'w+');
					fputs($file2, $img2);
					fclose($file2);
					
					$hdStatus=1;

					$img->imgProfile($fileNameWebUrl,$baseRepIMGSquare,$filNameWeb,300, 300, 300, 300, "#151515", 100);
                	$img->imgRectangle($fileNameWebUrl,$baseRepIMGReq,$filNameWeb,1024, 576, 1024, 576, "#151515", 100);
					
				} else {$hdStatus=0;}
			}
			

		
    //---------------------------------------------------------  
	// Modification video db
	//--------------------------------------------------------- 
			
		$reqmod = "UPDATE ".$prefixVideo."item SET url=:modUrl, img=:imgWeb WHERE id=:modId";
		$resmod = $db->prepare($reqmod);
		$resmod->bindValue(':modUrl', $modUrl, PDO::PARAM_STR);
		$resmod->bindValue(':imgWeb', $filNameWeb, PDO::PARAM_STR);
		$resmod->bindValue(':modId', $modId, PDO::PARAM_INT);
		$resmod->execute();
		$resmod->closeCursor();
		$resmod = NULL; 
		
		}else{
				
			$reqmod = "UPDATE ".$prefixVideo."item SET url=:modUrl WHERE id=:modId";
			$resmod = $db->prepare($reqmod);
			$resmod->bindValue(':modUrl', $modUrl, PDO::PARAM_STR);
			$resmod->bindValue(':modId', $modId, PDO::PARAM_INT);
			$resmod->execute();
			$resmod->closeCursor();
			$resmod = NULL; 
				
		}
		
		for($i=0; $i<$nbLang;$i++){
			
			$idLang=$tabLang[$i]['id'];
					
			$modTitle='modTitleVideo_'.$idLang;
			${$modTitle}=mb_convert_encoding($secure->v('string', $modTitle, false) ?? 'Sans Titre', 'UTF-8');
			if(empty(${$modTitle})){$modTitleInsert=$video_title;}else{$modTitleInsert=${$modTitle};}
			
			$modDescription='modDescriptionVideo_'.$idLang;
			${$modDescription}=mb_convert_encoding($secure->v('string', $modDescription, false) ?? '', 'UTF-8', 'ISO-8859-1');
			//if(empty(${$moddescription})){$modDescriptionInsert=$video_description;}else{$modDescriptionInsert=${$moddescription};}

			$reqvideomod = "UPDATE ".$prefixVideo."lang SET title=:modTitle, description=:modDescription, htag=:htag, idCategory=:idCategory WHERE idVideo=:modId AND idLang=:idLang";
			$resvideomod = $db->prepare($reqvideomod);
			$resvideomod->bindValue(':modId', $modId, PDO::PARAM_STR);
			$resvideomod->bindValue(':idLang', $idLang, PDO::PARAM_STR);
			$resvideomod->bindValue(':modTitle', $modTitleInsert, PDO::PARAM_STR);
			$resvideomod->bindValue(':modDescription', mb_convert_encoding(${$modDescription}, 'ISO-8859-1', 'UTF-8'), PDO::PARAM_STR);
			$resvideomod->bindValue(':htag', $modTags, PDO::PARAM_STR);
			$resvideomod->bindValue(':idCategory', $idCategory, PDO::PARAM_STR);
			$resvideomod->execute();
			$resvideomod->closeCursor();
			$resvideomod = NULL;
		
		}
		
		$modStatut=1;
		
	}
		

	//---------------------------------------------------------
  	// RESPONSE
   	//---------------------------------------------------------

		$status=($modStatut==1)? 1 : 2;

		$response = [
			'error' => false,
			'status' => $status
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;