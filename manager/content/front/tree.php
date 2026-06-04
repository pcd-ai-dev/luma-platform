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


?>

<div style="text-align:left;">
		<?php 

			$param=$secure->v('slug', 'param', false) ?? '';

			switch($param){
				case 'param_page': include ('content/front/tree/index.php');break;
				case 'param_content': include ('content/front/tree/page.php');break;
				case 'param_add': include ('content/front/tree/content_add.php');break;

				default:include ('content/front/tree/index.php');
			} 
		?>
</div>