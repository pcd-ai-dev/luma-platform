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

		$idSite=$secure->v('int', 'id', false) ?? 0;
		
		
   //---------------------------------------------------------  
   // FUNCTION
   //---------------------------------------------------------
	
		function delBack(PDO $db, string $prefixRoot, int $idSite): int {

			// Suppression Site
				$req = "DELETE  FROM ".$prefixRoot."site WHERE id=:idSite";
				$res = $db->prepare($req);
				$res->bindValue(':idSite', $idSite, PDO::PARAM_INT);
				$res->execute();
				$res->closeCursor();
				$res = NULL;

			// Suppression Site Category
				$reqs = "DELETE  FROM ".$prefixRoot."category WHERE idSite=:idSite";
				$ress = $db->prepare($reqs);
				$ress->bindValue(':idSite', $idSite, PDO::PARAM_INT);
				$ress->execute();
				$ress->closeCursor();
				$ress = NULL;

			// Suppression category lang 
				$reql = "DELETE  FROM ".$prefixRoot."category_lang WHERE idSite=:idSite";
				$resl = $db->prepare($reql);
				$resl->bindValue(':idSite', $idSite, PDO::PARAM_INT);
				$resl->execute();
				$resl->closeCursor();
				$resl = NULL;
				
			// Suppression page
				$reqp = "DELETE FROM ".$prefixRoot."pages WHERE idSite=:idSite";
				$resp = $db->prepare($reqp);
				$resp->bindValue(':idSite', $idSite, PDO::PARAM_INT);
				$resp->execute();
				$resp->closeCursor();
				$resp = NULL;

			$result=1;
				
			return $result?:0;

		}
	
		if(!empty($idSite)){$resultDel=delBack($db, $prefixRoot, $idSite);}

		
	//---------------------------------------------------------
  	// RESPONSE
   	//---------------------------------------------------------

		$status=($resultDel)? 1 : 0;

		$response = [
			'status' => $status,
			'id' => $idSite
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;