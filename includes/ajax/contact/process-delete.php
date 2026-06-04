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
		use Front\ContactManager;


   //---------------------------------------------------------  
   // CONNEXIONS
   //---------------------------------------------------------

		include($_SERVER['DOCUMENT_ROOT'].'/config.php');


   //---------------------------------------------------------  
   // INIT CLASS
   //---------------------------------------------------------

        $secure = new SecureManager(requirePost: true, requireCsrf: true);
		$Contact= new ContactManager($db);


    //---------------------------------------------------------  
	// VARIABLES
	//---------------------------------------------------------
	
		$idContactDel=$secure->v('int', 'id', false) ?? 0;
		

    //---------------------------------------------------------  
	// FUNCTIONS
	//---------------------------------------------------------
	
		function delContact(PDO $db, $prefixContact, $idContactDel): void {
				
			// Suppression category
				$req = "DELETE  FROM ".$prefixContact."item WHERE id=:idContactDel";
				$res = $db->prepare($req);
				$res->bindValue(':idContactDel', $idContactDel, PDO::PARAM_INT);
				$res->execute();
				$res->closeCursor();
				$res = NULL;
				
			// Suppression category lang 
				$reql = "DELETE  FROM ".$prefixContact."lang WHERE idContact=:idContactDel";
				$resl = $db->prepare($reql);
				$resl->bindValue(':idContactDel', $idContactDel, PDO::PARAM_INT);
				$resl->execute();
				$resl->closeCursor();
				$resl = NULL;
		}


    //---------------------------------------------------------  
	// PROCESS
	//---------------------------------------------------------
		
		delContact($db, $prefixContact, $idContactDel);
		
		$Contact->recalculPositionContact();


   //---------------------------------------------------------
   // RESPONSE
   //---------------------------------------------------------
 
		$response = [
			'error' => false,
			'status' => 1,
			'id' => $idContactDel
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;