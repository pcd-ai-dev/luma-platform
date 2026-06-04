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


	  include(ADMINPROCESSPATH.'root/index.php' );

?>

<!-- Header -->
<!-- Btn Fermeture -->
<div style="text-align:right;" id="close"><a href="/manager<?= $varLink; ?>/admin_root.html" ><img src="/img/interface/btn/BtnClosePage.svg" alt="Fermer" name="Fermer" width="24" height="24" style="border:0px;" id="Fermer" /></a></div>
<!-- Btn Fermeture -->

<!-- Tabs -->
<div id="tabsholder">
	<ul class="tabs">
		<li id="tab1" style="background-color:#81B929;color:#FFFFFF">Ajout d'un site</li>
	</ul>
<!-- Tabs -->
</div>
<!-- /Header -->
<!-- Content -->
<div style="width:70%;padding:20px 20px 0px 20px;">
  <div>&nbsp;</div>
  <div>&nbsp;</div>
  <div>&nbsp;</div>
  <div>&nbsp;</div>
  <div>&nbsp;</div>
  <div>
    <form action="/manager<?= $varLink; ?>/admin_root.html" method="post" id="addSiteform" name="addpSiteform" enctype="multipart/form-data">
      <div class="headerform">Informations du site &nbsp;<span class="redb">*</span></div>
      <div class="corpsForm">
        <input type="text" name="name" id="name" value="" class="input-full requiredField" placeholder="Nom du site ..."/>
      </div>
      <div>&nbsp;</div>
      <div>&nbsp;</div>
      <div><button class="cubutton">Ajoutez</button></div>
      <div><input type="hidden" name="addSiteStart" id="addSiteStart" value="1" /></div>
    </form>
    <div>&nbsp;</div>
    <div>&nbsp;</div>
    <div>&nbsp;</div>
    <div>&nbsp;</div>
    <div>&nbsp;</div>
    <div>&nbsp;</div>
    <div>&nbsp;</div>
    <div>&nbsp;</div>
  </div>
</div>
<!-- /Content -->

<!-- JS File -->
<script type="text/javascript" src="/js/admin/root/process-root-add.js"></script> 
<!-- JS FileEnd  -->