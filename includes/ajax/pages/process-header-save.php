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
   // INIT CLASS
   //---------------------------------------------------------

		$secure = new SecureManager(requirePost: true, requireCsrf: true);


	//---------------------------------------------------------
   	// VARIABLES
  	//---------------------------------------------------------

		$idLang = $secure->v('int', 'idLang', false) ?? 0;
		$idHeader = $secure->session('siteData.id', 'int', false) ?? 0;
		$assets = $secure->v('array', 'assets', false) ? json_encode($secure->v('array', 'assets', false)) : '';
		$components = $secure->v('array', 'components', false) ? json_encode($secure->v('array', 'components', false)) : '';
		$styles = $secure->v('array', 'styles', false) ? json_encode($secure->v('array', 'styles', false)) : '';
		$css = $secure->v('string', 'css', false) ?? '';
		$html = $secure->v('string', 'html', false) ?? '';


   //---------------------------------------------------------  
   // UPDATE
   //--------------------------------------------------------- 
   
		$reqpmodif = "UPDATE ".$prefixRoot."header SET text=:text, css=:css, components=:components, assets=:assets, styles=:styles WHERE idSite=:idHeader AND idLang=:idLang";
		$respmodif = $db->prepare($reqpmodif);
		$respmodif->bindValue(':text', $html, PDO::PARAM_STR);
		$respmodif->bindValue(':css', $css, PDO::PARAM_STR);
		$respmodif->bindValue(':components', $components, PDO::PARAM_STR);
		$respmodif->bindValue(':assets', $assets, PDO::PARAM_STR);
		$respmodif->bindValue(':styles', $styles, PDO::PARAM_STR);
		$respmodif->bindValue(':idHeader', $idHeader, PDO::PARAM_STR);
		$respmodif->bindValue(':idLang', $idLang, PDO::PARAM_STR);
		$respmodif->execute();
		$respmodif->closeCursor();
		$respmodif = NULL;
			
	
   	//---------------------------------------------------------
  	// RESPONSE
   	//---------------------------------------------------------

		$response = [
			'success' => true,
			'message' => 'Sauvegarde réussie'
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;