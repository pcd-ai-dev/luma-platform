<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */
 
 
	//---------------------------------------------------------  
	// VARS PROCESS
	//---------------------------------------------------------

        // DATA SHEMA
		if(empty($rp->img)){
			$imgLink='';
			$width='';
			$height='';
		}else{
			$imgLink='/img/post/'.$rp->idCategory.'/img/square/'.$rp->img;
        	[$width, $height] = getimagesize($_SERVER['DOCUMENT_ROOT'].$imgLink);
		}

        $datePost = new DateTimeImmutable((isset($rp->date)) ?$rp->date : 'now');
        $datePostModified = new DateTimeImmutable((isset($rp->dateModified))? $rp->dateModified : 'now');
        $dateStart = new DateTimeImmutable((isset($rp->startDate))? $rp->startDate : 'now');
        $dateEnd = new DateTimeImmutable((isset($rp->endDate))? $rp->endDate : 'now');

		$formatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
		$formatter ->setPattern("dd MMMM YYYY");

        $dateshow=$formatter->format($datePost);

	//---------------------------------------------------------  
	// ARTICLE IMG
	//--------------------------------------------------------- 

		$reqi = "SELECT * FROM ".$prefixPost."img WHERE idPost=:idPost ORDER by position ASC";	
		$resi = $db->prepare($reqi);
		$resi->bindValue(':idPost', $idNews, PDO::PARAM_STR);
		$resi->execute();
		$nbi=$resi->rowCount();
		$tabi = $resi->fetchAll();
		$resi->closeCursor();
		$resi = NULL;



?>
<!-- CSS Files -->
<link href="/css/type_post.css" rel="stylesheet" type="text/css" />
<!-- CSS Files end -->