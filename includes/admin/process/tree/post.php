<?php

/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
*  @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*  @version  Release: 3
*/


    //---------------------------------------------------------  
   	// INIT NAME SPACES
   	//---------------------------------------------------------

		use Front\PostManager;
		use Xml\XmlManager;
		use Front\LangManager;


	//---------------------------------------------------------  
	// FILE SECURE
	//---------------------------------------------------------  

		if (!$session->getAdminData()) {
			header('HTTP/1.1 403 Forbidden');
			exit('Accès interdit');
		}

    //----------------------------------------------------------------------\\
    //----------------------------------------------------------------------\\ 
	// ADMIN POST
    //----------------------------------------------------------------------\\
    //----------------------------------------------------------------------\\

    //---------------------------------------------------------
	// INIT CLASS
	//---------------------------------------------------------

		$post= new PostManager($db, $siteName, $siteHost, $imgDefaultSquare);
		$xml= new XmlManager($db,$idSite,$siteName,$siteHost, $imgDefaultSquare);
		$lang= new LangManager($db);

		$postUId = uniqid('postUId_');


  	//---------------------------------------------------------
	// LANGUAGE
	//---------------------------------------------------------
	
      	$tabLang=$lang->activeListLang();
		$nbLang=count($tabLang);


    //---------------------------------------------------------
	// FOLDER IMG INIT
	//---------------------------------------------------------
	
		function createImgFolder($postImgPath){
			
			$new_gallery=$postImgPath.'gallery';
			$new_gallery_images=$new_gallery.'/images';
			$new_gallery_square=$new_gallery.'/square';
			$new_gallery_rec=$new_gallery.'/rec';

			$new_img=$postImgPath.'img';
			$new_img_images=$new_img.'/images';
			$new_img_square=$new_img.'/square';
			$new_img_rec=$new_img.'/rec';

			if (is_dir($postImgPath)) {
			  }else{
				  if(mkdir($postImgPath)&&mkdir($new_gallery)&&mkdir($new_img)){
					  if (mkdir($new_gallery_images)&&mkdir($new_gallery_square)&&mkdir($new_gallery_rec)){}else{}
					  if (mkdir($new_img_images)&&mkdir($new_img_square)&&mkdir($new_img_rec)){}else{}
				  }else{}
			  }
		}


    //---------------------------------------------------------  
	// FOLDER IMG CREATION
	//---------------------------------------------------------
		
		$postImgPath=$_SERVER['DOCUMENT_ROOT'].'/img/post/'.$idCategory.'/';
		  
		if (is_dir($postImgPath)){
		}else{
			createImgFolder($postImgPath);
		}

    //---------------------------------------------------------  
	// FOLDER File INIT
	//---------------------------------------------------------
	
		function createFileFolder($postFilePath){
			
			$new_files=$postFilePath.'files';

			if (is_dir($new_files)) {
			  }else{
				  if(mkdir($postFilePath)){
					  if (mkdir($new_files)){}else{}
				  }else{}
			  }
		}


    //---------------------------------------------------------  
	// FOLDER FILE CREATION
	//---------------------------------------------------------
		
		$postFilePath=$_SERVER['DOCUMENT_ROOT'].'/tmp/post/'.$idCategory.'/';
		  
		if (is_dir($postFilePath)){
		}else{
			createFileFolder($postFilePath);
		}


    //---------------------------------------------------------  
	// POST ADD PROCESS
	//--------------------------------------------------------- 

		if(isset($_POST['addPostStart'])){

			$datePost=$secure->v('string', 'datePost', false) ?? '';

			$reqPostAdd = "INSERT INTO ".$prefixPost."item (date,idSite,idCategory) VALUES(:datePost,:idSite, :idCategory)";
			$resPostAdd = $db->prepare($reqPostAdd);
			$resPostAdd->bindValue(':idSite', $_SESSION['siteData']['id'], PDO::PARAM_INT);
			$resPostAdd->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
			$resPostAdd->bindValue(':datePost', $datePost, PDO::PARAM_STR);
			$resPostAdd->execute();
			$resPostAdd->closeCursor();
			$resPostAdd = NULL;

			$idPost=$db->lastInsertId();

			for($i=0; $i<$nbLang;$i++){
			
				$idLang=$tabLang[$i]['id'];

				$titleAdd='titleAdd_'.$idLang;
				${$titleAdd}=$secure->v('string', $titleAdd, false) ?? '';

				$textAdd='textAdd_'.$idLang;
				${$textAdd}=$secure->v('string', $textAdd, false) ?? '';

				$reqpostladd = "INSERT INTO ".$prefixPost."lang (idPost,idLang,title,text,idCategory) VALUES(:idPost, :idLang, :title, :text, :idCategory)";
				$respostladd = $db->prepare($reqpostladd);
				$respostladd->bindValue(':idPost', $idPost, PDO::PARAM_INT);
				$respostladd->bindValue(':idLang', $idLang, PDO::PARAM_INT);
				$respostladd->bindValue(':title', ${$titleAdd}, PDO::PARAM_STR);
				$respostladd->bindValue(':text', ${$textAdd}, PDO::PARAM_STR);
				$respostladd->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
				$respostladd->execute();
				$respostladd->closeCursor();
				$respostladd = NULL;
			}
			
			for($i=0; $i<$nbLang;$i++){
				$idLang=$tabLang[$i]['id'];
				$xml->updateSiteMapNews();
				$xml->updateRssNews($idLang);
			}

		}

    //---------------------------------------------------------  
	// MOD POST PROCESS
	//--------------------------------------------------------- 
	

		if(isset($_POST['modPostStart'])){

			$modId          = $secure->v('string', 'modId', false) ?? '';
			
			$modUrl         = $secure->v('string', 'modTarget', false) ?? '';
			$datePost       = $secure->v('string', 'modDate', false) ?? '';
			$modTypePost    = $secure->v('string', 'modSource', false) ?? '';
			$positionIMG    = $secure->v('string', 'positionIMG', false) ?? '';
			$TxtColor       = $secure->v('string', 'txtColor', false) ?? '';
			$BGColor        = $secure->v('string', 'bgColor', false) ?? '';
			$modVideo       = $secure->v('string', 'modVideo', false) ?? '';

			$modEventStatus = $secure->v('int', 'modEventStatus', false) ?? 0;
			$modStartDate   = $secure->v('string', 'modStartDate', false) ?? date('Y-m-d H:i:s');
			$modEndDate     = $secure->v('string', 'modEndDate', false) ?? date('Y-m-d H:i:s');

			$modPlace       = $secure->v('string', 'modPlace', false) ?? '';
			$modAddress     = $secure->v('string', 'modAddress', false) ?? '';
			$modCp          = $secure->v('string', 'modCp', false) ?? '';
			$modCity        = $secure->v('string', 'modCity', false) ?? '';

			$modLattitude   = $secure->v('string', 'modLattitude', false) ?? '';
			$modLongitude   = $secure->v('string', 'modLongitude', false) ?? '';
			$modPhone       = $secure->v('string', 'modPhone', false) ?? '';
			$modUrl         = $secure->v('string', 'modUrl', false) ?? '';
			$modLink        = $secure->v('string', 'modLink', false) ?? '';
			$modPrice       = $secure->v('string', 'modprice', false) ?? '';


	//---------------------------------------------------------  
	// TAG PROCESS
	//--------------------------------------------------------- 
	
		$tags = $secure->v('string', 'tags', false) ?? '';
						
		if(empty($tags)){}else{

			$tabTag=explode("," , $tags);
			$nbTag=count($tabtag);
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

		$tags=$tags;
				
    //---------------------------------------------------------  
	// MOD POST DB
	//--------------------------------------------------------- 

		$reqMod = "UPDATE ".$prefixPost."item SET date=:datePost, dateModified=now(), video=:modVideo, startDate=:startDate, endDate=:endDate, place=:modPlace, address=:modAddress, cp=:modCp, city=:modCity, lat=:modLattitude, lng=:modLongitude, phone=:modPhone, url=:modUrl, link=:modLink, price=:modPrice WHERE id=:modId";
		$resMod = $db->prepare($reqMod);
		$resMod->bindValue(':datePost', $datePost, PDO::PARAM_STR);
		$resMod->bindValue(':modVideo', $modVideo, PDO::PARAM_STR);
		$resMod->bindValue(':startDate', $modStartDate, PDO::PARAM_STR);
		$resMod->bindValue(':endDate', $modEndDate, PDO::PARAM_STR);
		$resMod->bindValue(':modPlace', $modPlace, PDO::PARAM_STR);
		$resMod->bindValue(':modAddress', $modAddress, PDO::PARAM_STR);
		$resMod->bindValue(':modCp', $modCp, PDO::PARAM_STR);
		$resMod->bindValue(':modCity', $modCity, PDO::PARAM_STR);
		$resMod->bindValue(':modLattitude', $modLattitude , PDO::PARAM_STR);
		$resMod->bindValue(':modLongitude', $modLongitude, PDO::PARAM_STR);
		$resMod->bindValue(':modPhone', $modPhone, PDO::PARAM_STR);
		$resMod->bindValue(':modUrl', $modUrl, PDO::PARAM_STR);
		$resMod->bindValue(':modLink', $modLink, PDO::PARAM_STR);
		$resMod->bindValue(':modPrice', $modPrice, PDO::PARAM_STR);
		$resMod->bindValue(':modId', $modId, PDO::PARAM_INT);
		$resMod->execute();
		$resMod->closeCursor();
		$resMod = NULL; 
		
		for($i=0; $i<$nbLang;$i++){
			
			$idLang=$tabLang[$i]['id'];
					
			$modTitle='modTitlePost_'.$idLang;
			${$modTitle}=$secure->v('string', $modTitle, false) ?? '';
			
			$modText='modTextPost_'.$idLang;
			${$modText}=$secure->v('string', $modText, false) ?? '';

			$modShortText='modShortText_'.$idLang;
			${$modShortText}=$secure->v('string', $modShortText, false) ?? '';
			
			$reqPostMod = "UPDATE ".$prefixPost."lang SET title=:modTitle,  shortText=:modShortText,  text=:modText, htag=:htag, idCategory=:idCategory WHERE idPost=:modId AND idLang=:idLang";
			$resPostMod = $db->prepare($reqPostMod);
			$resPostMod->bindValue(':modId', $modId, PDO::PARAM_STR);
			$resPostMod->bindValue(':idLang', $idLang, PDO::PARAM_STR);
			$resPostMod->bindValue(':modTitle', ${$modTitle}, PDO::PARAM_STR);
			$resPostMod->bindValue(':modText', ${$modText}, PDO::PARAM_STR);
			$resPostMod->bindValue(':modShortText', ${$modShortText}, PDO::PARAM_STR);
			$resPostMod->bindValue(':htag', $tags, PDO::PARAM_STR);
			$resPostMod->bindValue(':idCategory', $idCategory, PDO::PARAM_STR);
			$resPostMod->execute();
			$resPostMod->closeCursor();
			$resPostMod = NULL;

			if(isset($modEventStatus)){

				if ((empty($modStartDate))||(empty($modEndDate))||(empty($modPlace))){
					
					$icsFile = $_SERVER['DOCUMENT_ROOT'].'/ics/Event_'.$idLang.'_'.$modId.'.ics';
					
					if (file_exists($icsFile)){
						unlink($icsFile);
					}
					
				}else{

					$dateStart = new DateTimeImmutable($modStartDate);
					$dateEnd = new DateTimeImmutable($modEndDate);

					$icsFile = $_SERVER['DOCUMENT_ROOT'].'/ics/Event_'.$idLang.'_'.$modId.'.ics';
					
					$linkPost=$siteHost.'/news-'.$modId.'-'.$string->cleanUrl(${$modTitle}).'.html';

					//Evenèment au format ICS
					$ics = "BEGIN:VCALENDAR\n";
					$ics .= "VERSION:2.0\n";
					$ics .= "PRODID:-//hacksw/handcal//NONSGML v1.0//EN\n";
					$ics .= "BEGIN:VEVENT\n";
					$ics .= "X-WR-TIMEZONE:Europe/Paris\n";
					$ics .= "ORGANIZER:mailto:".$mailBase."\n";
					$ics .= "DTSTART:".$dateStart->format("Ymd")."T".$dateStart->format("His")."\n";
					$ics .= "DTEND:".$dateEnd->format("Ymd")."T".$dateEnd->format("His")."\n";
					$ics .= "SUMMARY:".${$modTitle}."\n";
					$ics .= "LOCATION:".mb_convert_encoding($modPlace, "UTF-8", "ISO-8859-1")." ".mb_convert_encoding($modAddress." ".$modCp." ".$modCity, "UTF-8", "ISO-8859-1")."\n";
					$ics .= "GEO:".$modLattitude.";".$modLongitude."\n";
					$ics .= "URL:".$linkPost."\n";
					$ics .= "END:VEVENT\n";
					$ics .= "END:VCALENDAR\n";

					//Création du fichier
					$f = fopen($icsFile, 'w+');
					fputs($f, $ics);
				}

			}
		
		}
		
		$modStatus=0;
		$modId=0;
		
		for($i=0; $i<$nbLang;$i++){
			$idLang=$tabLang[$i]['id'];
			$xml->updateSiteMapNews();
			$xml->updateRssNews($idLang);
		}

        // Msg modif
        $msgUpdate='
            <script type="text/javascript">
                showWarningMessage("builderMsgUpdate", "Modifications enregistrées", "#81B929");
            </script>
        ';
		
	}
	


    //---------------------------------------------------------  
	// DATA MOD POST
	//---------------------------------------------------------

		if($modId!=0) {

			$rsmod=$post->infoPost($modId);
			$modIdPost = (isset($rsmod->id))? (int)$rsmod->id : 0;
			$modUrlpostFR = (isset($rsmod->urlFR))? $rsmod->urlFR : '';
            $modUrlpostEN = (isset($rsmod->urlEN))? $rsmod->urlEN : '';
			$modProjectPost = (isset($rsmod->urlProject))?$rsmod->urlProject : '';
			$modAnimation = (isset($rsmod->animation))? $rsmod->animation : '';
			$modVideo = (isset($rsmod->video))? $rsmod->video : '';
			$modSVG = (isset($rsmod->svg))? $rsmod->svg : '';
			$modSource = (isset($rsmod->type))? $rsmod->type : '';
			$positionIMG = (isset($rsmod->positionIMG))? $rsmod->positionIMG : '';
			$modTxtColor = (isset($rsmod->color))? $rsmod->color : '';
			$modBGColor = (isset($rsmod->bgColor))?$rsmod->bgColor : '';

			$modEventStatus = (isset($rsmod->eventStatus))? (int)$rsmod->eventStatus : 0;
			$modStartDate = (isset($rsmod->startDate))? $rsmod->startDate : '';
			$modEndDate = (isset($rsmod->endDate))? $rsmod->endDate : '';
			$modPlace = (isset($rsmod->place))? $rsmod->place : '';
			$modAddress = (isset($rsmod->address))? $rsmod->address : '';
			$modCp = (isset($rsmod->cp))? $rsmod->cp : '';
			$modCity = (isset($rsmod->city))? $rsmod->city : '';
			$modLattitude = (isset($rsmod->lat))? $rsmod->lat : '';
			$modLongitude = (isset($rsmod->lng))? $rsmod->lng : '';
			$modPhone = (isset($rsmod->bgColor))? $rsmod->bgColor : '';
			$modUrl = (isset($rsmod->url))? $rsmod->url : '';
			$modLink = (isset($rsmod->link))? $rsmod->link : '';
			$modPrice = (isset($rsmod->price))? $rsmod->price : '';
			
			$statusForm=0;	


		}