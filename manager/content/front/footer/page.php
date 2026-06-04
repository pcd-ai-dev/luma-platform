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

		include (ADMINPROCESSPATH.'tree/index.php');


	//---------------------------------------------------------
	// Variables
	//---------------------------------------------------------


?>

<!-- Script general -->

<!-- Update MSG -->
<div id="builderMsgUpdate">&nbsp;</div>
<!-- /Update MSG -->

<!-- Tabs -->
<div>
	<ul class="tabs" id="tabsholder" data-initial-tab="<?= isset($tabItem) ? (int)$tabItem : 1 ?>">
        <?php
			for($i=0,$p=1; $i<$nbLang;$i++,$p++){
				$idLangCheck=$tabLang[$i]['id'];
				echo '<li data-tab="tab'.$p.'" class="flag'.$tabLang[$i]['id'].'" style="padding-left:40px;">Footer Builder</li>';
			}
    	?>
      
    </ul>
	<div id="contentContainerTabs">
		<div class="contentLeft" style="min-width:100%;">
			<div class="contents marginbot"> 

			  <?php 
					for($i=0,$p=1; $i<$nbLang;$i++,$p++){
						$idLangCheck=$tabLang[$i]['id'];
						$idLangPage='l_'.$idLangCheck;
						${$idLangPage}=$idLangCheck;
						$idlangPost=$idLangCheck;
						echo '<div id="content'.$p.'" class="tabscontent" style="margin:0px;">';
						include 'builder.php';
						echo '</div>';
					}
			  ?>

			</div>
		</div>
  </div>
  <!-- /Tabs -->
</div>

<!-- JS -->
<script type="text/javascript">
	var ajaxPath="<?= AJAXPATH; ?>";
	var adminColor= "<?= $colorCustomAdmin; ?>";
	var adminTextColor= "<?= $colorCustomTextAdmin; ?>";
</script>
<script type="text/javascript" src="/js/admin/action/process-tabs.js"></script>
<!-- /JS -->