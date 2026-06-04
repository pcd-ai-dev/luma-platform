<?php 

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */

    //---------------------------------------------------------
	// COOKIE INIT
	//---------------------------------------------------------

		ini_set('session.cookie_secure', 1);
		ini_set('session.cookie_httponly', 1);
		ini_set('session.cookie_samesite', 'Lax');


	//---------------------------------------------------------
	// SESSION START
	//---------------------------------------------------------

		session_start();


	//---------------------------------------------------------
	// SESSION START
	//---------------------------------------------------------

		if (file_exists(__DIR__ . '/config/installed.lock')) {
		}else{
			header('location: /launch.php');
			exit();
		}
		

    //---------------------------------------------------------  
	// CONNEXIONS / CONFIG
	//--------------------------------------------------------- 

		require($_SERVER['DOCUMENT_ROOT'].'/config.php');


	//---------------------------------------------------------
	// INIT NAME SPACES
	//---------------------------------------------------------

		use Session\SessionManager;
		use Tools\SecureManager;
		use Front\StringManager;
		use Front\RootManager;
		use Front\PageManager;
		use Front\LangManager;
		use Front\NavManager;
		use Front\PostManager;


	//---------------------------------------------------------
	// INIT CLASSES ROOT
	//---------------------------------------------------------

		$secure  = new SecureManager(requirePost: false, requireCsrf: true);
		$string  = new StringManager($db);
		$lang    = new LangManager($db);
		$session = new SessionManager($db);


	//--------------------------------------------------------
	// LANGUAGE
	//--------------------------------------------------------

		$tabLang = $lang->activeListLang();

		$isolang = $secure->session('isolang', 'string', false)  ?? htmlspecialchars($tabLang[0]['code'] ?? 0, ENT_QUOTES, 'UTF-8');

		if (isset($isolang)) {
			foreach ($tabLang as $value) {
				if ($value['code'] == $isolang) {
					include $_SERVER['DOCUMENT_ROOT'].'/includes/lang/'.$value['code'].'.php';
					$langName = $value['name'];
					$idLang   = $value['id'];
					$langHtml = $value['code'].'_'.strtoupper($value['code']);
					setlocale(LC_TIME, $langHtml);
				} else {
					if (count($tabLang) == 1) {
						include $_SERVER['DOCUMENT_ROOT'].'/includes/lang/'.$value['code'].'.php';
						$langName = $value['name'];
						$idLang   = $value['id'];
						$langHtml = $value['code'].'_'.strtoupper($value['code']);
						setlocale(LC_TIME, $langHtml);
					}
				}
			}
		} else {
			include $_SERVER['DOCUMENT_ROOT'].'/includes/lang/fr.php';
			$langName = 'Fran&ccdil;ais';
			$idLang   = 1;
			$langHtml = 'fr_FR';
			setlocale(LC_TIME, $langHtml);
		}

		$idLang=(isset($idLang))? (int)$idLang : 0;

		$_SESSION['langue'] = $idLang;


	//---------------------------------------------------------
	// INIT CLASSES ROOT
	//---------------------------------------------------------

		$root     = new RootManager($db);
		$page     = new PageManager($db);
		if(!empty($translations)){
			$post     = new PostManager($db, $siteName, $siteHost, $imgDefaultSquare, $translations);
		}
		$nav      = new NavManager($db);


	//---------------------------------------------------------  
	// VARS
	//--------------------------------------------------------- 

		$idRevision    = 0;
		$contactiddel  = 0;
		$unsubscribeId = 0;
		$item          = "";


	//---------------------------------------------------------  
	// INIT SITE
	//---------------------------------------------------------

		$varLinkData = $secure->v('slug', 'varLink', false);
		$idSiteCheck = $secure->v('int', 'idSite', false);
		

	//---------------------------------------------------------  
	// INT
	//--------------------------------------------------------- 

		if (empty($varLinkData)) {
			$idSite = $secure->v('int', 'idSite', false) ?? 1;
			$is     = $root->infoSite($idSite);
			$varLinkData = $is->varLink;
			$idHome = $is->homePage;
			$introStatus = 0;
		} else {
			$idSiteCheck = $session->getIdSite($varLinkData);
			if (isset($idSiteCheck)) {
				$idSite = $idSiteCheck["id"];
				$is     = $root->infoSite($idSite);
				$idHome = $is->homePage;
			} else {
				$idSite = 1;
				$is     = $root->infoSite($idSite);
				$idHome = $is->homePage;
			}
			$introStatus = 0;
		}


	//---------------------------------------------------------  
	// INIT DEFAULT COLOR
	//--------------------------------------------------------- 

		$colorCustomAdmin     = isset($is->adminColor) ? $is->adminColor : '#81b929';
		$colorCustomTextAdmin = isset($is->adminTextColor) ? $is->adminTextColor : '#81b929';


	//---------------------------------------------------------
	// INIT DEFAULT LOGO
	//---------------------------------------------------------

		$defaultLogoSquare = isset($is->imgSquare)? $siteHost.'/img/root/square/'.$is->imgSquare : $imgDefaultSquare;
		$defaultLogoRec = isset($is->img)? $siteHost.'/img/root/rec/'.$is->img : $imgDefaultRec;


	//---------------------------------------------------------
	// VARIABLES
	//---------------------------------------------------------

		$itemCheck = $secure->v('slug', 'item', false) ?? '';

		if (isset($itemCheck)) {

			$item          = $secure->v('slug', 'item', false) ?? '';
			$idCategory    = $secure->v('int', 'idCategory', false) ?? $idHome;
			$parentId      = $secure->v('int', 'parentId', false) ?? 0;
			$idVideo       = $secure->v('int', 'idVideo', false) ?? 0;
			$idNews        = $secure->v('int', 'idNews', false) ?? 0;
			$unsubscribeId = $secure->v('int', 'unsubscribeId', false) ?? 0;
			$idRevision    = $secure->v('int', 'idRevision', false) ?? 0;


		//---------------------------------------------------------
		// INFO PAGE
		//---------------------------------------------------------

			$r = $page->infoPage($idCategory, $idLang ?? 0);


		//---------------------------------------------------------
		// REVISIONS
		//---------------------------------------------------------

			if ($idRevision != 0) {

				$idLang = $secure->v('int', 'idLang', false) ?? 0;
				$ir     = $page->infoRevision($idRevision, $idLang);

				$idCategory = $ir->idCategory;
				$r          = $page->infoPage($idCategory, $idLang);

				foreach ($tabLang as $value) {
					if ($idLang == $value['id']) {
						$langName = $value['name'];
						$idLang   = $value['id'];
						setlocale(LC_TIME, $value['code'].'_'.strtoupper($value['code']));
					}
				}
			}


		} else {

			$idCategory = $idHome;
			$parentId   = '0';
			$r = $page->infoPage($idCategory, $idLang);
			$idCategory = isset($r->category) ? (int)$r->category : 0;
			$checkIntro = 'intro';
		}


	//---------------------------------------------------------  
	// PAGE INFO
	//---------------------------------------------------------

		$c = $page->infoCat($idCategory, $idLang ?? 0);
		$typepage = isset($c->type) ? (int)$c->type : 0;
		$switchstatus = isset($c->switch) ? (int)$c->switch : 0;
		$photoHeader = isset($c->img) ? $c->img : '';
		$checkParent=(isset($c->parent))? (int)$c->parent : 0;


	//---------------------------------------------------------  
	// META
	//---------------------------------------------------------

		$titlePage = isset($r->title) ? $r->title : '';
		$descriptionMeta = isset($r->description) ? $r->description : '';
		$keywordsMeta = isset($r->keywords) ? $r->keywords : '';


	//---------------------------------------------------------  
	// META DAT
	//---------------------------------------------------------

			if  ($item == "post") {

				$rp = $post->infoPost($idNews);
				$rpost = $post->postLang($idNews, $idLang);

				$imgHeader = "/img/post/".$rp->idCategory."/img/rec/".(isset($rp->img) ? $rp->img : '');

				if (is_numeric($rp->video)) {
					$videoLinkcolorBox= "https://player.vimeo.com/video/".(isset($rp->video) ? $rp->video : '');
				} else {
					$videoLinkcolorBox= "https://www.youtube.com/embed/".(isset($rp->video) ? $rp->video : '');
				}

				$titlePage      = $siteName." | ".(isset($rpost->title) ? $rpost->title : '');
				$descriptionNews= strip_tags(isset($rpost->text) ? $rpost->text : '');

				$wordLimit  = 25;
				$teaserText = '';
				$words      = explode(' ', $descriptionNews);

				$i = 0;
				while ($i < $wordLimit) {
					$i++;
					$teaserText .= (isset($words[$i]) ? $words[$i] : '')." ";
				}

				$descriptionMeta = strip_tags($teaserText);
				$descriptionMeta = str_replace("&#39;", "'", $descriptionMeta);

			} else {
				$titlePage = isset($r->title) ? $r->title : '';
				$descriptionMeta = isset($r->description) ? $r->description : '';
				$keywordsMeta = isset($r->keywords) ? $r->keywords : '';
				$imgHeader = $defaultLogoSquare;
			}
		


		//---------------------------------------------------------  
		// IMG BG
		//--------------------------------------------------------- 

		if ($item == "post") {
			if ($rp->img == "") {
				$urlImg = "/img/interface/diaporama/default_".$idLang.".jpg";
				$imgThumbs = $defaultLogoSquare;
			} else {
				$urlImg = "/img/post/".$rp->idCategory."/img/rec/".$rp->img;
				$imgThumbs = "/img/post/".$rp->idCategory."/img/square/".$rp->img;
			}

			$colorPage = empty($c->color) ? "#FFFFFF" : $c->color;
			$colorTxtPage = empty($c->colorTxt) ? "#FFFFFF" : $c->colorTxt;

		} else {

			if ($photoHeader) {
				$urlImg = "/img/pages/".$idCategory."/header/rec/".$photoHeader;
				$urlVideo = $c->paramPage;
			} else {
				$urlImg = "/img/interface/diaporama/default_".($idLang ?? 0).".jpg";
				$urlVideo = "";
			}

			$colorPage = empty($c->color) ? "#FFFFFF" : $c->color;
			$colorTxtPage = empty($c->colortxt) ? "#FFFFFF" : $c->colortxt;

			$imgThumbs = $defaultLogoSquare;
		}


	//---------------------------------------------------------  
	// CONTENT
	//--------------------------------------------------------- 

		include('content/main.php');

?>