<?php
/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */
?>

<?php 
	$photoHeaderShow=(empty($adminData['img']))? $imgDefaultSquare : "/img/admin/square/".$adminData['img'];
	$photoSiteShow=(empty($_SESSION['siteData']['imgSquare']))? $imgDefaultSquare : "/img/root/square/".$_SESSION['siteData']['imgSquare'].'?'.rand();
	
?>

<div class="leftHeader">
	<?= '<div style="width:45px;padding:10px;"><a href="/manager'.$varLink.'/" target="_self"><img src="'.$photoSiteShow.'" width="38" height="38" alt="" style="border:0px;"/></a></div>'; ?>
	<div>&nbsp;</div>
	<div class="titleAdminHeader">ADMINISTRATION <?= (($_SESSION['siteData']['status']==1)? $_SESSION['siteData']['name'] : '');?></div>
	<div class="centerHeader"><?= ($_SESSION['adminData']['level'] == 0) ? '' : ($varTitle ?? 'Tableau de bord'); ?></div>
</div>

<div class="rightHeader">
	<?= '<div>Bonjour '.$adminData['first_name'].' '.$adminData['last_name'].'&nbsp;</div>'; ?>
	<div>&nbsp;&nbsp;&nbsp;</div>
	<div><?= '<img src="'.$photoHeaderShow.'" width="50" height="50" border="0px" class="adminImg"/>'; ?></div>
	<div><?= '<div id="sessionClose"><a href="/manager'.$varLink.'/logout/'.'">&nbsp;<i class="gg-enter"></i></a></div>';?></div>
</div>