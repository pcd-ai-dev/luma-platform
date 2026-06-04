<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */

	$rf=$page->infoFooter($idSite,$idLang);
	$htmlData=$string->removeBody($rf->text ?? '');
	$cssData=$rf->css;
	$varFooter="&copy; ".date("Y")." ".$siteName;

?>
<style><?= $cssData;?></style>
<?php 
	$textFooterBuilder= str_replace("#varFooter#", $varFooter, $htmlData);
	echo $textFooterBuilder;