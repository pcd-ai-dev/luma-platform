<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */


	//---------------------------------------------------------  
	// NEWS PROCESS
	//---------------------------------------------------------

	//---------------------------------------------------------  
	// DATA POST
	//--------------------------------------------------------- 

		$req = "SELECT p.*, l.* FROM ".$prefixPost."item p
			INNER JOIN ".$prefixPost."lang l
			ON p.id=l.idPost
			AND l.idLang=:idLang
			WHERE p.status=1
			AND p.idCategory=:idCategory
			AND idSite=:idSite
			ORDER BY p.date DESC LIMIT 30 OFFSET 0";	
		$res = $db->prepare($req);
		$res->bindParam(':idCategory', $idCategory, PDO::PARAM_INT);
		$res->bindParam(':idLang', $idLang, PDO::PARAM_INT);
		$res->bindParam(':idSite', $idSite, PDO::PARAM_INT);
		$res->execute();
		$nb=$res->rowCount();
		$tab = $res->fetchAll();
		$res->closeCursor();
		$res = NULL;


	//---------------------------------------------------------  
	// PROCESS POST
	//---------------------------------------------------------

		$newsContent="";
		$newsContent.='<div id="postArea">';
		for($i=0;$i<$nb;$i++){
			$newsContent.='';
			$newsContent.=$post->postListDisplay($tab[$i]['idPost'],$idLang,intval($is->articlePage),$varLinkData);
		}
		$newsContent.='</div>';	




	//---------------------------------------------------------  
	// NEWS DB HOME
	//--------------------------------------------------------- 

		$idPageNews=(isset($is->newsPage))? intval($is->newsPage) : 0;

		$reqn = "SELECT p.id, p.date, p.img, p.idCategory, p.video, l.title, l.text FROM ".$prefixPost."item p
				INNER JOIN ".$prefixPost."lang l
				ON p.id=l.idPost
				WHERE p.idCategory=:idCategory
				AND p.status=1 AND p.idSite=:idSite
				ORDER BY p.id DESC LIMIT 1";	
		$resn = $db->prepare($reqn);
		$resn->bindValue(':idCategory', $idPageNews, PDO::PARAM_INT);
		$resn->bindValue(':idSite', $idSite, PDO::PARAM_INT);
		$resn->execute();
		$rn = $resn->fetch(PDO::FETCH_OBJ);
		$resn->closeCursor();
		$resn = NULL;
		
		//PostDate
		$dateTimeEvent = new DateTime($rn->date ?? '');
		$formatterEvent = new IntlDateFormatter('fr_FR', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
		$formatterEvent ->setPattern("dd MMMM yyyy");
		$postDate=$formatterEvent->format($dateTimeEvent);

		//PostTitle
		$postTitle=$rn->title ?? '';

		//PostResume
		$postResume=$string->cleanCut(strip_tags($rn->text ?? ''),$length='100',$cutString = '...');

		//PostImg
		if (is_object($rn) && !empty($rn->img) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/img/post/' . $rn->idCategory . '/img/rec/' . $rn->img)) {
			$postImg='/img/post/'.$rn->idCategory.'/img/rec/'.$rn->img;
			echo '<style>#postImgArea{background-image:url("'.$postImg.'");}</style>';
			
		}else{
			$postImg='/img/post/default.jpg';
			echo '<style>#postImgArea{background-image:url("'.$postImg.'");}</style>';
		}

		//PostLink
		if (is_object($rn)) {
			$postLink = '/' . $is->varLink . '/news-'.$is->articlePage.'-' . $rn->id . '-' . $string->cleanurl($rn->title ?? '') . '.html';
		} else {
			$postLink = ''; // ou une valeur par défaut
		}

		//PostVideo
		if(empty($rn->video)){
			$postVideo='';
		}else{
			
			if ( is_numeric($rn->video) ) {
				$videoLinkcolorBox = "https://player.vimeo.com/video/".$rn->video;
			} else {
				$videoLinkcolorBox = "https://www.youtube.com/embed/".$rn->video;
			}
			
			$postVideo='<div class="overlay-content-header">';
			$postVideo.='<a data-fancybox class="fancybox-video" href="'.$videoLinkcolorBox.'">';
			$postVideo.='<div class="play-button">&nbsp;</div>';
			$postVideo.='</a>';
			$postVideo.='</div>';
		}

?>
<!-- CSS Files -->
<link href="/css/type_post.css" rel="stylesheet" type="text/css" />
<!-- CSS Files end -->
