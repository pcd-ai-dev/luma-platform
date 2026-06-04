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
	// Includes
	//---------------------------------------------------------

		include (ADMINPROCESSPATH.'tree/index.php');

?>
<div id="builderMsgUpdate">&nbsp;</div>
<div id="contentContainer">
<div class="contentLeft" style="min-width:80%;">
  <div style="width:95%;padding:20px 20px 0px 20px;">
    <div class="headerformglobal">
      <table style="border:0px;" cellpadding="0" cellspacing="0" width="100%">
        <tr>
          <td nowrap><table width="100%" style="border:0px;" cellspacing="0" cellpadding="0">
              <tr>
                <td>Arborescence du site <?php if($adminData['idSite'] == "1" ? true : false){echo "";}else{echo "";}?></td>
              </tr>
            </table></td>
          <td style="text-align:right;" height="35"><a href="<?= '/manager'.$varLink.'/admin_tree/param_add/1/0/0/0/'; ?>"  class="adminSmallButton" style="color:#ffffff;">Ajoutez une page </a></td>
        </tr>
      </table>
    </div>
  </div>
  <div style="width:95%;padding:0px 20px 0px 20px;">
    <?= affCat($db, $prefixRoot, $varLink, $_SESSION['siteData']['id']); ?>
  </div>
  <div>&nbsp;</div>
  <div>&nbsp;</div>
  <div>&nbsp;</div>
  <div>&nbsp;</div>
  <div>&nbsp;</div>
  <div>&nbsp;</div>
</div>
<div class="contentRight" >
  <div>
	<div>&nbsp;</div>
    <ul id="infoAdmin">
      <li>Pour d&eacute;plier une section, cliquez sur + situ&eacute; &agrave; gauche</li>
      <li>Pour activer ou d&eacute;sactiver une page, cliquez sur la pastille situ&eacute;e &agrave; droite</li>
      <li>Pour d&eacute;placer une page dans l'arborescence, cliquez sur la page et d&eacute;placez l&agrave; &agrave; l'endroit voulu.</li>
      <li>Fa&icirc;tes <strong>attention</strong>, la suppression d'une page est d&eacute;finitive et les donn&eacute;es y attenant seront perdues.</li>
    </ul>
  </div>
</div>

 <!-- JS -->
<script>
    var ajaxPath="<?= AJAXPATH; ?>"; 
    var prefix="<?= $prefixRoot; ?>";
    var adminColor= "<?= $colorCustomAdmin; ?>";
		var adminTextColor= "<?= $colorCustomTextAdmin; ?>";
</script>
<script type="text/javascript" src="/js/admin/action/process-sortable.js"></script>
<script type="text/javascript" src="/js/admin/action/process-status.js"></script>
<script type="text/javascript" src="/js/admin/page/process-page.js"></script>
<!-- /JS -->