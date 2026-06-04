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

?>

<div style="text-align:left;">
	<?php 
		$param=$secure->v('slug', 'param', false) ?? '';
		switch($param){
			case 'param_owner': include ('content/app/owner/index.php');break;
			case 'param_main': include ('content/app/owner/page.php');break;
			case 'param_add': include ('content/app/owner/owner_add.php');break;
			default : include ('content/app/owner/index.php');break;
		} 
	?>
</div>