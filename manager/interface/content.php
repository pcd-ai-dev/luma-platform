<?php
/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */
?>

<div id="adminheader"><?php include('header.php'); ?></div>
	<div id="adminContainer">
		<div class="block_gauche" ><?php include ('menu.php');?></div>
		<div class="block_droit">
			<?php 
				$defaultPage="content/index.php";
				 switch($item){

					//FORGOT
					case 'admin_forgot': include ('content/config/forgot.php');break;

					//ADMIN
					case 'admin_cgu': include ('content/admin/cgu.php');break;
					case 'admin_root': include ('content/admin/root.php');break;
					
					//FRONT
					case 'admin_tree': include ('content/front/tree.php');break;
					case 'admin_footer': include ('content/front/footer.php');break;
					case 'admin_header': include ('content/front/header.php');break;
					case 'admin_gallery': include ('content/front/gallery.php');break;
					case 'admin_product': include ('content/front/product.php');break;
					case 'admin_location': include ('content/front/location.php');break;

					//OWNER
					case 'admin_owner_list': include ('content/app/owner/index.php');break;
					case 'admin_owner': include ('content/app/owner.php');break;
					case 'admin_owner_add': include ('content/app/owner/add.php');break;

					//DEFAULT
					default:include ($defaultPage);
				} 
			?>
		</div>
	</div>
<!-- footer -->
<div id="footeradmin">
	<?php include(MANAGERPATH.'interface/agent.php');?>
  	<?php include(MANAGERPATH.'interface/footer.php');?>
</div>
<!-- fin footer -->