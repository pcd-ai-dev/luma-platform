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


   //---------------------------------------------------------
   // CONNEXIONS
   //---------------------------------------------------------

      include($_SERVER['DOCUMENT_ROOT'].'/config.php');
	  

   //---------------------------------------------------------
   // INIT CLASS SECURE
   //---------------------------------------------------------

		$secure = new SecureManager(requirePost: true, requireCsrf: true);


   //---------------------------------------------------------  
   // VARIABLES
   //---------------------------------------------------------

   		$idCategory = $secure->v('int', 'idCategory', false) ?? 0;


    //---------------------------------------------------------  
	// DATA
	//---------------------------------------------------------
		
		$req = "SELECT v.*, l.* 
				FROM ".$prefixVideo."item v
				INNER JOIN ".$prefixVideo."lang l
				ON v.id=l.idVideo
				AND l.idLang='1'
				WHERE v.idCategory=:idCategory
				AND v.url!='0'
				ORDER BY v.globalPosition ASC";
		$res = $db->prepare($req);
		$res->bindValue(':idCategory', $idCategory, PDO::PARAM_STR);
		$res->execute();
		$nb=$res->rowCount();
		$datas = $res->fetchAll();
		$res->closeCursor();
		$res = NULL;

		
    //---------------------------------------------------------  
	// PROCESS
	//---------------------------------------------------------

		$videoContent = '<div class="corpsForm">';

		if ($nb == 0) {
			$videoContent .= "Aucune vid&eacute;o n'a &eacute;t&eacute; post&eacute;.";
		} else {

			$videoContent .= '<ol class="sortable videoGlobal" style="margin-left:-40px; width:100%;">';

			foreach ($datas as $data) {

				$idVideo = !empty($data['idVideo']) ? (int)$data['idVideo'] : 0;

				// Status icons
				$imgStatus     = ((int)$data['status'] === 1) ? 'statusOnRed.png' : 'statusOff.png';
				$styleHome = ((int)$data['home'] === 1)   ? 'style="font-size:25px;color:green;"' : 'style="font-size:25px;color:#CCCCCC;"';

				// Image vidéo
				$imgVideo = (!empty($data['img']) &&
					file_exists($_SERVER['DOCUMENT_ROOT'].'/img/videos/square/'.$data['img']))
					? '/img/videos/square/'.$data['img']
					: $imgDefaultSquare;

				// Titre
				$title = !empty($data['title']) ? $data['title'] : 'Sans titre';

				$videoContent .= '
				<li data-id="'.$idVideo.'" id="list_'.$idVideo.'">

                    <div class="item">
						
						<table width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td width="45">
									<img src="'.$imgVideo.'" width="45" />
								</td>

								<td style="width:15px;">&nbsp;</td>

								<td style="text-align:left;">'.htmlspecialchars($title, ENT_QUOTES, 'UTF-8').'</td>

								<td width="13" align="right">
									<button id="checkStatusHome'.$idVideo.'"
										data-id="'.$idVideo.'"
										data-table="'.$prefixVideo.'item"
										data-field="home"
										data-target-id="id"
										data-output="light"
										style="border:0px;background:none;">
										<span id="pen_'.$idVideo.'" '.$styleHome.'/>&#9728;</span>
									</button>
								</td>
								<td width="13" align="right">
									<button id="checkStatusItem'.$idVideo.'"
										data-id="'.$idVideo.'"
										data-table="'.$prefixVideo.'item"
										data-field="status"
										data-target-id="id"
										data-output="red"
										style="border:0px;background:none;">
										<img src="/img/interface/icons/'.$imgStatus.'" width="13" />
									</button>
								</td>
							</tr>
						</table>
					</div>
				</li>';
			}

			$videoContent .= '</ol>';
		}

		$videoContent .= '</div>';

	//---------------------------------------------------------
  	// RESPONSE
   	//---------------------------------------------------------

		$videoContent=mb_convert_encoding((isset($videoContent))? $videoContent : '', 'UTF-8');

		$response = [
			'videoContent' => $videoContent
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;