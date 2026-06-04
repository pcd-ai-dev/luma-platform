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
		$videoCode=$secure->v('string', 'videoCode', false) ?? '';
		

   //---------------------------------------------------------  
   // PROCESS
   //---------------------------------------------------------
   
		$reqpadd = "INSERT INTO ".$prefixPost."img (idPost,video,idCategory) VALUES(:idPost,:video,:idCategory)";
		$respadd = $db->prepare($reqpadd);
		$respadd->bindValue(':idPost', $idPost, PDO::PARAM_STR);
		$respadd->bindValue(':idCategory', $idCategory, PDO::PARAM_STR);
		$respadd->bindValue(':video', $videoCode, PDO::PARAM_STR);
		$respadd->execute();
		$respadd->closeCursor();
		$respadd = NULL;
			
		$status=1;

	
  	//---------------------------------------------------------
  	// RESPONSE
   	//---------------------------------------------------------

		$response = [
			'error' => false,
			'status' => $status
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;