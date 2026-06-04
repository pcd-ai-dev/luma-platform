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
   // INIT CLASS SECURE
   //---------------------------------------------------------

		$secure = new SecureManager(requirePost: true, requireCsrf: false);
 

   //---------------------------------------------------------  
   // VARIABLES
   //---------------------------------------------------------

		$pk = $secure->v('string', 'pk', false);
		$pkArray=json_decode($pk);

		$idImg = isset($pkArray->id) ? (int)$pkArray->id : 0;
		$idLang = isset($pkArray->idLang) ? (int)$pkArray->idLang : 1;
		$idCategory = isset($pkArray->idCategory) ? (int)$pkArray->idCategory : 0;
		$tableLang = isset($pkArray->table) ? $pkArray->table : '';
		$field = isset($pkArray->field) ? $pkArray->field : '';
		$idField = isset($pkArray->idField) ? $pkArray->idField : 0;
		$video = isset($pkArray->video) ? $pkArray->video : 0;

		$value=$secure->v('string', 'value', false) ?? '';

   
   //---------------------------------------------------------  
   // CHECK ID IINPUT
   //---------------------------------------------------------
			
		if(empty($video)){
			$reql = "SELECT * FROM ".$tableLang." WHERE idImg=:idImg AND idLang=:idLang";			 
			$resl = $db->prepare($reql);
			$resl->bindValue(':idImg', $idImg, PDO::PARAM_STR);
			$resl->bindValue(':idLang', $idLang, PDO::PARAM_STR);
			$resl->execute();
			$nb=$resl->rowCount();
			$resl->closeCursor();
			$resl = NULL;

			if($nb==0){

				if($field=="idCategory"){
					$reqni = "INSERT INTO ".$tableLang." (idImg,idLang,description,idCategory) VALUES(:idImg, :idLang, :description, :idCategory)";
					$resni = $db->prepare($reqni);
				}else{
					$reqni = "INSERT INTO ".$tableLang." (idImg,idLang,description,idCategory,".$field.") VALUES(:idImg, :idLang, :description, :idCategory, :idField)";
					$resni = $db->prepare($reqni);
					$resni->bindValue(':idField', $idField, PDO::PARAM_STR);
				}
				$resni->bindValue(':idImg', $idImg, PDO::PARAM_STR);
				$resni->bindValue(':idLang', $idLang, PDO::PARAM_STR);
				$resni->bindValue(':description', $value, PDO::PARAM_STR);
				$resni->bindValue(':idCategory', $idCategory, PDO::PARAM_STR);
				
				$resni->execute();
				$resni->closeCursor();
				$resni = NULL;

			}else{

				if(empty($value)){
					$req = "DELETE  FROM ".$tableLang." WHERE idImg=:idImg AND idLang=:idLang";
					$res = $db->prepare($req);
					$res->bindValue(':idImg', $idImg, PDO::PARAM_STR);
					$res->bindValue(':idLang', $idLang, PDO::PARAM_STR);
					$res->execute();
					$res->closeCursor();
					$res = NULL;
				}else{
					$req = "UPDATE ".$tableLang." SET description=:value WHERE idImg=:idImg AND idLang=:idLang";
					$res = $db->prepare($req);
					$res->bindValue(':idImg', $idImg, PDO::PARAM_STR);
					$res->bindValue(':idLang', $idLang, PDO::PARAM_STR);
					$res->bindValue(':value', $value, PDO::PARAM_STR);
					$res->execute();
					$res->closeCursor();
					$res = NULL;
				}
			}
		}else{
			$req = "UPDATE ".$tableLang." SET video=:value WHERE id=:idImg";
			$res = $db->prepare($req);
			$res->bindValue(':idImg', $idImg, PDO::PARAM_STR);
			$res->bindValue(':value', $value, PDO::PARAM_STR);
			$res->execute();
			$res->closeCursor();
			$res = NULL;
		}
   

	//---------------------------------------------------------
  	// RESPONSE
   	//---------------------------------------------------------

		$response = [
			'error' => false,
			'message' => 'Info sauvegardé'
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;