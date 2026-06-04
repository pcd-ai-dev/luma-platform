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
		use Action\ActionManager;
		use Front\LangManager;

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
	
		$entity=$secure->v('string', 'entity', false) ?? '';
		$id=$secure->v('int', 'id', false) ?? 0;
		$idCategory=$secure->v('int', 'idCategory', false) ?? 0;
		$folder=$secure->v('string', 'folder', false) ?? '';
		$table=$secure->v('string', 'table', false) ?? '';
		$table_img=$table.'_lang';
		$field=$secure->v('string', 'field', false) ?? '';
        $target=$secure->v('string', 'target', false) ?? '';
		$type=$secure->v('string', 'type', false) ?? '';
		$rl='';


   //---------------------------------------------------------
   // INIT CLASS
   //---------------------------------------------------------

		$action = new ActionManager($db, $table, $field, $target, $type);
		$lang= new LangManager($db);


    //---------------------------------------------------------  
	// LANGUAGE
	//--------------------------------------------------------- 
	
		$tabLang=$lang->activeListLang();


    //---------------------------------------------------------
	// DATA
	//---------------------------------------------------------

		$tab=$action->getImgGalleryPhoto($id);
		$nb=count($tab);


    //---------------------------------------------------------
	// PROCESS
	//---------------------------------------------------------

		$galleryContent="";
	
		if($nb==0){

			$galleryContent.='<div class="corpsForm">Cette galerie ne comporte pas d\'images. <br />Pour valider ce formulaire, vous devez charger une image.</div>';

		}else{

			if($type==0){$sortableType='globalDisplay';}else{$sortableType='imgDisplay';}
			
			$galleryContent.='<div class="corpsForm">';
			
			$galleryContent.='<ol class="sortable '.$sortableType.'">';
			
			for ($i=0; $i<$nb; $i++){
		
				$videoDisplay=(isset($tab[$i]['video']))? htmlspecialchars($tab[$i]['video'], ENT_QUOTES, 'UTF-8') : '';

				if(strlen($videoDisplay)>20){

                $videoSourceMP4="";
				$videoPoster=(isset($tab[$i]['img']))? '/img/'.$folder.'/'.$tab[$i]['idCategory'].'/gallery/square/'.$tab[$i]['img']: $imgDefaultSquare;
            
					if(empty($videoDisplay)){}else{
						$videoSourceMP4.= '<div>';
						$videoSourceMP4.= '<video controls id="poster_'.$tab[$i]['id'].'" width="100%" height="150" poster="'.$videoPoster.'">';
						$videoSourceMP4.= '<source src="'.$videoDisplay.'" type="video/mp4" />';
						$videoSourceMP4.= '</video>';
						$videoSourceMP4.= '</div>';
					}

				}else{
					if ( is_numeric( $videoDisplay ) ) {
						$videoLink="https://player.vimeo.com/video/".$videoDisplay;
					} else {
						$videoLink="https://www.youtube.com/embed/".$videoDisplay;
					}
				}
					
					
				if(empty($videoDisplay)){
					$galleryContent.='<li data-id="'.$tab[$i]['id'].'" class="img">';
					$galleryContent.='<div class="item img">';

					$galleryContent.='<span class="drag-handle" title="Déplacer">
										<svg width="18" height="18" viewBox="0 0 24 24">
											<polygon points="16.51 4.82 15.31 6.04 13.43 4.31 13.43 8.88 11.57 8.88 11.57 4.31 9.69 6.04 8.49 4.82 12.5 .87 16.51 4.82"/>
											<polygon points="20.3 16.51 19.08 15.31 20.82 13.43 16.24 13.43 16.24 11.57 20.82 11.57 19.08 9.69 20.3 8.49 24.25 12.5 20.3 16.51"/>
											<polygon points="8.49 20.18 9.69 18.96 11.57 20.69 11.57 16.12 13.43 16.12 13.43 20.69 15.31 18.96 16.51 20.18 12.5 24.13 8.49 20.18"/>
											<polygon points="4.7 8.49 5.92 9.69 4.18 11.57 8.76 11.57 8.76 13.43 4.18 13.43 5.92 15.31 4.7 16.51 .75 12.5 4.7 8.49"/>
										</svg>
										</span>';
					
					$imgLink=(!empty($tab[$i]['img']))?	'/img/'.$folder.'/'.$tab[$i]['idCategory'].'/gallery/images/'.$tab[$i]['img'] : $imgDefaultSquare;
					$imgDisplay=(!empty($tab[$i]['img']))?	'/img/'.$folder.'/'.$tab[$i]['idCategory'].'/gallery/square/'.$tab[$i]['img'] : $imgDefaultSquare;	
					$galleryContent.='<a href="'.$imgLink.'" data-fancybox="gallery" class="fancybox">';
					$galleryContent.='<img src="'.$imgDisplay.'" width="100%" alt="" draggable="false" />';
					$galleryContent.='</a>';

					if($type==1){

						$galleryContent.='<div>';
						$galleryContent.='<img src="/img/interface/icons/poubelleoff.png" width="16" height="16" style="border:0px;" class="deleteImg"  id="'.$tab[$i]['id'].'"/>';
						$galleryContent.='</div>';

						$galleryContent.='<div class="txtarea">';

						foreach($tabLang as $value){
							${$rl}=$action->galleryImgLang($tab[$i]['id'],$value['id'],$table_img);
							$descriptionIMG=(isset(${$rl}->description))? ${$rl}->description : 'Description '.$value['code'];
							$pk = json_encode([
								"id" => $tab[$i]['id'],
								"idLang" => $value['id'],
								"idCategory" => $idCategory,
								"field" => $field,
								"idField" => $id,
								"table" => $table_img,
								"video" => 0
							]);

							$galleryContent .= '
							<div 
								class="editable"
								data-type="textarea"
								data-name="value"
								data-pk=\''.htmlspecialchars($pk, ENT_QUOTES, "UTF-8").'\'
							>
							'.$descriptionIMG.'
							</div>';
							
						}

						$galleryContent.='</div>';

					}

					$galleryContent.='</div>';

					$galleryContent.='</li>';

				}else{

					$uid = uniqid('uplv_');

					$galleryContent.='<li data-id="'.$tab[$i]['id'].'" class="img">';
					$galleryContent.='<div class="item img">';

					$galleryContent.='<span class="drag-handle" title="Déplacer">
										<svg width="18" height="18" viewBox="0 0 24 24">
											<polygon points="16.51 4.82 15.31 6.04 13.43 4.31 13.43 8.88 11.57 8.88 11.57 4.31 9.69 6.04 8.49 4.82 12.5 .87 16.51 4.82"/>
											<polygon points="20.3 16.51 19.08 15.31 20.82 13.43 16.24 13.43 16.24 11.57 20.82 11.57 19.08 9.69 20.3 8.49 24.25 12.5 20.3 16.51"/>
											<polygon points="8.49 20.18 9.69 18.96 11.57 20.69 11.57 16.12 13.43 16.12 13.43 20.69 15.31 18.96 16.51 20.18 12.5 24.13 8.49 20.18"/>
											<polygon points="4.7 8.49 5.92 9.69 4.18 11.57 8.76 11.57 8.76 13.43 4.18 13.43 5.92 15.31 4.7 16.51 .75 12.5 4.7 8.49"/>
										</svg>
										</span>';

					if(strlen($videoDisplay)>20){
                    	$galleryContent.=$videoSourceMP4;
					}else{
						$galleryContent.='<iframe style="border:0px;" width="100%" src="'.$videoLink.'" framestyle="border:0px;" allow="autoplay; encrypted-media" allowfullscreen></iframe>';
					}
					$galleryContent.='<div>';
					$galleryContent.='<img src="/img/interface/icons/poubelleoff.png" width="16" height="16" style="border:0px;" class="deleteImg"  id="'.$tab[$i]['id'].'"/>';
					$galleryContent.='</div>';

					$galleryContent.='<div id="'.$uid.'">';
					$galleryContent.='<input type="file" id="fileInput_'.$uid.'" accept="image/*" style="display:none;">';
                    $galleryContent.='<button type="button" id="browse_'.$uid.'" class="btn btn-upload-small" style="width:100%;" >Ajouter un poster</button>';
                    $galleryContent.='<div class="progress" style="display:none"><span class="percent">0%</span></div>';
					$galleryContent.='<button draggable="false" type="button" class="btn-danger resetImageBtn_'.$uid.'" style="margin-top:10px;display:none;">Supprimer</button>';
					$galleryContent.='</div>';

					$galleryContent.='</div>';
					$galleryContent.='</li>';

					$galleryContent.='
									<script>
										window.__uploaders_Posters = window.__uploaders_Posters || [];
										window.__uploaders_Posters.push({
											uid: "'.$uid.'",
											entity: "'.$entity.'",
											id: '.$tab[$i]['id'].',
											field: "img",
											target: "id",
											idParent: '.$idCategory.',
											folder: "gallery",
											defaultImg: "'.$imgDefaultSquare.'",
											type: "5",
										});
									</script>
									';
				}		
			}
			
			$galleryContent.='</ol>';
			
			$galleryContent.='</div>';
		
		}


	//---------------------------------------------------------
  	// RESPONSE
   	//---------------------------------------------------------

		$galleryContent=mb_convert_encoding($galleryContent, 'UTF-8');

		$response = [
			'error' => false,
			'galleryContent' => $galleryContent
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;