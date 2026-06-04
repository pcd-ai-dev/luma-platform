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
		use Front\PageManager;
		use Xml\XmlManager;

		
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

		$idCategory=$secure->v('int', 'id', false) ?? 0;
		$idSite = $secure->session('siteData.id', 'int', false) ?? 0;
		
		
    //---------------------------------------------------------
	// recupération Liste categories à supprimer
	//---------------------------------------------------------

		function listDel(PDO $db, string $prefixRoot, int $idCategory): array {

			$listCat = [];

			$req = "SELECT id FROM ".$prefixRoot."category WHERE parent = :idCategory";
			$res = $db->prepare($req);
			$res->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
			$res->execute();

			$datas = $res->fetchAll(PDO::FETCH_ASSOC);
			$res->closeCursor();

			foreach ($datas as $data) {
				$listCat[] = $data['id'];
				$listCat = array_merge(
					$listCat,
					listDel($db, $prefixRoot, (int)$data['id'])
				);
			}

			return $listCat?:[];
		}

		$listCat = [];
		$listCat[] = $idCategory;
		$listCat = array_merge($listCat, listDel($db, $prefixRoot, $idCategory));


    //---------------------------------------------------------
	// FUNCTION DELETE CATEGORY
	//---------------------------------------------------------
	
		function delCategory(PDO $db, string $prefixRoot, int $idCategory): void {

				// Suppression category
					$req = "DELETE  FROM ".$prefixRoot."category WHERE id=:idCategory";
					$res = $db->prepare($req);
					$res->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
					$res->execute();
					$res->closeCursor();
					$res = NULL;
					
				// Suppression category lang 
					$reql = "DELETE  FROM ".$prefixRoot."category_lang WHERE idCategory=:idCategory";
					$resl = $db->prepare($reql);
					$resl->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
					$resl->execute();
					$resl->closeCursor();
					$resl = NULL;
					
				// Suppression page
					$reqp = "DELETE FROM ".$prefixRoot."pages WHERE idCategory=:idCategory";
					$resp = $db->prepare($reqp);
					$resp->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
					$resp->execute();
					$resp->closeCursor();
					$resp = NULL;
				
		}
	
	
    //---------------------------------------------------------
	// FUNCTION DELETE POST
	//---------------------------------------------------------

		function delPost(PDO $db, string $prefixPost, int $idCategory): void {

				// Suppression Product lang
				$reqpl = "DELETE l FROM ".$prefixPost."lang l INNER JOIN ".$prefixPost."item i ON l.idPost=i.id WHERE i.idCategory=:idCategory";
				$respl = $db->prepare($reqpl);
				$respl->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
				$respl->execute();
				$respl->closeCursor();
				$respl = NULL;

				// Suppression Product IMG lang
				$reqpl = "DELETE l FROM ".$prefixPost."img_lang l INNER JOIN ".$prefixPost."img i ON l.idImg=i.id WHERE i.idCategory=:idCategory";
				$respl = $db->prepare($reqpl);
				$respl->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
				$respl->execute();
				$respl->closeCursor();
				$respl = NULL;

				// Suppression Product IMG
				$reqi = "DELETE FROM ".$prefixPost."img WHERE idCategory = :idCategory";
				$resi = $db->prepare($reqi);
				$resi->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
				$resi->execute();
				$resi->closeCursor();
				$resi = NULL;

				// Suppression Product
				$reqp = "DELETE FROM ".$prefixPost."item WHERE idCategory = :idCategory";
				$resp = $db->prepare($reqp);
				$resp->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
				$resp->execute();
				$resp->closeCursor();
				$resp = NULL; 

		}


    //---------------------------------------------------------
	// FUNCTION DELETE PRODUCT
	//---------------------------------------------------------

		function delProd(PDO $db, string $prefixProd, int $idCategory): void {

				// Suppression Product lang
				$reqpl = "DELETE l FROM ".$prefixProd."lang l INNER JOIN ".$prefixProd."item i ON l.idProd = i.id WHERE i.idCategory = :idCategory";
				$respl = $db->prepare($reqpl);
				$respl->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
				$respl->execute();
				$respl->closeCursor();
				$respl = NULL;

				// Suppression Product IMG lang
				$reqpl = "DELETE l FROM ".$prefixProd."img_lang l INNER JOIN ".$prefixProd."img i ON l.idProd = i.idProd WHERE i.idCategory = :idCategory";
				$respl = $db->prepare($reqpl);
				$respl->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
				$respl->execute();
				$respl->closeCursor();
				$respl = NULL;

				// Suppression Product IMG
				$reqi = "DELETE FROM ".$prefixProd."img WHERE idCategory = :idCategory";
				$resi = $db->prepare($reqi);
				$resi->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
				$resi->execute();
				$resi->closeCursor();
				$resi = NULL;

				// Suppression Product
				$reqp = "DELETE FROM ".$prefixProd."item WHERE idCategory = :idCategory";
				$resp = $db->prepare($reqp);
				$resp->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
				$resp->execute();
				$resp->closeCursor();
				$resp = NULL;

		}
		
	
    //---------------------------------------------------------  
	// FUNCTION DELETE GMAP
	//---------------------------------------------------------
	
		function delContact(PDO $db, string $prefixContact, int $idCategory): void {

				// Suppression Contact
				$reqc = "DELETE FROM ".$prefixContact."item WHERE idCategory=:idCategory";
				$resc = $db->prepare($reqc);
				$resc->bindValue(':idCategory', $idCategory, PDO::PARAM_STR);
				$resc->execute();
				$resc->closeCursor();
				$resc = NULL;

				// Suppression Contact LAng
				$reql = "DELETE FROM ".$prefixContact."lang WHERE idCategory=:idCategory";
				$resl = $db->prepare($reql);
				$resl->bindValue(':idCategory', $idCategory, PDO::PARAM_STR);
				$resl->execute();
				$resl->closeCursor();
				$resl = NULL;

				// Suppression Googlemap
				$reqg = "DELETE FROM ".$prefixContact."place WHERE idCategory=:idCategory";
				$resg = $db->prepare($reqg);
				$resg->bindValue(':idCategory', $idCategory, PDO::PARAM_STR);
				$resg->execute();
				$resg->closeCursor();
				$resg = NULL;


		}
		

    //---------------------------------------------------------
	// FUNCTION DELETE MAIN
	//---------------------------------------------------------
	
		function del(PDO $db, int $idSite, string $prefixRoot, string $prefixPost, string $prefixContact, string $prefixProd, string $siteName, string $siteHost, string $imgDefaultSquare, array $listCat): int {

				//CLASS INT
				$page= new PageManager($db);
				$xml= new XmlManager($db, $idSite, $siteName, $siteHost, $imgDefaultSquare);

				foreach($listCat as $idCategory){
					delCategory($db, $prefixRoot, $idCategory);
					delPost($db, $prefixPost, $idCategory);
					delProd($db, $prefixProd, $idCategory);
					delContact($db, $prefixContact, $idCategory);
				}

				$page->recalculPosition();
				$xml->updateSitemap();


				return 1;

		}

    //---------------------------------------------------------
	// PROCESS
	//---------------------------------------------------------

		if(!empty($listCat)){$resultDel=del($db, $idSite, $prefixRoot, $prefixPost, $prefixContact, $prefixProd, $siteName, $siteHost, $imgDefaultSquare, $listCat);}
		
		
   	//---------------------------------------------------------
  	// RESPONSE
   	//---------------------------------------------------------

		if($resultDel){$status=1;}else{$status=0;}

		$response = [
			'status' => $status,
			'idCategory' => $idCategory
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;