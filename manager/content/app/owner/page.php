<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */


	//---------------------------------------------------------  
	// INIT
	//---------------------------------------------------------

		use App\AdminManager;

	//---------------------------------------------------------  
	// FILE SECURE
	//---------------------------------------------------------  

		if (!$session->getAdminData()) {
			header('HTTP/1.1 403 Forbidden');
			exit('Accès interdit');
		}

	//---------------------------------------------------------  
	// INIT CLASS
	//---------------------------------------------------------

		$admin= new AdminManager($db);
	  
	  
	//---------------------------------------------------------  
	// CONNEXION
	//---------------------------------------------------------

		include (ADMINPROCESSPATH.'owner/index.php');


	//---------------------------------------------------------  
	// VARIABLES
	//---------------------------------------------------------

		$rinfo=$admin->getAdminInfo($adminId);

?>


<!-- CSS File -->
<link rel="stylesheet" href="/plugins/scheduler/css/stylesCDS.css" />
<!-- CSS File End -->

<div style="text-align:left;">
	<!-- Btn Fermeture -->
	<div style="text-align:right;" id="close"><a href="/manager<?= $varLink; ?>/admin_owner.html" ><img src="/img/interface/btn/BtnClosePage.svg" alt="Fermer" name="Fermer" width="24" height="24" style="border:0px;" id="Fermer" /></a></div>
	<!-- Btn Fermeture -->

	<!-- Update MSG -->
	<div id="builderMsgUpdate">&nbsp;</div>
	<!-- /Update MSG -->

	<!-- Tabs -->
	<div>
		<ul class="tabs" id="tabsholder" data-initial-tab="<?= isset($tabItem) ? (int)$tabItem : 1 ?>">
			<li data-tab="tab1"><?= $rinfo->first_name.' '.$rinfo->last_name;?></li>
		</ul>

		<div id="contentContainerTabs">
			<div class="contentLeft" style="min-width:95%;">
				<div class="contents marginbot"> 
					<div id="content1" class="tabscontent" style="margin: 20px;">
						<?= (isset($msgUpdate))? $msgUpdate : ''; ?>
						<?php include 'main.php'; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
  	<!-- /Tabs -->
</div>
<!-- JS File -->
<script type="text/javascript">
	const adminId="<?= $adminId;?>";
	const ajaxPath="<?= AJAXPATH; ?>";
	const adminColor= "<?= $colorCustomAdmin; ?>";
	const adminTextColor= "<?= $colorCustomTextAdmin; ?>";
    const prefixAdmin="<?= $prefixAdmin; ?>";
	const entityAdmin="admin";
    const entityAdminUId="<?= $adminUId; ?>";
    const idSite=<?= intval($idSite); ?>;
	const modId=<?= $modId; ?>;
	<?= (($modId!="0")&&($modstatus=="0"))? "const modStatus=1;" : "const modStatus=0;"; ?>
</script>
<script type="text/javascript" src="/jsp/inPlaceEditing/xeditable.js"></script>
<script type="text/javascript" src="/js/admin/action/process-tabs.js"></script>
<script type="text/javascript" src="/js/admin/action/process-file-add.js"></script>
<script type="text/javascript" src="/js/admin/owner/process-owner-mod.js"></script>
<!-- JS FileEnd  -->