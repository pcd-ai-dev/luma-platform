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
		use Front\LangManager;
		use Front\PhotoManager;


   //---------------------------------------------------------  
   // CONNEXIONS
   //--------------------------------------------------------- 

		include($_SERVER['DOCUMENT_ROOT'].'/config.php');
		
		
   //---------------------------------------------------------
   // INIT CLASS
   //---------------------------------------------------------

   		$secure = new SecureManager(requirePost: true, requireCsrf: true);
		$lang= new LangManager($db,['table_lang' => $prefixRoot.'lang']);
		$photo= new PhotoManager($db);
		
		
  	//---------------------------------------------------------
	// LANGUAGE
	//---------------------------------------------------------
	
      	$tabLang=$lang->activeListLang();
   
		if(isset($_SESSION['langue'])) {
			$idLang = $_SESSION['langue'];
			foreach($tabLang as $value){
				if($value['id']==$idLang){
					include $_SERVER['DOCUMENT_ROOT'].'/includes/lang/'.$value['code'].'.php';
				}else{}
			}
		} else {
			$_SESSION['langue'] = 1;
			$idLang             = 1;
			include $_SERVER['DOCUMENT_ROOT'].'/includes/lang/fr.php';
		}

   //---------------------------------------------------------  
   // VARIABLES
   //---------------------------------------------------------

		$idGallery=$secure->v('int', 'idGallery', false) ?? 0;
		$idCategory=$secure->v('int', 'idCategory', false) ?? 0;
			
		
    //---------------------------------------------------------  
    // DATA CONTENT
    //--------------------------------------------------------- 
	
		if(empty($idGallery)){ 
			$reqi = "SELECT * FROM ".$prefixGallery."img WHERE idCategory=:idCategory ORDER BY globalPosition ASC LIMIT 18 OFFSET 0";
            $resi = $db->prepare($reqi);
		}else{
			$reqi = "SELECT * FROM ".$prefixGallery."img WHERE idCategory=:idCategory AND idGallery=:idGallery ORDER BY position ASC LIMIT 18 OFFSET 0";
            $resi = $db->prepare($reqi);
			$resi->bindValue(':idGallery', $idGallery, PDO::PARAM_INT);
		}
		$resi->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
		$resi->execute();
		$nbi  = $resi->rowCount();
        $tabi = $resi->fetchAll();
        $resi->closeCursor();
        $resi = NULL;
			
	
    //---------------------------------------------------------  
	// PROCESS CONTENT
	//---------------------------------------------------------
		
		$photoContent='';
		
		if($nbi==0){
		}else{
			
			//$photoContent.= '<div class="grid-sizer"></div>';
			
			for ($j = 0; $j < $nbi; $j++) {
	
				$r = $photo->galleryImgLang($tabi[$j]['id'], $idLang);
				$caption=(isset($r->description))? $r->description : '';

				if (file_exists($_SERVER['DOCUMENT_ROOT'].'/img/gallery/'.$idCategory.'/gallery/rec/'.$tabi[$j]['img'])) {
					$imgThumb = '/img/gallery/'.$idCategory.'/gallery/rec/'.$tabi[$j]['img'];
				} else {
					$imgThumb = $imgDefaultRec;
				}
				
				$videoshow=(isset($tabi[$j]['video']))? htmlspecialchars($tabi[$j]['video'], ENT_QUOTES, 'UTF-8') : '';
				
				if ( is_numeric( $videoshow ) ) {
					$videoLinkcolorBox = "https://player.vimeo.com/video/" . $videoshow;
				} else {
					$videoLinkcolorBox = "https://www.youtube.com/embed/" . $videoshow;
				}
				
				$photoContent.= '<li>';
				$photoContent.= '<div class="photoItemList">';
				$photoContent.= '<a href="/img/gallery/'.$idCategory.'/gallery/images/'.$tabi[$j]['img'].'" data-caption="'.$caption.'" data-fancybox="gallery" class="fancybox">';
				$photoContent.= '<div class="imgBoxHome" style="background-image:url('.$imgThumb.');background-repeat:no-repeat;background-size:103%; background-position:center center;">';
				$photoContent.= '</div>';
				$photoContent.= '</div>';
				$photoContent.= '</a>';
				$photoContent.= '</li>';

			}
		

		}


   //---------------------------------------------------------  
   // GALLERY MENU
   //---------------------------------------------------------

        $req = "SELECT * FROM ".$prefixGallery."item WHERE idCategory=:idCategory AND status=1 ORDER BY position ASC";	
        $res = $db->prepare($req);
		$res->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
        $res->execute();
        $nb=$res->rowCount();
        $tab = $res->fetchAll();
        $res->closeCursor();
        $res = NULL;

		$menuContent='';
		if(empty($idGallery)){$statusGlobal='class="current"';}else{$statusGlobal='';}
		$menuContent.='<a href="" id="0" '.$statusGlobal.' >'.$translations['ALLRESULTTXT'].'</a>';
		
		for($i=0;$i<$nb;$i++){
			$gLang = $photo->galleryLang($tab[$i]['id'], $idLang);
			if($tab[$i]['id']==$idGallery){$statusGallery='class="current"';}else{$statusGallery='';}
			$menuContent.='<a href="" id="'.$tab[$i]['id'].'" '.$statusGallery.' >'.$gLang->title.'</a>';
		}


	//---------------------------------------------------------
  	// RESPONSE
   	//---------------------------------------------------------

		$photoContent=mb_convert_encoding((isset($photoContent))? $photoContent : '', 'UTF-8');
		$menuContent=mb_convert_encoding((isset($menuContent))? $menuContent : '', 'UTF-8');
 
		$response = [
			'error' => false,
			'photoContent' => $photoContent,
			'menuContent' => $menuContent,
			'idGallery' => $idGallery,
			'idCategory' => $idCategory
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;