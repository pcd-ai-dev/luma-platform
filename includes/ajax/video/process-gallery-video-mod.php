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
      use Front\StringManager;


   //---------------------------------------------------------
   // CONNEXIONS
   //---------------------------------------------------------

      include($_SERVER['DOCUMENT_ROOT'].'/config.php');
			

   //---------------------------------------------------------  
   // INIT CLASS
   //---------------------------------------------------------

      $secure = new SecureManager(requirePost: true, requireCsrf: true);
      $video= new VideoManager($db);
      $lang= new LangManager($db);
      $string= new StringManager($db);
		
		
  //---------------------------------------------------------  
	// VARIABLES
	//---------------------------------------------------------

      $modIdVideo=$secure->v('int', 'id', false) ?? 0;
      $idCategory=$secure->v('int', 'idCategory', false) ?? 0;
		
		
  //---------------------------------------------------------  
	// LANGUAGE
	//---------------------------------------------------------
	
      $tabLang=$lang->activeListLang();
		  $nbLang=count($tabLang);
		
	
  //---------------------------------------------------------  
	// DATA
	//---------------------------------------------------------
	
      $reqMod = "SELECT * FROM ".$prefixVideo."item WHERE id=:mod";
      $resMod = $db->prepare($reqMod);
      $resMod->execute(array(':mod'=>$modIdVideo));
      $rsMod=$resMod->fetch(PDO::FETCH_OBJ);
      $resMod->closeCursor();
      $resMod = NULL;
          
      $modUrlVideo = (isset($rsMod->url))? $rsMod->url : 0;
      $modUrlAudio = (isset($rsMod->idAudio))? $rsMod->idAudio : 0;
      $rtags=$video->videoLang($modIdVideo,1);
      $modTags = (isset($rtags->htag))? $rtags->htag : '';
      $idGallery=(isset($rsMod->idGallery))? $rsMod->idGallery : 0;

      $videoUId = uniqid('videoUId_');


  //---------------------------------------------------------  
	// PROCESS
	//---------------------------------------------------------

    $titleContent='';
    for($i=0; $i<$nbLang;$i++){
      $idLangCheck=$tabLang[$i]['id']; $rvideo=$video->videoLang($modIdVideo,$idLangCheck);
      $titleContent.='<div><input type="text" name="modTitleVideo_'.$idLangCheck.'" id="modTitleVideo_'.$idLangCheck.'" value="'.mb_convert_encoding((isset($rvideo->title))? $rvideo->title : '', 'UTF-8').'" class="input-full-lang flag'.$idLangCheck.'" placeholder="Titre de la vid&eacute;o..."/></div>';
    }

    $descriptionContent='';
    for($i=0; $i<$nbLang;$i++){
      $idLangCheck=$tabLang[$i]['id']; $rvideo=$video->videoLang($modIdVideo,$idLangCheck);
      $descriptionContent.='<div id="imglang"><img src="/img/l/'.$tabLang[$i]['id'].'.svg" width="21" /></div>';
      $descriptionContent.='<div><textarea name="modDescriptionVideo_'.$idLangCheck.'" id="modDescriptionVideo_'.$idLangCheck.'" class="form-area" rows="3" placeholder="Description de la vid&eacute;o...">'.mb_convert_encoding((isset($rvideo->description))? $rvideo->description : '', 'UTF-8').'</textarea></div>';
    }


    $formContent='';
    $formContent.='
      <!-- FORM -->
      <form action="" method="post" id="videoModForm" name="videoModForm" enctype="multipart/form-data">
        <div>&nbsp;</div>
        <div>&nbsp;</div>
        <div class="headerform">Titre  &nbsp;<span class="redb">*</span></div>
        <div class="corpsForm">'.$titleContent.'</div>
        <div>&nbsp;</div>
        <div>&nbsp;</div>

        <div class="headerform">Tags</div>
        <div class="corpsForm">
          <div class="tag-container">
						<div class="tags"></div>
						<input type="text" class="tag-input" placeholder="Ajouter un tag...">
						<input type="hidden" class="tags-hidden" name="tags" value="'.$modTags.'">
						<div class="suggestions"></div>
				  </div>
        <div>&nbsp;</div>
        <div>&nbsp;</div>
        
        <div class="headerform">Description : (3 lignes maximum)</div>
        <div class="corpsForm">'.$descriptionContent.'</div>
        <div>&nbsp;</div>
        <div>&nbsp;</div>

        <div class="headerform">Photo de la vid&eacute;o</div>
        <div class="corpsForm">
            <div><label class="containerRB">R&eacute;cup&eacute;ration de la photo originale <input type="checkbox" name="getPhoto" id="getPhoto" value="1" ><span class="checkmarkRB"></span></label></div>
            <div id="photoUploadAreaVideoMod"></div>
        </div>
        <div>&nbsp;</div>
        <div>&nbsp;</div>

        <div class="headerform">Num&eacute;ro de la vid&eacute;o</div>
        <div class="corpsForm">
          <input type="text" name="modUrl" id="modUrl" value="'.$modUrlVideo.'" class="input-normal" placeholder="Num&eacute;ro Youtube ou Vimeo..." />
        </div>
        <div>&nbsp;</div>
        <div>&nbsp;</div>

        <div class="headerform">URL Soundcloud</div>
        <div class="corpsForm">
          <input type="text" name="modUrlAudio" id="modUrlAudio" value="'.$modUrlAudio.'" class="input-normal" style="width:400px;" placeholder="URL complet Soundcloud..."/>
        </div>
        <div>&nbsp;</div>
        <div>&nbsp;</div>

        <div>
          <table style="border:0px;" cellspacing="0" cellpadding="0">
            <tr>
              <td>
                  <div id="cuButtonRed"><a href="" id="closeForm" style="color:#FFFFFF;">Fermer</a></div>
              </td>
              <td>&nbsp;</td>
              <td>
                <input type="hidden" name="modId" id="modId" value="'.$modIdVideo.'" />
                <input type="hidden" name="idCategory" id="idCategory" value="'.intval($idCategory).'" />
                <input type="hidden" name="idGallery" id="idGallery" value="'.intval($idGallery).'" />
                <input type="hidden" name="modVideoStart" id="modVideoStart" value="1" />
                <button class="cubutton" id="modVideoBtn">Modifiez</button>
              </td>
            </tr>
          </table>
        </div>
      </form>
      <!-- /FORM -->';

      
	//---------------------------------------------------------
  // RESPONSE
  //---------------------------------------------------------

		$formContent=mb_convert_encoding((isset($formContent))? $formContent : '', 'UTF-8');

		$response = [
			'formContent' => $formContent
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;