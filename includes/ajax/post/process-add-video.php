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
   // INIT CLASS
   //---------------------------------------------------------

		$secure = new SecureManager(requirePost: true, requireCsrf: true);


	//---------------------------------------------------------  
    // VARIABLES
    //---------------------------------------------------------
	
      $idCategory=$secure->v('int', 'idCategory', false) ?? 0; 
      $idPost=$secure->v('int', 'idPost', false) ?? 0; 
		
   //---------------------------------------------------------  
   // PROCESS
   //---------------------------------------------------------
   
      $formContent="";
      $formContent.= '<div class="corpsForm">';
      $formContent.= '<div>&nbsp;</div>';
      $formContent.= '<div id="videoForm"><label><b>Num&eacute;ro de la vid&eacute;o </b></label><input type="text" name="videoCode" id="videoCode" value="" class="input-normal" placeholder="Youtube ou Vimeo ou Url ..."/><a href="" id="'.$idPost.'" class="adminSmallButton" style="color:#FFFFFF;">Envoyer</a></div>';
      $formContent.= '<div>&nbsp;</div>';
      $formContent.= '</div>';


  	//---------------------------------------------------------
  	// RESPONSE
   //---------------------------------------------------------

		$formContent = mb_convert_encoding($formContent, 'UTF-8');

		$response = [
			'formContent' => $formContent
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;