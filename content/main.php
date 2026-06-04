<!doctype html>
<html lang="<?= $langHtml; ?>">
<head>
	<link rel="apple-touch-icon" href="<?= $defaultLogoSquare; ?>" />
	<link rel="shortcut icon" href="<?= $siteHost; ?>/favicon.ico" />
	<title><?= stripslashes($titlePage); ?></title>
	<meta charset="UTF-8" />
	<meta name="description" content="<?= $descriptionMeta; ?>" />
	<meta name="author" content="" />
	<meta name="identifier-URL" content="<?= $siteHost; ?>" />
	<meta name="copyright" content="&copy; <?= $siteName; ?>" />
	<meta name="abstract" content="<?= $descriptionMeta; ?>" />
	<meta name="robots" content="index,follow,all" />
	<meta name="rating" content="general" />
	<meta name="author" content="<?= $siteName; ?> <?= (isset($is->name)) ? $is->name : ''; ?>" />
	<meta name="publisher" content="<?= $siteName; ?>" />
	<meta name="revisit-after" content="7 days" />
	<meta name="googlebot" content="noodp" />
	<meta name="expires" content="never" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta property="og:locale" content="<?= $langHtml; ?>" />
	<meta property="og:type" content="website" />
	<meta property="og:title" content="<?= stripslashes($titlePage); ?>" />
	<meta property="og:description" content="<?= $descriptionMeta; ?>" />
	<meta property="og:url" content="<?= $urlSSL; ?>" />
	<meta property="og:site_name" content="<?= $siteName; ?> <?= (isset($is->name)) ? $is->name : ''; ?>>" />
	<meta property="og:image" content="<?= $imgThumbs; ?>" />
	<meta name="twitter:card" content="summary" />
	<meta name="twitter:description" content="<?= $descriptionMeta; ?>" />
	<meta name="twitter:title" content="<?= stripslashes($titlePage); ?>" />
	<meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
	<link rel="canonical" href="<?= $urlSSL; ?>" />
	<link rel="privacy-policy" href="<?= $siteHost.((!empty($is->varLink))? '/'. $is->varLink : '').'/'.((isset($is->privacyPage)) ? $is->privacyPage : 0).'-'.$translations['PRIVACYPAGEURL'] ?>.html">
	
	<!-- CSS -->
	<link href="/css/index,header,footer,menu,fonts,/jsp/fancybox/fancybox,/jsp/cookie-banner/cookie-banner.css" rel="stylesheet" type="text/css" />
	<!-- CSS -->
	<?php if ($colorTxtPage): ?>
	<style>
		:root {
			--color-adminColor: <?= $colorCustomAdmin; ?>;
			--color-adminTextColor: <?= $colorCustomTextAdmin; ?>;
			--color-frontTxtColor: <?= $colorTxtPage; ?>;
			--color-frontColor: <?= $colorPage; ?>;
		}
	</style><?php else: ?><?php endif; ?>
	<!-- /CSS -->

	<?php include('content/header/type.php'); ?>

</head>
<body>
	<?php if ($idRevision != 0): ?>
		<div style="text-align: center;padding: 20px;background-color: #D90101; color:#FFFFFF;font-family: 'AvenirNextLTPro-Demi';">Version du <?php echo date("d.m.Y", strtotime($ir->date)); ?> &agrave; <?php echo date("H\hi", strtotime($ir->date)); ?></div>
	<?php endif; ?>
	<?php include('content/interface/logoCollapse.php'); ?>
	<?php include('content/interface/logo.php'); ?>
	<!-- Header -->
	<header>
		<div id="header"><?php include($_SERVER['DOCUMENT_ROOT'].'/content/header/index.php'); ?></div>
	</header>
	<!-- /Header -->
	<!-- Main -->
	<main>
		<div><?php include($_SERVER['DOCUMENT_ROOT'] . '/content/pages/type.php'); ?></div>
	</main>
	<!-- /Main -->
	<!-- Footer -->
	<footer id="footer">
		<?php include($_SERVER['DOCUMENT_ROOT'] . '/content/footer/index.php'); ?>
	</footer>
	<!-- /Footer -->

	<!-- JS -->
	<script type="text/javascript">
		const ajaxPath="<?= AJAXPATH; ?>";
	</script>
	<!-- /JS -->
	<script async src="/jsp/cookie-banner/cookie-banner.js"></script>
	<script type="text/javascript" src="/js/class/ajax.service.class.js"></script>
	<script type="text/javascript" src="/js/admin/action/process-common.js"></script>
	<script type="text/javascript" src="/js/footer/process-footer.js"></script>
	<script type="text/javascript" src="/js/header/process-header.js"></script>
	<script type="text/javascript" src="/jsp/fancybox/fancybox.umd.js"></script>

	<?php include('content/footer/type.php'); ?>

	<!-- Cookie Banner -->
	<div class="nk-cookie-banner alert alert-warning text-center" role="alert">
		&#x267B; Nous respectons votre vie priv&eacute;e et vos donn&eacute;es personnelles.
		<a href="<?= $siteHost.((!empty($is->varLink))? '/'. $is->varLink : '').'/'.((isset($is->privacyPage)) ? $is->privacyPage : 0).'-'.$translations['PRIVACYPAGEURL'] ?>.html" rel="privacy-policy" target="blank">En savoir plus sur notre politique de confidentialité</a>
		<button type="button" class="btn-primary" onclick="window.nk_hideCookieBanner()">Accepter et fermer</button>
	</div>
	<!-- End of Cookie Banner -->
</body>
</html>