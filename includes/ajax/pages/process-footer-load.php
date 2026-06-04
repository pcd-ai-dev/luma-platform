<?php declare(strict_types=1);

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */


    //---------------------------------------------------------
   	// INIT NAME SPACES
   	//---------------------------------------------------------

		  use Front\PageManager;
		  use Tools\SecureManager;
 
   //---------------------------------------------------------
   // CONNEXIONS
   //---------------------------------------------------------

		include($_SERVER['DOCUMENT_ROOT'].'/config.php');


	//---------------------------------------------------------
	// CLASS INIT
	//---------------------------------------------------------

    	$secure = new SecureManager(requirePost: true, requireCsrf: true);
		$page= new PageManager($db,['table_lang' => $prefixRoot.'lang']);
		
		
   //---------------------------------------------------------  
   // VARIABLES
   //--------------------------------------------------------- 

		$idSite = $secure->session('siteData.id', 'int', false) ?? 0;
		$idLang = $secure->v('int', 'idLang', false) ?? 0;

		$r = $page->infoFooter($idSite, $idLang);

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