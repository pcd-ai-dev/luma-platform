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
 

   //---------------------------------------------------------
   // CONNEXIONS
   //---------------------------------------------------------

		include($_SERVER['DOCUMENT_ROOT'].'/config.php');


   //---------------------------------------------------------
   // INIT CLASS
   //---------------------------------------------------------

		$secure = new SecureManager(requirePost: true, requireCsrf: true);
		$page= new PageManager($db);
		
   //---------------------------------------------------------  
   // VARIABLES
   //---------------------------------------------------------

		$idCategory = $secure->v('int', 'idCategory', false) ?? 0;
		$idLang = $secure->v('int', 'idLang', false) ?? 0;

		$r = $page->infoPage($idCategory, $idLang);

		$htmlData = $r->text ?? '';
		$cssData = $r->css ?? '';
		$assetsData = $r->assets ?? '';
		$componentsData = $r->components ?? [];
		$stylesData = $r->styles ?? [];

   	//---------------------------------------------------------
  	// RESPONSE
   	//---------------------------------------------------------

		$response = [
			'html' => $htmlData,
			'css' => $cssData,
			'components' => $componentsData,
			'styles' => $stylesData
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;