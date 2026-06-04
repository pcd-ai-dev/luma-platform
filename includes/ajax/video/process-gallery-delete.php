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

   		$idGalleryDel=$secure->v('int', 'id', false) ?? 0;
		

    //---------------------------------------------------------  
	// FUNCTION
	//---------------------------------------------------------
	
		function delGallery(PDO $db, string $prefixVideo, int $idGalleryDel): int {

			$basePath = $_SERVER['DOCUMENT_ROOT'] . "/img/videos/";

			function deleteFiles(array $dirs, string $filename): void {
				if (!$filename) return;

				foreach ($dirs as $dir) {
					$file = $dir . $filename;
					if (is_file($file)) {
						unlink($file);
					}
				}
			}

			//---------------------------------------------------------  
			// GET VIDEO IMG
			//---------------------------------------------------------

                $req = "SELECT * FROM ".$prefixVideo."item WHERE idGallery=:id";				
                $res = $db->prepare($req);
                $res->bindValue(':id', $idGalleryDel, PDO::PARAM_INT);
                $res->execute();
                $files = $res->fetchAll();
                $res->closeCursor();
                $res = NULL;


			//---------------------------------------------------------  
			// DELETE PROCESS DB
			//---------------------------------------------------------

				$reqsDelete = [
					"DELETE  FROM ".$prefixVideo."gallery WHERE id = :id",
					"DELETE  FROM ".$prefixVideo."gallery_lang WHERE idGallery= :id",
					"DELETE  FROM ".$prefixVideo."item WHERE idGallery= :id",
					"DELETE  FROM ".$prefixVideo."lang WHERE idGallery= :id"
				];

				foreach ($reqsDelete as $reqDelete) {
					$resDelete = $db->prepare($reqDelete);
					$resDelete->execute([':id' => $idGalleryDel]);
				}

				
			//---------------------------------------------------------  
			// DELETE VIDEO IMG
			//---------------------------------------------------------

				foreach ($files as $file) {
						deleteFiles([
							$basePath . 'images/',
							$basePath . 'square/',
							$basePath . 'rec/'
						], $file['img']);
					}
					
					$status=1;
				
			return $status?:0;
				
		}
			
    //---------------------------------------------------------  
	// PROCESS
	//---------------------------------------------------------
		
		$status=delGallery($db, $prefixVideo, $idGalleryDel);
		
		$video->recalculPositionGallery();
			
	
	//---------------------------------------------------------
  	// RESPONSE
   	//---------------------------------------------------------

		$response = [
			'error' => false,
			'status' => $status,
			'id' => $idGalleryDel
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;