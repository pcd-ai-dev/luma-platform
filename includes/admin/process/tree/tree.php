<?php

/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
*  @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*  @version  Release: 3
*/

	//---------------------------------------------------------  
	// FILE SECURE
	//---------------------------------------------------------  

		if (!$session->getAdminData()) {
			header('HTTP/1.1 403 Forbidden');
			exit('Accès interdit');
		}

    //---------------------------------------------------------  
	// SITE TREE
	//---------------------------------------------------------

		function has_children(array $datas, int $id): bool {
			foreach ($datas as $data) {
				if ($data['parent'] == $id)
					return true;
				}
			return false;
	  	} 

	  	function build_menu(string $varLink, array $datas, int $parent=1): string { 
		
			if($parent==1){$result='<ol class="sortable" id="mainTree" style="margin-left:-40px;">';}else{$result = '<ol>';}

			foreach ($datas as $data){

				if ($data['parent'] == $parent){

					if(intval($data['status'])=='1'){$imgstatus='statusOnGreen.png';}else{$imgstatus='statusOff.png';}
					if(empty($data['color'])){$color="#333333";}else{$color=$data['color'];}
					$urllinksc='/manager'.$varLink.'/admin_tree/param_content/'.$data['idCheck'].'/1/0/0/';

					$result.= '<li data-id="'.$data['idCheck'].'" id="list_page_'.$data['idCheck'].'"> ';
					$result.= '<div class="item" style="border-left:15px solid '.$color.'; border-radius:5px;">';
					$result.= '<table style="border:0px;" width="100%" cellspacing="0" cellpadding="0">';
					$result.= '<tr>';

					if (has_children($datas,$data['idCheck'])){
						$result.= '<td width="20" class="discloseTD" align="center">';
						$result.= '<span class="disclose"><span></span></span>';
						$result.= '</td>';
						$result.= '<td width="5">&nbsp;</td>';
					}

					$result.= '<td valign="bottom" width="20">';
					$result.= '<img src="/img/interface/icons/poubelleoff.png" width="16" height="16" style="border:0px;" class="delete"  id="'.$data['idCheck'].'"/>';
					$result.= '</td>';
					$result.= '<td style="text-align:left;">&nbsp;|&nbsp;<a href="'.$urllinksc.'" target="_self" >'.$data['idCheck'].' - '.$data['name'].'</a></td>';
					$result.= '<td width="5"><i class="gg-duplicate" id="'.$data['idCheck'].'"></i></td>';
					$result.= '<td width="20">&nbsp;</td>';
					$result.= '<td width="5" style="text-align:right;">';
					$result.= '<button id="checkStatus'.$data['idCheck'].'" data-id="'.$data['idCheck'].'" data-table="root_category" data-field="status" data-target-id="id" data-output="green" style="border:0px;background:none;">';
                    $result.= '<img src="/img/interface/icons/'.$imgstatus.'" width="13" height="13" style="border:0px;"/>';
                    $result.= '</button>';
					$result.= '</td>';
					$result.= '</tr>';
					$result.= '</table>';
					$result.= '</div>';

					if (has_children($datas,$data['idCheck'])){
						$result.= build_menu($varLink, $datas,$data['idCheck'],1);
					}
					
					$result.= "</li>";
				}
			}
			$result.= "</ol>";
	  
			return $result;
	  	}


		function affCat(PDO $db, string $prefixRoot, string $varLink, int $idSite): string {

			$reqCat = "SELECT c.idSite, c.id as idCheck, c.parent, c.color, c.status, l.idCategory, l.name, l.id
						FROM ".$prefixRoot."category c
						INNER JOIN ".$prefixRoot."category_lang
						l ON c.id=l.idCategory
						AND l.idLang=1
						WHERE c.idSite
						IN (0, $idSite)
						ORDER BY c.position";		 
			$resCat = $db->prepare($reqCat);
			$resCat->execute();
			$tabCat=$resCat->fetchAll();
			$resCat->closeCursor();
			$resCat = NULL;

			$siteTree='';
			$siteTree.= '<div class="corpsForm">';
			$siteTree.= build_menu($varLink, $tabCat);
			$siteTree.= '</div>';

			return $siteTree?:null;

		}