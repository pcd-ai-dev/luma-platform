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
  		use Front\VideoManager;

   //---------------------------------------------------------
   // CONNEXIONS
   //---------------------------------------------------------

		include($_SERVER['DOCUMENT_ROOT'].'/config.php');


   //--------------------------------------------------------- 
   // INIT CLASS
   //---------------------------------------------------------

   		$secure = new SecureManager(requirePost: true, requireCsrf: true);

		$video= new VideoManager($db,[
            'table_item' => $prefixVideo.'item',
            'table_lang' => $prefixVideo.'lang'
			]
		);
		
		$lang= new LangManager($db);

		
  	//---------------------------------------------------------  
	// LANGUAGE
	//---------------------------------------------------------
	
      	$tabLang=$lang->listLang();
   
		if(isset($_SESSION['langue'])) {
			$idLang = intval($_SESSION['langue']);
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
   // VARIABLE
   //---------------------------------------------------------

		$idGallery=$secure->v('int', 'idGallery', false) ?? 0;
		$offset=$secure->v('int', 'offset', false) ?? 0;
		$idCategory=$secure->v('int', 'idCategory', false) ?? 0;
		$menuContent="";
			
		
    //---------------------------------------------------------  
	// PROCESS
	//---------------------------------------------------------
	
			if(empty($idGallery)){
	
				$reqv = "SELECT v.*, l.*
					   FROM ".$prefixVideo."item v
					   INNER JOIN ".$prefixVideo."lang l
					   ON v.id=l.idVideo
					   AND l.idLang=:idLang
					   WHERE v.status=1
					   AND url!='0'
					   AND v.idCategory=:idCategory
					   ORDER BY v.globalPosition ASC LIMIT 18 OFFSET $offset";
				$resv = $db->prepare($reqv);
				
			}else{
				$reqv = "SELECT v.*, l.*
					   FROM ".$prefixVideo."item v
					   INNER JOIN ".$prefixVideo."lang l
					   ON v.id=l.idVideo
					   AND l.idLang=:idLang
					   WHERE v.status=1
					   AND v.idGallery=:idGallery
					   AND v.idCategory=:idCategory
					   ORDER BY v.position ASC LIMIT 18 OFFSET $offset";
				$resv = $db->prepare($reqv);
				$resv->bindValue(':idGallery', $idGallery, PDO::PARAM_INT);
			}
			$resv->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
			$resv->bindValue(':idLang', $idLang, PDO::PARAM_INT);
			$resv->execute();
			$nbv=$resv->rowCount();
			$tabv = $resv->fetchAll();
			$resv->closeCursor();
			$resv = NULL;
			
			
			$videoContent="";
		
			for($i=0;$i<$nbv;$i++) {
				$vlang=$video->videoLang($tabv[$i]['idVideo'],$idLang);
				$videoshow=htmlspecialchars($tabv[$i]['url']);
				
				if (is_numeric($videoshow)) {
					$videoClass="vimeo";
					$videoLinkcolorBox="https://player.vimeo.com/video/".$videoshow; 
				}else{
					$videoClass="youtube";
					$videoLinkcolorBox="https://www.youtube.com/embed/".$videoshow; 
				}
				
				$videoThumbCheck=$_SERVER['DOCUMENT_ROOT'].'/img/videos/rec/'.$tabv[$i]['img'];
				if((file_exists($videoThumbCheck))&&(!empty($tabv[$i]['img']))){
					$videoThumb= '/img/videos/rec/'.$tabv[$i]['img'];
					$status=true;
				}else{
					$videoThumb=$imgDefaultRec;
					$status=true;
				}
				
				if($status==false){}else{
					
					if(!empty($tabv[$i]['idAudio'])){

						if (strpos($tabv[$i]['idAudio'], 'soundcloud') > 0) {
							$videoContent.= '<div style="width:100%;">';
							$videoContent.= '
							<div><iframe frameborder="no" height="160" scrolling="no" src="https://w.soundcloud.com/player/?url='.$tabv[$i]['idAudio'].'" width="100%"allow="encrypted-media" ></iframe></div>

							<div style="height:20px;">&nbsp;</div>
							';
							$videoContent.= '</div>';
						}else{
							$videoContent.= '<div style="width:100%;">';
							$videoContent.= '
							<div><iframe frameborder="no" height="160" scrolling="no" src="'.$tabv[$i]['idAudio'].'" width="100%" height="232" framestyle="border:0px;" allowtransparency="true" allow="encrypted-media"></iframe></div>

							<div style="height:20px;">&nbsp;</div>
							';
							$videoContent.= '</div>';
						}
						

					}else{
						
						$videoContent.= '<li>';
						$videoContent.= '<div class="videoItemList">';
						
						$videoContent.= '<div class="imgBoxHome lazy" style="background-image:url('.$videoThumb.');background-repeat:no-repeat;background-size:103%; background-position:center center;">';

						$videoContent.= '<div class="caption">';
						if (!empty($vlang->title)) {
							$videoContent.= '<div class="title" ><h3>'.$vlang->title.'</h3></div>';
							$videoContent.= '<div class="description">';
							$videoContent.= (isset($vlang->description))? nl2br($vlang->description) : '';
							$videoContent.= '</div>';
							$videoContent.= '<div>&nbsp;</div>';
						}
						$videoContent.= '<a href="'.$videoLinkcolorBox.'" data-fancybox data-width="80%" data-height="80%">';
						$videoContent.= '<div class="SeeTxt">'.$translations['SEEVIDEOTXT'].'</div>';
						$videoContent.= '</a>';
						$videoContent.= '</div>';

						$videoContent.= '</div>'; 
						
						$videoContent.= '</div>';
						$videoContent.= '</li>';

					}
					
				}
			}
			

	//---------------------------------------------------------
  	// RESPONSE
   	//---------------------------------------------------------

		$videoContent=mb_convert_encoding((isset($videoContent))? $videoContent : '', 'UTF-8');
		$menuContent=mb_convert_encoding((isset($menuContent))? $menuContent : '', 'UTF-8');

		$response = [
			'videoContent' => $videoContent,
			'menuContent' => $menuContent,
			'offsetvideolist' => $offset,
			'idGallery' => $idGallery,
			'idCategory' => $idCategory
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;