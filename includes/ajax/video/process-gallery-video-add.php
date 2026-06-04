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

   //---------------------------------------------------------
   // CONNEXIONS
   //---------------------------------------------------------

      include($_SERVER['DOCUMENT_ROOT'].'/config.php');
			

   //---------------------------------------------------------  
   // INIT CLASS
   //---------------------------------------------------------

      $secure = new SecureManager(requirePost: true, requireCsrf: true);
      $lang= new LangManager($db);

 
  //---------------------------------------------------------  
	// VARIABLES
	//---------------------------------------------------------

    $idGallery=$secure->v('int', 'id', false) ?? 0;
    $idCategory=$secure->v('int', 'idCategory', false) ?? 0;


  //---------------------------------------------------------  
	// LANGUAGE
	//---------------------------------------------------------
	
      $tabLang=$lang->activeListLang();
		  $nbLang=count($tabLang);
		
	
  //---------------------------------------------------------  
	// PROCESS
	//---------------------------------------------------------

    $titleContent='';
    for($i=0; $i<$nbLang;$i++){
      $idLangCheck=$tabLang[$i]['id'];
      $titleContent.='<div><input type="text" name="titleAdd_'.$idLangCheck.'" id="titleAdd_'.$idLangCheck.'" value="" class="input-full-lang flag'.$idLangCheck.'" placeholder="Titre de la vid&eacute;o..."/></div>';
    }


    $descriptionContent='';
    for($i=0; $i<$nbLang;$i++){
      $idLangCheck=$tabLang[$i]['id'];
      $descriptionContent.='<div id="imglang"><img src="/img/l/'.$tabLang[$i]['id'].'.svg" width="21" /></div>';
      $descriptionContent.='<div><textarea name="descriptionAdd_'.$idLangCheck.'" id="descriptionAdd_'.$idLangCheck.'" class="form-area" rows="3" placeholder="Description de la vid&eacute;o..."></textarea></div>';
    }

    $formContent='';
    $formContent.='
      <!-- FORM -->
        <form action="" method="post" id="videoAddForm" name="videoAddForm" enctype="multipart/form-data">
        <div>&nbsp;</div>
        <div>&nbsp;</div>
        <div class="headerform">Ajout d\'une vid&eacute;o : Titre &nbsp;<span class="redb">*</span></div>
        <div class="corpsForm">'.$titleContent.'</div>
        <div>&nbsp;</div>
        <div>&nbsp;</div>

        <div class="headerform">Tags</div>
        <div class="corpsForm">
        <div class="tag-container">
						<div class="tags"></div>
						<input type="text" class="tag-input" placeholder="Ajouter un tag...">
						<input type="hidden" class="tags-hidden" name="tags" value="">
						<div class="suggestions"></div>
				</div>
        <div>&nbsp;</div>
        <div>&nbsp;</div>

        <div class="headerform">Description : (3 lignes maximum)</div>
        <div class="corpsForm">'.$descriptionContent.'</div>
        <div>&nbsp;</div>
        <div>&nbsp;</div>

        <div class="headerform">Num&eacute;ro de la vid&eacute;o</div>
        <div class="corpsForm">
        <input type="text" name="url_video" id="url_video" value="" class="input-normal requiredField" placeholder="Num&eacute;ro Youtube ou Vimeo..."/>
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
                <input type="hidden" name="idCategory" id="idCategory" value="'.intval($idCategory).'" />
                <input type="hidden" name="idGallery" id="idGallery" value="'.intval($idGallery).'" />
                <input type="hidden" name="addVideoStart" id="addVideoStart" value="1" />
                <button class="cubutton" id="addVideoBtn">Ajoutez</button>
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