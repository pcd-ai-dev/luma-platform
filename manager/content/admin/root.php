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
			case 'param_root': include ('content/admin/root/index.php');break;
			case 'param_content': include ('content/admin/root/page.php');break;
			case 'param_add': include ('content/admin/root/root_add.php');break;
			default : include ('content/admin/root/index.php');break;
		} 
	?>
</div>