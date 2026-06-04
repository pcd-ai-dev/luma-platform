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
	
		include (ADMINPROCESSPATH.'root/root.php');


	//---------------------------------------------------------  
	// DB QUERY
	//--------------------------------------------------------- 

		$req = "SELECT * FROM ".$prefixRoot."site ORDER BY position ASC";
	  	$res = $db->prepare($req);
		$res->execute();
		$nb=$res->rowCount();
		$tab = $res->fetchAll();
		$res->closeCursor();
		$res = NULL;

?>

    <!-- Update MSG -->
    <div id="builderMsgUpdate">&nbsp;</div>
    <?= (isset($msgUpdate))? $msgUpdate : ''; ?>
    <!-- /Update MSG -->

<div id="contentContainer">
	<div class="contentLeft" style="min-width:80%;">
	  	<div style="width:95%;padding:20px 20px 0px 20px;">
			<div class="headerformglobal">
		  		<table style="border:0px;" cellpadding="0" cellspacing="0" width="100%">
					<tr>
					<td nowrap><table width="100%" style="border:0px;" cellspacing="0" cellpadding="0">
						<tr>
							<td style="text-align:left;">Liste des sites <?= (($_SESSION['siteData']['status']==1)? $_SESSION['siteData']['name'] : ''); ?></td>
						</tr>
						</table></td>
					<td style="text-align:right;" height="35"><a href="/manager<?= $varLink; ?>/admin_root/param_add/0/0/0/0/"  class="adminSmallButton" style="color:#ffffff;">Ajoutez un site</a></td>
					</tr>
		  		</table>
			</div>
	  	</div>
	  	<div style="width:95%;padding:0px 20px 200px 20px;">
			<div class="corpsForm">
				<?php

				$searchcontent="";

				if($nb==0){
					$searchcontent.= '<div class="corpsForm">Pas de r&eacute;sultats pour cette recherche</div>';
				}else{

					$searchcontent.= '<ol class="sortable site" style="margin-left:-40px;">';

					for ($i=0;$i<$nb;$i++){
						
						$img=$_SERVER['DOCUMENT_ROOT'].'/img/root/square/'.$tab[$i]['imgSquare'];
					
						$imgStatus=($tab[$i]['status']=='1')? 'statusOnGreen.png' : 'statusOff.png';

						$searchcontent.= '<li data-id="'.$tab[$i]['id'].'" id="list_site_'.$tab[$i]['id'].'">';
						$searchcontent.= '<div class="item">';
						$searchcontent.= '<table border="0" cellpadding="0" cellspacing="0" width="100%">';
						$searchcontent.= '<tr>';
						$searchcontent.= '<td width="16" height="55"><img src="/img/interface/icons/poubelleoff.png" width="16" height="16" border="0" class="deleteSite"  id="'.intval($tab[$i]['id']).'"/></td>';
						$searchcontent.= '<td><span style="padding:5px;">&nbsp;</span></td>';
						$searchcontent.= '<td width="20" class="tdnotright">';
						if((file_exists($img))&&(!empty($tab[$i]['imgSquare']))){
							$searchcontent.= '<img src="/img/root/square/'.$tab[$i]['imgSquare'].'?'.rand().'" alt="" width="50" style="border:0px;" class="teamThumb"/>';
						}else{
							$searchcontent.= '<img src="'.$imgDefaultSquare.'" height="50" style="border:0px;" />';
						}
						$searchcontent.= '</td>';
						$searchcontent.= '<td><span style="padding:5px;">&nbsp;</span></td>';
						$searchcontent.= '<td width="99%"><a href="/manager'.$varLink.'/admin_root/param_content/'.intval($tab[$i]['id']).'/1/0/0/" style="font-weight:bold;">'.((isset($tab[$i]['name']))? $tab[$i]['name'] : 'Sans nom').'</a> </td>';
						$searchcontent.='<td>&nbsp;</td>';

						$searchcontent.='<td style="text-align:right;">';
						$searchcontent.='<button id="checkStatusGallery'.$tab[$i]['id'].'" data-id="'.$tab[$i]['id'].'" data-table="'.$prefixRoot.'site" data-field="status" data-target-id="id" data-output="green" style="border:0px;background:none;">';
						$searchcontent.='<img src="/img/interface/icons/'.$imgStatus.'" width="13" height="13" style="border:0px;"/>';
						$searchcontent.='</button>';
						$searchcontent.='</td>';

						$searchcontent.= '<td><span style="padding:5px;">&nbsp;</span></td>';
						$searchcontent.= '</table>';
						$searchcontent.= '</div>';
						$searchcontent.= '</li>';
					}
					$searchcontent.= '</ol>';
				}
				echo $searchcontent;

				?>
	  		</div>
		</div>
	</div>
	<div class="contentRight"></div>
</div>

<!-- JS -->
<script>
    var ajaxPath="<?= AJAXPATH; ?>"; 
    var prefix="<?= $prefixRoot; ?>";
    var adminColor= "<?= $colorCustomAdmin; ?>";
	var adminTextColor= "<?= $colorCustomTextAdmin; ?>";
</script>
<script type="text/javascript" src="/js/admin/action/process-sortable.js"></script>
<script type="text/javascript" src="/js/admin/action/process-status.js"></script>
<script type="text/javascript" src="/js/admin/root/process-root.js"></script>
<!-- /JS -->