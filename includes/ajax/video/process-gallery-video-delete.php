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
		use Front\VideoManager;


   //---------------------------------------------------------
   // CONNEXIONS
   //---------------------------------------------------------

		include($_SERVER['DOCUMENT_ROOT'].'/config.php');


   //---------------------------------------------------------
   // INIT CLASS
   //---------------------------------------------------------

		$secure = new SecureManager(requirePost: true, requireCsrf: true);

		$video= new VideoManager($db,[
            'table_item' => $prefixVideo.'item',
            'table_lang' => $prefixVideo.'lang',
            'table_gallery' => $prefixVideo.'gallery',
            'table_gallery_lang' => $prefixVideo.'gallery_lang'
			]
		);


   //---------------------------------------------------------  
   // VARIABLES
   //---------------------------------------------------------

		$idVideoDel=$secure->v('int', 'id', false) ?? 0;


    //---------------------------------------------------------  
	// FUNCTION
	//---------------------------------------------------------
	
		function delVideo(PDO $db, string $prefixVideo, int $idVideoDel): int {
							
			// Suppression category
		
				$req = "DELETE  FROM ".$prefixVideo."item WHERE id=:idVideoDel";
				$res = $db->prepare($req);
				$res->bindValue(':idVideoDel', $idVideoDel, PDO::PARAM_INT);
				$res->execute();
				$res->closeCursor();
				$res = NULL;
				
			// Suppression category lang 
		
				$reql = "DELETE  FROM ".$prefixVideo."lang WHERE idVideo=:idVideoDel";
				$resl = $db->prepare($reql);
				$resl->bindValue(':idVideoDel', $idVideoDel, PDO::PARAM_INT);
				$resl->execute();
				$resl->closeCursor();
				$resl = NULL;
				
				$status=1;
				
				return $status?:0;
		}


    //---------------------------------------------------------  
	// PROCESS
	//---------------------------------------------------------
		
		if(delVideo($db, $prefixVideo, $idVideoDel)){$status=1;}else{$status=0;}
		
		$video->recalculPositionVideo();
	

	//---------------------------------------------------------
  	// RESPONSE
   	//---------------------------------------------------------

		$response = [
			'error' => false,
			'status' => $status,
			'idVideo' => $idVideoDel
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;