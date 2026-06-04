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
	
		include (ADMINPROCESSPATH.'owner/owner.php');


	//---------------------------------------------------------  
	// DB QUERY
	//--------------------------------------------------------- 

		$req = "SELECT * FROM ".$prefixAdmin."user WHERE idSite=:idSite ORDER BY position ASC";
	  	$res = $db->prepare($req);
	  	$res->bindValue(':idSite', $adminData['idSite'], PDO::PARAM_STR);
		$res->execute();
		$nb=$res->rowCount();
		$tab = $res->fetchAll();
		$res->closeCursor();
		$res = NULL;


?>

<!-- Update MSG -->
<div id="builderMsgUpdate">&nbsp;</div>
<!-- /Update MSG -->

<!-- Content -->
<div id="contentContainer">
	<div class="contentLeft" style="min-width:80%;">
	  <div style="width:95%;padding:20px 20px 0px 20px;">
		<div class="headerformglobal">
		  <table style="border:0px;" cellpadding="0" cellspacing="0" width="100%">
			<tr>
			  <td nowrap><table width="100%" style="border:0px;" cellspacing="0" cellpadding="0">
				  <tr>
					<td style="text-align:left;">Acc&egrave;s administration <?= (($_SESSION['siteData']['status']==1)? $_SESSION['siteData']['name'] : ''); ?></td>
				  </tr>
				</table></td>
			  <td style="text-align:right;" height="35"><a href="/manager<?= $varLink; ?>/admin_owner/param_add/0/0/0/0/"  class="adminSmallButton" style="color:#ffffff;">Ajoutez un utilisateur</a></td>
			</tr>
		  </table>
		</div>
	  </div>
	  <div style="width:95%;padding:0px 20px 200px 20px;">
		<div class="corpsForm">

		<?php

			$searchContent="";

			if($nb==0){
				$searchContent.= '<div class="corpsForm">Pas d\'enregistrement.</div>';
			}else{

				$searchContent.= '<ol class="sortable owner" style="margin-left:-40px;">';

				for ($i=0;$i<$nb;$i++){

					$photo=$_SERVER['DOCUMENT_ROOT'].'/img/admin/square/'.$tab[$i]['img'];
					
					$imgStatus=($tab[$i]['status'])? 'statusOnGreen.png' : 'statusOff.png';

					$searchContent.= '<li data-id="'.$tab[$i]['id'].'" id="list_owner_'.$tab[$i]['id'].'">';
					$searchContent.= '<div class="item">';
					$searchContent.= '<table border="0" cellpadding="0" cellspacing="0" width="100%">';
					$searchContent.= '<tr>';
					$searchContent.= '<td width="16" height="55"><img src="/img/interface/icons/poubelleoff.png" width="16" height="16" border="0" class="delete"  id="'.intval($tab[$i]['id']).'"/></td>';
					$searchContent.= '<td><span style="padding:5px;">&nbsp;</span></td>';
					$searchContent.= '<td width="20" class="tdnotright">';
					if((file_exists($photo))&&(!empty($tab[$i]['img']))){
						$searchContent.= '<img src="/img/admin/square/'.$tab[$i]['img'].'?'.rand().'" alt="" width="50" style="border:0px;" class="teamThumb"/>';
					}else{
						$searchContent.= '<img src="'.$imgDefaultSquare.'" height="50" style="border:0px;" />';
					}
					$searchContent.= '</td>';
					$searchContent.= '<td><span style="padding:5px;">&nbsp;</span></td>';
					$searchContent.= '<td width="99%"><a href="/manager'.$varLink.'/admin_owner/param_main/'.intval($tab[$i]['id']).'/1/0/0/" style="font-weight:bold;">'.$tab[$i]['first_name'].' '.$tab[$i]['last_name'].' </a></td>';
					$searchContent.='<td>&nbsp;</td>';

					$searchContent.='<td style="text-align:right;">';
					$searchContent.='<button id="checkStatusGallery'.$tab[$i]['id'].'" data-id="'.$tab[$i]['id'].'" data-table="'.$prefixAdmin.'user" data-field="status" data-target-id="id" data-output="green" style="border:0px;background:none;">';
					$searchContent.='<img src="/img/interface/icons/'.$imgStatus.'" width="13" height="13" style="border:0px;"/>';
					$searchContent.='</button>';
					$searchContent.='</td>';

					$searchContent.= '<td><span style="padding:5px;">&nbsp;</span></td>';
					$searchContent.= '</table>';
					$searchContent.= '</div>';
					$searchContent.= '</li>';
				}
			$searchContent.= '</ol>';



		}
			echo $searchContent;

		?>

	  </div>
	  </div>
	</div>
	<div class="contentRight">
	  <div>
		<ul id="infoAdmin">
		  <li>Cette section vous permet de contr&ocirc;ler les droits d'acc&egrave;s et d'administration du site</li>
		  <li>Fa&icirc;tes <strong>attention</strong>, la suppression d'un profil est d&eacute;finitif et les donn&eacute;es y attenant seront perdues.</li>
		</ul>
	  </div>
	</div>
</div>
<!-- /Content -->

<!-- JS -->
<script>
    var ajaxPath="<?= AJAXPATH; ?>"; 
    var prefix="<?= $prefixAdmin; ?>";
    var adminColor= "<?= $colorCustomAdmin; ?>";
	var adminTextColor= "<?= $colorCustomTextAdmin; ?>";
</script>
<script type="text/javascript" src="/js/admin/action/process-sortable.js"></script>
<script type="text/javascript" src="/js/admin/action/process-status.js"></script>
<script type="text/javascript" src="/js/admin/owner/process-owner.js"></script>
<!-- /JS -->