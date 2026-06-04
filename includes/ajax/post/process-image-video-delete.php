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

		$idImg=$secure->v('int', 'idImg', false) ?? 0;


    //---------------------------------------------------------
	// DATA UPDATE
   	//---------------------------------------------------------

		$reqi = "UPDATE ".$prefixPost."img SET url='' WHERE id=:idImg";
		$resi = $db->prepare($reqi);
		$resi->bindValue(':idImg', $idImg, PDO::PARAM_STR);
		$resi->execute();
		$resi->closeCursor();
		$resi = NULL;


	//---------------------------------------------------------
  	// RESPONSE
   	//---------------------------------------------------------

		$response = [
			'status' => 1,
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;