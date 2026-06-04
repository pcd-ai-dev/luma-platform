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
	// Connexion
	//---------------------------------------------------------

		include (ADMINPROCESSPATH.'root/index.php');

?>

<!-- CSS File -->
<link href="/jsp/coloris/coloris.min.css" rel="stylesheet" type="text/css" />
<!-- CSS File End -->


<div style="text-align:left;">
	<!-- Btn Fermeture -->
	<div style="text-align:right;" id="close"><a href="/manager<?= $varLink; ?>/admin_root.html" ><img src="/img/interface/btn/BtnClosePage.svg" alt="Fermer" name="Fermer" width="24" height="24" style="border:0px;" id="Fermer" /></a></div>
	<!-- Btn Fermeture -->

	<!-- Update MSG -->
	<div id="builderMsgUpdate">&nbsp;</div>
	<!-- /Update MSG -->

	<!-- Tabs -->
		<ul class="tabs" id="tabsholder" data-initial-tab="<?= isset($tabItem) ? (int)$tabItem : 1 ?>">
			<li data-tab="tab1"><?= (isset($rinfo->name))? $rinfo->name : 'Sons nom';?></li>
			<li data-tab="tab2">Langues</li>
			<li data-tab="tab3">Images</li>
		</ul>

		<div id="contentContainer">
			<div class="contentLeft" style="min-width:80%;">
				<div class="contents marginbot"> 
					<div id="content1" class="tabscontent" style="margin:20px; ">
						<?= (isset($msgUpdate))? $msgUpdate : ''; ?>
						<?php include 'main.php'; ?>
					</div>
					<div id="content2" class="tabscontent" style="margin:20px; ">
						<?= (isset($msgUpdate))? $msgUpdate : ''; ?>
						<?php include 'language.php'; ?>
					</div>
					<div id="content3" class="tabscontent" style="margin:20px; ">
						<?= (isset($msgUpdate))? $msgUpdate : ''; ?>
						<?php include 'images.php'; ?>
					</div>
				</div>
			</div>
			<div class="contentRight">
			</div>
		</div>
  	<!-- /Tabs -->
</div>

<!-- JS File -->
<script type="text/javascript">
	var ajaxPath="<?= AJAXPATH; ?>";
	var adminColor= "<?= $colorCustomAdmin; ?>";
	var adminTextColor= "<?= $colorCustomTextAdmin; ?>";
    var prefix="<?= $prefixRoot; ?>";
    var entitySite="root";
	var entityLang="lang";
    var entitySiteUId="<?= $siteUId; ?>";
    var idSite=<?= $idSite; ?>;
	var modId=<?= $modId; ?>;
	<?= (($modId!="0")&&($modStatus=="0"))? "var modStatus=1;" : "var modStatus=0;"; ?>
</script>
<script type="text/javascript" src="/jsp/coloris/coloris.min.js"></script>
<script type="text/javascript" src="/js/admin/action/process-tabs.js"></script>
<script type="text/javascript" src="/js/admin/action/process-sortable.js"></script>
<script type="text/javascript" src="/js/admin/action/process-status.js"></script>
<script type="text/javascript" src="/js/admin/action/process-file-add.js"></script>
<script type="text/javascript" src="/js/admin/root/process-root-mod.js"></script>
<!-- JS FileEnd  -->