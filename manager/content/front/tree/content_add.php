<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2025 | Luma Prod - Pierre Cosmao Dumanoir
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

	  include(ADMINPROCESSPATH.'tree/index.php' );


?>
<!-- Header -->
<!-- Btn Fermeture -->
<div style="text-align:right;" id="close"><a href="/manager<?= $varLink; ?>/admin_tree.html" ><img src="/img/interface/btn/BtnClosePage.svg" alt="Fermer" name="Fermer" width="24" height="24" style="border:0px;" id="Fermer" /></a></div>
<!-- Btn Fermeture -->

<!-- Tabs -->
<div id="tabsholder">
	<ul class="tabs">
		<li id="tab1" style="background-color:#81B929;color:#FFFFFF">Ajout d'une page</li>
	</ul>
<!-- Tabs -->
</div>
<!-- /Header -->
<!-- Content -->
<div style="width:95%;padding:20px 20px 0px 20px;">
  <div>&nbsp;</div>
  <div>&nbsp;</div>
  <div>&nbsp;</div>
  <div>&nbsp;</div>
  <div><b>Les champs pr&eacute;c&egrave;d&eacute;s d'un trait rouge sont obligatoires.</b></div>
  <form action="/manager/content/tree/url.php" enctype="multipart/form-data" id="icat" name="icat" method="post"/>
    <div>&nbsp;</div>
    <div class="headerform">Page parente</div>
    <div class="corpsForm">
        <div class="dropdown">
          <select name="parentId" id="parentId" class="requiredField">
            <option value="">Sélectionnez...</option>
            <?php
              $incn = 0;
              $getCat = intval($_GET['category'] ?? 0);

              foreach ($catTree as $category => $catName) {
                  echo '<optgroup label="' . $page->catNameCheck($category) . '">';
                  foreach ($catName as $catElement => $catValue) {
                      $id   = $tabCat[$incn]['idCheck'];
                      $name = $tabCat[$incn]['name'];
                      $selected = ($id == $getCat) ? 'selected' : '';
                      echo '<option value="'.$id.'" '.$selected.'>'.$name.'</option>';
                      $incn++;
                  }
                  echo '</optgroup>';
              }
            ?>
          </select>
        </div>           
    </div>
  </form>
  <div>&nbsp;</div>
  <form action="/manager<?= $varLink; ?>/admin_tree.html" method="post" id="addpageform" name="addpageform" enctype="multipart/form-data">
    <div>&nbsp;</div>
    <div class="headerform">Nom de la page</div>
    <div class="corpsForm">
        <?php for($i=0; $i<$nbLang;$i++){?>
          <input type="text" name="name_<?= $tabLang[$i]['id'];?>" id="name_<?= $tabLang[$i]['id'];?>" value="" class="input-full-lang flag<?= $tabLang[$i]['id'];?> requiredField" placeholder="Nom de la page ..."/>
        <?php } ?>
    </div>
    <div>&nbsp;</div>
    <div class="headerform">Type de page</div>
    <div class="corpsForm">
      <div class="dropdown">
        <select name="type" id="type" class="requiredField">
          <option value="0">S&eacute;lectionnez ...</option>
          <option value="1" selected>Page Builder</option>
          <option value="2">Page normal</option>
          <option value="3">Articles</option>
        </select>
      </div>
    </div>
    <div>&nbsp;</div>
    <div><button class="cubutton">Ajoutez</button></div>
    <div>
      <input type="hidden" name="addCategoryStart" id="addCategoryStart" value="1" />
      <input type="hidden" name="parent" id="parent" value="<?= intval($idCategory); ?>" />
    </div>
  </form>
</div>
<!-- /Content -->

<!-- JS File --> 
<script type="text/javascript" src="/js/class/validator.class.js"></script>
<script type="text/javascript" src="/js/admin/page/process-category-add.js"></script> 
<!-- JS FileEnd  -->