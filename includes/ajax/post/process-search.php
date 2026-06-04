<?php declare(strict_types=1);

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */


    //---------------------------------------------------------  
   	// INIT NAME SPACES
   	//---------------------------------------------------------

		use Front\LangManager;
   		use Front\StringManager;
		use Tools\SecureManager;

		
   //---------------------------------------------------------  
   // CONNEXIONS
   //---------------------------------------------------------

		include($_SERVER['DOCUMENT_ROOT'].'/config.php');


   //---------------------------------------------------------  
   // INIT CLASS
   //---------------------------------------------------------

		$string= new StringManager($db);		
		$lang= new LangManager($db);
		$secure = new SecureManager(requirePost: true, requireCsrf: true);
    

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
			$_SESSION['langue'] = "1";
			$idLang             = '1';
			include $_SERVER['DOCUMENT_ROOT'].'/includes/lang/fr.php';
		}

    
    //---------------------------------------------------------  
    // VARIABLES
    //---------------------------------------------------------

		$idSite=$_SESSION['siteData']['id'];
		$varLink=$_SESSION['siteData']['varLink'];
		$idCategory		 = $secure->v('int', 'idCategory', false) ?? 0;
		$nbTotalResult   = $secure->v('string', 'nbresult', false) ?? '';
		$queryString     = $secure->v('string', 'q', false) ?? '';

		$queryStringshow = $queryString;
		$queryString     = str_replace('  ', ' ', preg_replace("# [[:alnum:]]{1,1}[\.,;:]? #", "", str_replace(' ', '  ', $queryString)));
		$queryString     = str_replace("'", ' ', $queryString);
		$noword          = 'de|des|la|le|les|en|dans|et';
		$other           = "";
		$queryString     = preg_replace('`(^|\W)(' . $noword . ')(\W|$)`si', '$1 ' . $other . ' $3', $queryString);
		$queryString     = $queryString;
		$operator        = " & ";
		$isolang = "fr";
		
		$queryStringArray      = explode(" ", $queryString);
		$queryStringArrayCount = count($queryStringArray);
		
		$queryStringQuote = '';
		
		for ($i = 0, $j = 1; $i < $queryStringArrayCount; $i++, $j++) {
			if (empty($queryStringArray[$i]) || ($queryStringArray[$i] == " ")) {
			} else {
				$queryStringQuote .= ' +' . $queryStringArray[$i] . '*';
			}
		}
		
		$queryStringOperator = $queryStringQuote;
		
		//$resultContent.= $queryStringOperator;

    
    //---------------------------------------------------------  
    // INIT
    //--------------------------------------------------------- 
    
		if($idLang==1){
			setlocale(LC_TIME, 'fr', 'fr_FR', 'french', 'fra', 'fra_FRA', 'fr_FR.ISO_8859-1', 'fra_FRA.ISO_8859-1', 'fr_FR.utf8', 'fr_FR.utf-8', 'fra_FRA.utf8', 'fra_FRA.utf-8');
		}
	
	
    //---------------------------------------------------------  
    // DB
    //--------------------------------------------------------- 
	
		if(empty($queryString)){
		
			$req = "SELECT p.id, p.img, p.url, p.date, p.dateHistory, p.position, p.status, p.type, p.video, p.source, p.idCategory, l.id, l.idPost, l.idLang, l.title, l.text
					FROM ".$prefixPost."item p
					INNER JOIN ".$prefixPost."lang l
					ON p.id=l.idPost AND l.idLang='1'
					WHERE p.idCategory=:idCategory AND p.idSite=:idSite
					ORDER BY p.date DESC
					LIMIT 20";
			$res = $db->prepare($req);
			$res->bindParam(':idCategory', $idCategory, PDO::PARAM_INT);
			$res->bindParam(':idSite', $idSite, PDO::PARAM_INT);
			$res->execute();
			$nb=$res->rowCount();
			$tab = $res->fetchAll();
			$res->closeCursor();
			$res = NULL;
			
			
		}else{
			
			$req = "SELECT p.id, p.img, p.url, p.date, p.position, p.status, p.type, p.video, p.source, p.idCategory, l.id, l.idPost, l.idLang, l.title, l.text,
			(
			(1.3 * (MATCH(l.title) AGAINST ('" . $queryStringOperator . "' IN BOOLEAN MODE))) +
			(0.4 * (MATCH(l.text) AGAINST ('" . $queryStringOperator . "' IN BOOLEAN MODE)))
			) as score
			FROM ".$prefixPost."item p
			INNER JOIN ".$prefixPost."lang l
			ON p.id=l.idPost AND l.idLang='1'
			WHERE MATCH (l.title,l.text)
			AGAINST ('" . $queryStringOperator . "' IN BOOLEAN MODE)
			AND p.idCategory=:idCategory AND p.idSite=:idSite
			ORDER BY p.date DESC
			LIMIT 20";
			$res = $db->prepare($req);
			$res->bindParam(':idCategory', $idCategory, PDO::PARAM_INT);
			$res->bindParam(':idSite', $idSite, PDO::PARAM_INT);
			$res->execute();
			$nb=$res->rowCount();
			$tab = $res->fetchAll();
			$res->closeCursor();
			$res = NULL;
			
		}

	
    //---------------------------------------------------------  
    // PROCESS
    //--------------------------------------------------------- 

		$resultContent="";
		if ($nb==0){
			$resultContent.= "Aucun article n'a &eacute;t&eacute; post&eacute;.";
		}else{ 
			$resultContent.= '<ol class="sortable" style="margin-left:-40px; width:100%;">';
			
			for($i=0;$i<$nb;$i++){
								  
				$id = $tab[$i]['idPost']; 
				$urlLinkPost = '/manager' . $varLink . '/admin_tree/param_content/' . $idCategory . '/4/0/' . $id . '/';
				$imgStatus=($tab[$i]['status']==1)? 'statusOnRed.png' : 'statusOff.png';

				$photoProfilShow=($tab[$i]['img']!="")? '/img/post/'.$tab[$i]['idCategory'].'/img/square/'.$tab[$i]['img'] : $imgDefaultSquare;
				
				$dateDisplay=($isolang=="en")? date("g:i a | F j, Y", strtotime($tab[$i]['date'])) : date("d.m.Y H\hi", strtotime($tab[$i]['date']));

				$showname=(empty($tab[$i]['title']))? $dateDisplay.' | Sans titre' : $dateDisplay.' | '.mb_convert_encoding($tab[$i]['title'], 'UTF-8');
                                  
				$resultContent.= '<li data-id="'.$id.'" id="list_post_'.$id.'">';
				$resultContent.= '<div class="item">';
				$resultContent.= '<table style="border:0px;" width="100%" cellspacing="0" cellpadding="0">';
				$resultContent.= '<tr>';
				$resultContent.= '<td width="20">';
				$resultContent.= '<img src="/img/interface/icons/poubelleoff.png" width="16" height="16" style="border:0px;" class="deletePost"  id="'.$id.'"/>';
				$resultContent.= '</td>';
				$resultContent.= '<td width="5"><img src="'.$photoProfilShow.'" width="60"  style="border:0px;" /></td>';
				$resultContent.= '<td width="5">&nbsp;</td>';
				$resultContent.= '<td style="text-align:left;"><a href="'.$urlLinkPost.'" target="_self">'.$showname.'</a></td>';
				$resultContent.= '<td style="text-align:right;">';
				$resultContent.= '
					<button id="checkStatusItem'.$id.'"
						data-id="'.$id.'"
						data-table="'.$prefixPost.'item"
						data-field="status"
						data-target-id="id"
						data-output="red"
						style="border:0px;background:none;">
						<img src="/img/interface/icons/'.$imgStatus.'" width="13" />
					</button>';
				$resultContent.= '</td>';
				$resultContent.= '</tr>';
				$resultContent.= '</table>';
				$resultContent.= '</div>';
				$resultContent.= '</li>';
			} 
			
			$resultContent.= '</ol>';
		}
		

  	//---------------------------------------------------------
  	// RESPONSE
   	//---------------------------------------------------------

		$resultContent = mb_convert_encoding($resultContent, 'UTF-8');

		$response = [
			'searchContent' => $resultContent
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;