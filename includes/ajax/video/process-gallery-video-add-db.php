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
		use Tools\ImgManager;
		use Front\LangManager;
		use Front\StringManager;
		

   //---------------------------------------------------------
   // CONNEXIONS
   //---------------------------------------------------------

		include($_SERVER['DOCUMENT_ROOT'].'/config.php');


   //---------------------------------------------------------
   // INIT CLASS
   //---------------------------------------------------------

		$secure = new SecureManager(requirePost: true, requireCsrf: true);
    	$img= new ImgManager($db);
		$string= new StringManager($db);
		$lang= new LangManager($db);
		

    //---------------------------------------------------------  
	// VARIABLES
	//---------------------------------------------------------

		$idCategory=$secure->v('int', 'idCategory', false) ?? 0;
		$idGallery=$secure->v('int', 'idGallery', false) ?? 0;
		$addVideoStart=$secure->v('int', 'addVideoStart', false) ?? false;

		
    //---------------------------------------------------------  
	// LANGUAGE
	//---------------------------------------------------------

		$tabLang=$lang->activeListLang();
		$nbLang=count($tabLang);
		
		
    //---------------------------------------------------------  
	// PROCESS
	//--------------------------------------------------------- `
	
		function get_http_response_code($url) {
			$headers = get_headers($url);
			return substr($headers[0], 9, 3);
		}


		if(isset($addVideoStart)){
	
	//---------------------------------------------------------  
	// TAG PROCESS
	//--------------------------------------------------------- 
	
		$tags = $secure->v('string', 'tags', false) ?? '';
			
			if(empty($tags)){}else{
			
				$tabTag=explode("," , $tags);
				$nbTag=count($tabTag);
				if($nbTag==0){}else{
					foreach ($tabTag as $valueTag) {
						$value=trim($valueTag);
						$veriftag=$string->verifyTag($valueTag);
						if($veriftag==0){
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
			
			$tags=$tags;
		
			// Video
			$url_video=$secure->v('string', 'url_video', false) ?? '';
			$url_audio=$secure->v('string', 'url_audio', false) ?? '';

			if(empty($url_video)){}else{
		
				if(is_numeric($url_video)){

				// Vimeo
					$hash = unserialize(file_get_contents("https://vimeo.com/api/v2/video/$url_video.php"));
					$imgweb=$hash[0]['thumbnail_small'];
					$imgWebLarge=$hash[0]['thumbnail_large'];
					$video_title=mb_convert_encoding($hash[0]['title'], 'ISO-8859-1', 'UTF-8');	
					$video_description = mb_convert_encoding(
						strip_tags($hash[0]['description']),
						'ISO-8859-1',
						'UTF-8'
					);	
				}else{

				// Youtube
					$imgweb='https://img.youtube.com/vi/'.$url_video.'/mqdefault.jpg';
					$imgWebLarge='https://img.youtube.com/vi/'.$url_video.'/maxresdefault.jpg';
					if (get_http_response_code($imgweb) == "200") {
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


				if(get_http_response_code($imgweb) != "200"){
					$hdStatus=0;
				}else{
					if(file_get_contents($imgweb)) {
						$imgContent = file_get_contents($imgweb);
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
				
			}

			$reqvideoadd = "INSERT INTO ".$prefixVideo."item (idGallery,url,idAudio,idCategory,img,hd) VALUES(:idGallery, :url, :idAudio, :idCategory, :imgWeb, :hd)";
			$resvideoadd = $db->prepare($reqvideoadd);
			$resvideoadd->bindValue(':idGallery', $idGallery, PDO::PARAM_STR);
			$resvideoadd->bindValue(':url', $url_video, PDO::PARAM_STR);
			$resvideoadd->bindValue(':idAudio', $url_audio, PDO::PARAM_STR);
			$resvideoadd->bindValue(':imgWeb', $filNameWeb, PDO::PARAM_STR);
			$resvideoadd->bindValue(':idCategory', $idCategory, PDO::PARAM_STR);
			$resvideoadd->bindValue(':hd', $hdStatus, PDO::PARAM_STR);
			$resvideoadd->execute();
			$resvideoadd->closeCursor();
			$resvideoadd = NULL;
			
			
			$idVideo=$db->lastInsertId();
			

			for($i=0; $i<$nbLang;$i++){
							
				$idLang=$tabLang[$i]['id'];

				$titleAdd='titleAdd_'.$idLang;
				${$titleAdd}=mb_convert_encoding($secure->v('string', $titleAdd, false) ?? '', 'UTF-8');
				if(empty(${$titleAdd})){$addTitleInsert=(isset($video_title))? $video_title : '';}else{$addTitleInsert=${$titleAdd};}

				$descriptionAdd='descriptionAdd_'.$idLang;
				${$descriptionAdd}=stripslashes($secure->v('string', $descriptionAdd, false) ?? '');
				if(empty(${$descriptionAdd})){$addDescriptionInsert=(isset($video_description))? $video_description : '';}else{$addDescriptionInsert=${$descriptionAdd};}

				$reqvideoladd = "INSERT INTO ".$prefixVideo."lang (idGallery,idVideo,idLang,title,description,htag,idCategory) VALUES(:idGallery, :idVideo, :idLang, :title, :description, :htag , :idCategory)";
				$resvideoladd = $db->prepare($reqvideoladd);
				$resvideoladd->bindValue(':idGallery', $idGallery, PDO::PARAM_STR);
				$resvideoladd->bindValue(':idVideo', $idVideo, PDO::PARAM_STR);
				$resvideoladd->bindValue(':idLang', $idLang, PDO::PARAM_STR);
				$resvideoladd->bindValue(':title', $addTitleInsert, PDO::PARAM_STR);
				$resvideoladd->bindValue(':description', $addDescriptionInsert, PDO::PARAM_STR);
				$resvideoladd->bindValue(':htag', $tags, PDO::PARAM_STR);
				$resvideoladd->bindValue(':idCategory', $idCategory, PDO::PARAM_STR);
				$resvideoladd->execute();
				$resvideoladd->closeCursor();
				$resvideoladd = NULL;
		
			}
		
		}	
		

	//---------------------------------------------------------
  	// RESPONSE
   	//---------------------------------------------------------

		$status = (!empty($idVideo)) ? 1 : 0;

		$response = [
			'error' => false,
			'status' => $status
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;