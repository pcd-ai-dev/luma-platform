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
		use Front\PostManager;
		use Action\ActionManager;
 

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

		$idPostDel=$secure->v('int', 'id', false) ?? 0;
		$idCategory=$secure->v('int', 'idCategory', false) ?? 0;
		

    //---------------------------------------------------------  
	// Del category
	//---------------------------------------------------------
	
		function delPost(PDO $db, string $prefixPost, int $idCategory, int $idPostDel): void {

			$action = new ActionManager($db, $prefixPost.'img', 'idPost', 'position', "");
			$post  = new PostManager($db);

			$basePath = $_SERVER['DOCUMENT_ROOT'] . '/img/post/'.$idCategory.'/';

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

				$infoPost = $post->infoPost($idPostDel);
				deleteFiles([
					$basePath . 'img/images/',
					$basePath . 'img/square/',
					$basePath . 'img/rec/'
				], $infoPost->img ?? '');


			//---------------------------------------------------------  
			// DELETE GALLERY IMG
			//---------------------------------------------------------

				$files = $action->getImgGalleryPhoto($idPostDel);

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
					"DELETE FROM ".$prefixPost."item WHERE id = :id",
					"DELETE FROM ".$prefixPost."lang WHERE idPost = :id",
					"DELETE FROM ".$prefixPost."img WHERE idPost = :id",
					"DELETE FROM ".$prefixPost."img_lang WHERE idPost = :id",
				];

				foreach ($reqs as $req) {
					$res = $db->prepare($req);
					$res->bindValue(':id', $idPostDel, PDO::PARAM_INT);
					$res->execute();
					$res->closeCursor();
					$res = NULL;
				}

				$post->recalculpositionblog();

		}
		
		delPost($db, $prefixPost, $idCategory, $idPostDel);
		
	
  	//---------------------------------------------------------
  	// RESPONSE
   	//---------------------------------------------------------

		$response = [
			'error' => false,
			'status' => 1,
			'id' => $idPostDel
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;