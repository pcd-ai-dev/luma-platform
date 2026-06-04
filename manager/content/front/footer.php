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
				case 'param_page': include ('content/front/footer/index.php');break;
				case 'param_content': include ('content/front/footer/page.php');break;
				case 'param_add': include ('content/footer/front/content_add.php');break;

				default:include ('content/front/footer/page.php');
			} 
		?>
</div>