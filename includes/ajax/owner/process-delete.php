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
   // CLASS INIT
   //---------------------------------------------------------

		$secure = new SecureManager(requirePost: true, requireCsrf: true);


   //---------------------------------------------------------
   // VARIABLES
   //---------------------------------------------------------

		$idAdmin=$secure->v('int', 'id', false) ?? 0;
		
		
    //---------------------------------------------------------
	// FUNCTION
	//---------------------------------------------------------
	
		function delBack(PDO $db, string $prefixAdmin, int $idAdmin): int {
				
			$req = "DELETE  FROM ".$prefixAdmin."user WHERE id=:idAdmin";
			$res = $db->prepare($req);
			$res->bindValue(':idAdmin', $idAdmin, PDO::PARAM_INT);
			$res->execute();
			$res->closeCursor();
			$res = NULL;	
				
			$result=1;
				
			return $result?:0;
		}


    //---------------------------------------------------------
	// PROCESS
	//---------------------------------------------------------
	
		if(!empty($idAdmin)){$resultDel=delBack($db, $prefixAdmin, $idAdmin);}

		
   	//---------------------------------------------------------
  	// RESPONSE
   	//---------------------------------------------------------

		if($resultDel){$status=1;}else{$status=0;}

		$response = [
			'status' => $status,
			'id' => $idAdmin
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;