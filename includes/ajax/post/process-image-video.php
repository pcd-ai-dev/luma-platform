<?php declare(strict_types=1);

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */


    //---------------------------------------------------------  
   	// INIT NAME SPACES
   	//---------------------------------------------------------

		  use Tools\ImgManager;
		  use Tools\SecureManager;
 
 
   //---------------------------------------------------------
   // CONNEXIONS
   //---------------------------------------------------------

		include($_SERVER['DOCUMENT_ROOT'].'/config.php');


   //---------------------------------------------------------  
   // INIT CLASS
   //---------------------------------------------------------

    	$secure = new SecureManager(requirePost: true, requireCsrf: true);
  		$img= new ImgManager($db);

		
	//---------------------------------------------------------  
	// VARIABLES
	//---------------------------------------------------------

	// 5 minutes execution time
		@set_time_limit(5 * 60);
			
		$file    = (isset($_FILES['file']))? $_FILES['file'] : '';
		$name    = $_REQUEST["name"];
		$size    = (isset($_REQUEST['size']))? $_REQUEST['size'] : '';
		
		$idCategory=intval($_GET['idCategory']);
		$idImg=intval($_GET['idImg']);
		$targetDirCategory=$_SERVER['DOCUMENT_ROOT'].'/img/post/'.$idCategory.'/';
		$targetDirMain = $_SERVER['DOCUMENT_ROOT'].'/img/post/'.$idCategory.'/gallery/';
		$targetDir = $_SERVER['DOCUMENT_ROOT'].'/img/post/'.$idCategory.'/gallery/photos/';

		$repThumbs = $targetDirMain.'thumb/';
		$repRec = $targetDirMain.'rec/';

		
   //---------------------------------------------------------  
   // PROCESS
   //---------------------------------------------------------

		if (strlen($_REQUEST["name"])) {
			list($txt, $ext) = explode(".", $name);
			$fileName='les-alternes-'.rand(). "." .$ext;

			$valid_formats = array("jpg","jpeg","png","gif","bmp","JPG","PNG","GIF","BMP","JPEG");


			if (filesize((isset($_REQUEST['tmp_name']))? $_REQUEST['tmp_name'] : '') > 10000000) {
				die('{"error":true, "message": "Image trop grande"}');
			}

			if (in_array($ext, $valid_formats)) {


				$tmp = $_FILES['file']['tmp_name'];
				if (move_uploaded_file($tmp, $targetDir.$fileName)) {

					// Update Photo
					$reqimg = "UPDATE ".$prefixPost."img SET url=:photo WHERE id=:idImg";
					$resimg = $db->prepare($reqimg);
					$resimg->bindValue(':photo', $fileName, PDO::PARAM_STR);
					$resimg->bindValue(':idImg', $idImg, PDO::PARAM_STR);
					$resimg->execute();
					$resimg->closeCursor();
					$resimg = NULL;

					//---------------------------------------------------------  
					// IMG Treatment
					//---------------------------------------------------------		

					$img->imgProfile($repThumbs.$fileName, $repThumbs, $fileName, 300, 300, 500, 500, "#FFF", 100);
					$img->imgRectangle($targetDir.$fileName, $repRec, $fileName, 600, 400, 600, 400, "#FFF", 100);


				   die ('{"error":false, "main":"/img/post/'.$idCategory.'/gallery/thumb/'.$fileName.'?'.rand().'"}');
				} else {
					die('{"error":true, "message": "Erreur transfert "}');
					exit;
				}

			} else {
				die('{"error":true, "message": "Erreur format:'.$ext.'"}');
				exit;
			}
		} else {

			die('{"error":true, "message": "Erreur strlen "}');
			exit;
		}