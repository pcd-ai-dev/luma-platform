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
		use Front\PhotoManager;
		use Action\ActionManager;
 
 
   //---------------------------------------------------------  
   // CONNEXIONS
   //---------------------------------------------------------			

		include($_SERVER['DOCUMENT_ROOT'].'/config.php');


   //---------------------------------------------------------
   // INIT CLASS SECURE
   //---------------------------------------------------------

		$secure = new SecureManager(requirePost: true, requireCsrf: true);


	//---------------------------------------------------------  
	// VARIABLES
	//---------------------------------------------------------

		$idGalleryDel=$secure->v('int', 'id', false) ?? 0;
		$idCategory=$secure->v('int', 'idCategory', false) ?? 0;
		

    //---------------------------------------------------------  
	// FUNCTION
	//---------------------------------------------------------
	
		function delGallery(PDO $db, string $prefixGallery, int $idGalleryDel, int $idCategory){

			$action = new ActionManager($db, $prefixGallery . 'img', 'idgallery', 'position', "");
			$photo  = new PhotoManager($db);

			$basePath = $_SERVER['DOCUMENT_ROOT'] . "/img/gallery/$idCategory/";

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
			// DELETE MAIN IMG
			//---------------------------------------------------------

				$infoGallery = $photo->galleryInfo($idGalleryDel);
				deleteFiles([
					$basePath . 'img/images/',
					$basePath . 'img/square/',
					$basePath . 'img/rec/'
				], $infoGallery->img ?? '');


			//---------------------------------------------------------  
			// DELETE GALLERY IMG
			//---------------------------------------------------------

				$files = $action->getImgGalleryPhoto($idGalleryDel);

				foreach ($files as $file) {
					deleteFiles([
						$basePath . 'gallery/images/',
						$basePath . 'gallery/square/',
						$basePath . 'gallery/rec/'
					], $file['img']);
				}


			//---------------------------------------------------------  
			// DELETE PROCESS DB
			//---------------------------------------------------------

				$reqs = [
					"DELETE FROM ".$prefixGallery."item WHERE id = :id",
					"DELETE FROM ".$prefixGallery."lang WHERE idGallery = :id",
					"DELETE FROM ".$prefixGallery."img WHERE idGallery = :id"
				];

				foreach ($reqs as $req) {
					$res = $db->prepare($req);
					$res->execute([':id' => $idGalleryDel]);
				}

				// PREVOIR SUPPRESSION IMG_LANG

				$photo->recalculPositionGallery();

				return 1;
				
				return $response?:0;
			
		}


	//---------------------------------------------------------  
	// PROCESS
	//---------------------------------------------------------
		
		delGallery($db, $prefixGallery, $idGalleryDel, $idCategory);
		

	
   //---------------------------------------------------------
   // RESPONSE
   //---------------------------------------------------------
 
		$response = [
			'error' => false,
			'status' => 1,
			'id' => $idGalleryDel
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;