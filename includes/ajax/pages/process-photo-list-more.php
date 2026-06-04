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
		use Front\PageManager;
 
		
   //---------------------------------------------------------  
   // CONNEXIONS
   //---------------------------------------------------------

		include($_SERVER['DOCUMENT_ROOT'].'/config.php');


   //---------------------------------------------------------  
   // INIT CLASS
   //---------------------------------------------------------

		$secure = new SecureManager(requirePost: true, requireCsrf: true);
		$lang= new LangManager($db);
		$page= new PageManager($db);
		
		
  	//---------------------------------------------------------  
	// LANGUAGE
	//---------------------------------------------------------
	
      	$tabLang=$lang->listLang();
   
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

		$offset = $secure->session('offsetWall', 'int', false) ?? 0;
		$idCategory = $secure->v('int', 'idCategory', false) ?? 0;


    //---------------------------------------------------------  
    // DATA
    //--------------------------------------------------------- 
	
		$reqi = "SELECT * FROM ".$prefixRoot."pages_img  WHERE idCategory=:idCategory ORDER BY position ASC LIMIT 18 OFFSET $offset";
        $resi = $db->prepare($reqi);
        $resi->execute(array(':idCategory' => $idCategory));
        $nbi  = $resi->rowCount();
        $tabi = $resi->fetchAll();
        $resi->closeCursor();
        $resi = NULL;

	
    //---------------------------------------------------------  
	// PROCESS CONTENT
	//---------------------------------------------------------
		
		$photoContent="";
		
		if($nbi==0){
			$photoContent.='';
			$status=0;
		}else{
			
			for ($j = 0; $j < $nbi; $j++) {

				$r=$page->pagesImgLang($tabi[$j]['id'],1);
				$IMGDescription=(isset($r->description))? $r->description : '';
				
				if (file_exists($_SERVER['DOCUMENT_ROOT'].'/img/pages/'.$idCategory.'/gallery/images/'.$tabi[$j]['img'])) {
					$imgThumb = '/img/pages/'.$idCategory.'/gallery/rec/'.$tabi[$j]['img'];
				} else {
					$imgThumb = $imgDefaultRec;
				}
				
				$photoContent.= '<li>';
				$photoContent.= '<a href="/img/pages/'.$idCategory.'/gallery/images/'.$tabi[$j]['img'].'" data-caption="'.isset($r->description).'" data-fancybox="gallery" class="fancybox">';
				$photoContent.= '<div class="imgBoxPhoto" style="background-image:url('.$imgThumb.');background-repeat:no-repeat;background-size:103%; background-position:center center;">';
				$photoContent.= '</div>';
				$photoContent.= '</a>';
				$photoContent.= '</li>';

			}
						
			$status=1;
			
		}
			
		$_SESSION['offsetWall']=$offset+18;
		

	//---------------------------------------------------------
  	// RESPONSE
   	//---------------------------------------------------------

		$response = [
			'error' => false,
			'status' => $status,
			'photoContent' => $photoContent,
			'nbItem' => $nbi,
			'offsetWall' => $offset
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;