<?php 

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */


    //---------------------------------------------------------  
	// CONNEXIONS / CONFIG
	//--------------------------------------------------------- 

		require($_SERVER['DOCUMENT_ROOT'].'/config.php');


    //---------------------------------------------------------
	// INIT NAMES SPACE
	//---------------------------------------------------------

		use Session\SessionManager;
		use Tools\SecureManager;
		use Front\RootManager;
		use Front\PageManager;
		use Front\LangManager;
		use Front\StringManager;
		

	//---------------------------------------------------------  
	// INIT CLASSES
	//---------------------------------------------------------

		$session = new SessionManager($db,[
			'table_user'    => $prefixAdmin.'user',
			'table_session' => $prefixAdmin.'session',
			'table_place' => $prefixAdmin.'place',
			'table_site' => $prefixRoot.'site',
			'inactivity_timeout' => 325500,
			'cookie_secure' => true
			]
		);

		$root= new RootManager($db);
		$page= new PageManager($db);
		$lang= new LangManager($db);
		$string= new StringManager($db,['table_tags' => $prefixParam.'tags']);
		$secure = new SecureManager(requirePost: false);
		

    //---------------------------------------------------------
	// SESSION START
	//---------------------------------------------------------

		$session->start();
		
		$adminData = $session->getAdminData();

		if (!$adminData) {
			
			$email = $secure->v('email', 'email', false) ?? '';
			$password = $secure->v('string', 'password', false) ?? '';
			$idSite = $secure->v('int', 'idSite', false) ?? 1;

			$userId = $session->getLoginCheckAdmin(trim($email), $password, $idSite);

			if ($userId) {
				$session->dbCleanAdmin();
				$session->openSessionAdmin($userId);

				$adminData = $session->getAdminData();
				$_SESSION['adminData'] = $adminData;

				$siteData = $session->getSiteData($adminData['idSite']);
				$_SESSION['siteData'] = $siteData;
			}
		}
		

	//---------------------------------------------------------  
	// VARIABLES
	//--------------------------------------------------------- 

		$item = $secure->v('slug', 'item', false) ?? 'log';
		$item = $item === '' ? 'log' : $item;

		$varLinkData = $secure->v('slug', 'varLink', false) ?? '';
		$varLinkData = $varLinkData === '' ? '' : $varLinkData;

		// Vérification session admin
		if (!empty($_SESSION['adminData']) && !empty($_SESSION['siteData'])) {

			$varLink = $secure->session('siteData.varLink', 'string', false) ?? '';
			$colorCustomAdmin = $secure->session('siteData.adminColor', 'string', false) ?? '#81b929';
			$colorCustomTextAdmin = $secure->session('siteData.adminTextColor', 'string', false) ?? '#81b929';

			$adminLevel = $secure->session('adminData.level', 'int', false) ?? null;
			$bgStatus = $adminLevel === 0 ? 'class="neutral"' : 'class="adminPanel"';
			$cssBodyBG = '';
			$idSite = $secure->session('siteData.id', 'int', false) ?? 1;
			$imgLogoTarget = '';
			$imgLogoTargetRec =  '';

		} else {

			$bgStatus = 'class="adminLog"';

			$idSiteCheck = $session->getIdSite($varLinkData);
			$varLink = $idSiteCheck['varLink'] ?? '';
			$session->closeSessionAdmin($varLink);

			$idSite = $idSiteCheck['id'] ?? 1;
			$cssBodyBG = !empty($idSiteCheck['bgColor']) ? 'style="background-color:'.$idSiteCheck['bgColor'].';"' : 'style="'.$bgColorDefault.'"';
			$imgLogoTargetRec = $idSiteCheck['img'] ?? '';
			$imgLogoTarget = $idSiteCheck['imgSquare'] ?? '';
			$colorCustomAdmin = $idSiteCheck['adminColor'] ?? '#81b929';
			$colorCustomTextAdmin = $idSiteCheck['adminTextColor'] ?? '#81b929';
		}


	//---------------------------------------------------------  
	// MENU PROCESS
	//---------------------------------------------------------

		if(!empty($_SESSION['adminData'])){
			include(ADMINPATH.'menu/index.php');
		}

?>
<!doctype html>
<html xmlns="https://www.w3.org/1999/xhtml" dir="ltr" lang="fr-standard" xml:lang="fr-standard">
	<head>
		<link rel="SHORTCUT ICON" href="/favicon.ico"/>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php if(isset($_SESSION['siteData'])){echo $_SESSION['siteData']['name'];}else{echo (isset($idSiteCheck["name"]))? $idSiteCheck["name"]: '';}?> ADMINISTRATION</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="identifier-URL" content="<?= $siteHost; ?>" />
		<meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
		<!-- CUSTOM CSS -->
		<style>
			:root {
				--color-adminColor: <?= $colorCustomAdmin; ?>;
				--color-adminTextColor: <?= $colorCustomTextAdmin; ?>;
			}
		</style>
		<!-- /CUSTOM CSS -->
		<!-- CSS Files -->
		<link rel="stylesheet" type="text/css" href="/css/fonts.css" />
		<link rel="stylesheet" type="text/css" href="/css/admin.css" />
		<link rel="stylesheet" type="text/css" href="/css/adminheader.css" />
		<link rel="stylesheet" type="text/css" href="/css/log.css" />
		<link rel="stylesheet" type="text/css" href="/css/form.css" />
		<link rel="stylesheet" type="text/css" href="/css/btn.css" />
		<link rel="stylesheet" type="text/css" href="/css/fonts.css" />
		<link rel="stylesheet" type="text/css"  href="/jsp/fancybox/fancybox.css"/>
		<link rel="stylesheet" type="text/css"  href="/jsp/sweetalert/sweetalert2.css"/>
		<!-- CSS Files end -->

		<!-- JS Files -->
		<script type="text/javascript" src="/plugins/ckeditor/ckeditor.js"></script>
		<script type="text/javascript" src="/jsp/fancybox/fancybox.umd.js"></script>
		<script type="text/javascript" src="/jsp/sweetalert/sweetalert2.all.js"></script>
		<script type="text/javascript" src="/js/class/validator.class.js"></script>
		<script type="text/javascript" src="/js/class/ajax.service.class.js"></script>
		<script type="text/javascript" src="/js/admin/action/process-common.js"></script>
		<!-- /JS Files -->

		<?php $schedulerLoad = (($item=="admin_scheduler")||($item=="admin_learn"))? 'onload="init();"' : ''; ?>
	
	</head>
	<body <?= $bgStatus; ?> <?= $cssBodyBG; ?> <?= $schedulerLoad; ?>>
		<!-- Content -->
		<?php 
			switch($item){
				case 'forgot': include (MANAGERPATH.'config/forgot.php');break;
				case 'log': include (MANAGERPATH.'config/log.php');break;
				default:include(MANAGERPATH.'config/log.php');
			}
		?>
		<!-- Content -->
	</body>
</html>