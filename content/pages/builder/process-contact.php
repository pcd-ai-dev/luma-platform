<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2025 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */
 
 
	//---------------------------------------------------------
	// CONTACT FORM
	//---------------------------------------------------------

		$cryptographp='<img src="/includes/verification/cryptographp.php" alt="" />';

		$textBuilder= str_replace("#cryptographp#", (isset($cryptographp))?  $cryptographp  : '', $htmlText);
		$textBuilder= str_replace("#msgcrypto#", (isset($msgcrypto))?  $msgcrypto  : '', $textBuilder);
		$textBuilder= str_replace("#nameSession#", (isset($_SESSION['inputForm']['name']))? $_SESSION['inputForm']['name']  : '', $textBuilder);
		$textBuilder= str_replace("#phoneSession#", (isset($_SESSION['inputForm']['phone']))? $_SESSION['inputForm']['phone']  : '', $textBuilder);
		$textBuilder= str_replace("#emailSession#", (isset($_SESSION['inputForm']['email']))? $_SESSION['inputForm']['email']  : '', $textBuilder);
		$textBuilder= str_replace("#messageSession#", (isset($_SESSION['inputForm']['msg']))? $_SESSION['inputForm']['msg']  : '', $textBuilder);
		$textBuilder= str_replace("#csrf#", (isset($_SESSION['csrf']))? $_SESSION['csrf'] : '', $textBuilder);
		$textBuilder= str_replace("#contactBox#",$contactBox, $textBuilder);