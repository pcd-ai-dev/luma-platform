<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */


	//---------------------------------------------------------  
	// FILE SECURE
	//---------------------------------------------------------  

		if (!$session->getAdminData()) {
			header('HTTP/1.1 403 Forbidden');
			exit('Accès interdit');
		}


    //---------------------------------------------------------  
	// Variables
	//--------------------------------------------------------- 

		if(empty($idSite)){
			$mainFolder='';
			$baseName='';
			$baseNameSql='';
			$imgLogo='';
		}else{
			$is=$root->infoSite($idSite);
			$mainFolder=(isset($is->varLink))? $baseNameHost.$is->varLink : '';
			$baseName=(isset($is->name))? $is->name : '';
			$baseNameSql=(isset($is->varLink))? $is->varLink : '';
		}


    //---------------------------------------------------------  
	// Total ADMIN
	//--------------------------------------------------------- 

		$reqRes = "SELECT * FROM ".$prefixAdmin."user WHERE idSite=:idSite ORDER BY position";	
		$resRes = $db->prepare($reqRes);
		$resRes->bindValue(':idSite', $_SESSION['siteData']['id'], PDO::PARAM_STR);
		$resRes->execute();
		$totalAdmin=$resRes->rowCount();
		$tabom = $resRes->fetchAll();
		$resRes->closeCursor();
		$resRes = NULL;


	//---------------------------------------------------------
	// NOTIFICATIONS
	//---------------------------------------------------------

		$displayNotify      = '';
		$displayNotifyValid = '';


    //---------------------------------------------------------  
	// STATUS MENU
	//---------------------------------------------------------

		//SuperAdmin=>99
		//Administrateur=>0

		// INIT cat_XX
		for ($i = 0; $i <= 24; $i++) {
			${"cat_" . str_pad($i, 2, '0', STR_PAD_LEFT)} = '';
		}

		// Mapping Menu
		$menuMap = [
			'admin_root' => ['cat' => 'cat_00', 'title'=>'Gestion de sites'],
			'admin_tree' => ['cat' => 'cat_01', 'title'=>'Gestion éditoriale'],
			'admin_footer' => ['cat' => 'cat_02', 'title'=>'Gestion du pied de page'],
			'admin_header' => ['cat' => 'cat_03', 'title'=>'Gestion de l\'entête'],
			'admin_owner' => ['cat' => 'cat_04', 'title'=>'Gestion des ressources'],
		];

		if (isset($menuMap[$item]['cat'])) {
			$varName = $menuMap[$item]['cat'];
			${$varName} = 'class="current"';
			$varTitle = $menuMap[$item]['title'];
		}


	//---------------------------------------------------------  
	// Structure menu latéral
	//---------------------------------------------------------

			$menuStructure = [
				'00' => [
					'label' => ['ROOT', 'extra' => '', 'icon' => 'setting.svg'],
					'items' => [
						['key' => 'admin_root', 'label' => 'Gestion des Sites', 'count' => '', 'icon' => '', 'cat' => $cat_00, 'level' => [0,99]],
					],
				],
				'01' => [
					'label' => ['SITE '.(($_SESSION['siteData']['status']==1)? $_SESSION['siteData']['name'] : ''), 'extra' => '', 'icon' => 'content.svg'],
					'items' => [
						['key' => 'admin_tree', 'label' => 'Contenu', 'count' => '', 'icon' => '<i class="gg-user-remove"></i>', 'cat' => $cat_01, 'level' => [99,0]],
						['key' => 'admin_footer', 'label' => 'Pied de page', 'count' => '', 'icon' => '', 'cat' => $cat_02, 'level' => [99,0]],
					],
				],
				'02' => [
					'label' => ['ACCÈS ' .(($_SESSION['siteData']['status']==1)? $_SESSION['siteData']['name'] : ''), 'extra' => '', 'icon' => 'access.svg'],
					'items' => [
						['key' => 'admin_owner', 'label' => 'Accès Admin', 'count' => $totalAdmin, 'icon' => '', 'cat' => $cat_04, 'level' => [99,0]],
					],
				]
			];


	//---------------------------------------------------------  
	// Structure menu Accueil
	//---------------------------------------------------------

		$menuStructureSummary = [
			'01' => [
				'label' => ['SITE ' . (($_SESSION['siteData']['status']==1)? $_SESSION['siteData']['name'] : ''), 'icon' => 'tools.svg'],
				'items' => [
					['key' => 'admin_tree', 'label' => 'Contenu', 'icon' => 'content.svg', 'level' => [99,0]],
					['key' => 'admin_footer', 'label' => 'Pied de page', 'icon' => 'footer.svg', 'level' => [99,0]],
					['key' => 'admin_owner', 'label' => 'Accès Admin', 'icon' => 'acces.svg', 'level' => [99,0]],
				],
			]
		];

