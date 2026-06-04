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

<!-- Content -->
<div style="width:95%;border:0px;padding:20px;">
  <form action="<?= '/manager'.$varLink . '/admin_tree/param_content/'.$idCategory.'/12/'.$statusForm.'/0/'; ?>" method="post" id="addMapForm" name="addMapForm" enctype="multipart/form-data">
    <div>&nbsp;</div>
    <div><b>Les champs pr&eacute;c&egrave;d&eacute;s d'un trait rouge sont obligatoires.</b></div>
    <div>&nbsp;</div>
	<div class="headerform"><?= ($countMap == 0)? 'Cr&eacute;ez une carte' : 'Modifiez la carte'; ?> en saisissant une nouvelle adresse. </div>
	<div class="corpsForm">
		<table width="100%" style="border:0px;" cellpadding="0" cellspacing="0">
			<tr>
				<?php if ($countMap == 0) :?>
					<td><input type="text" name="address" id="address" value="" class="input-full requiredField" placeholder="Veuillez renseigner un nom ou une adresse."/></td>
				<?php else: ?>
					<td><input type="text" name="modAddress" id="modAddress" value="<?= $rMap->address; ?>" class="input-full requiredField" placeholder="Veuillez renseigner un nom ou une adresse."/></td>
				<?php endif; ?>
				<td>
					<?php if ($countMap == 0): ?>
						<input type="hidden" name="addMapStart" id="addMapStart" value="1" />
						<input type="hidden" name="lattitude" id="lattitude" value="" />
						<input type="hidden" name="longitude" id="longitude" value="" />
					<?php else: ?>
						<input type="hidden" name="modMapStart" id="modMapStart" value="1" />
						<input type="hidden" name="idCategory" id="modMapStart" value="<?= $idCategory;  ?>" />
						<input type="hidden" name="modLat" id="modLat" value="<?= (isset($modLat))? $modLat : ''; ?>" />
						<input type="hidden" name="modLng" id="modLng" value="<?= (isset( $modLng))? $modLng : ''; ?>" />
					<?php endif; ?>
				</td>
				<td style="text-align:right;">
					<?= ($countMap == 0)? '<button class="cubutton">Validez</button>' : '<button class="cubutton">Modifiez</button>';?>
				</td>
			</tr>
		</table>
	</div>
	<div>&nbsp;</div>
	<div>&nbsp;</div>
	<div>&nbsp;</div>
	<?php if ($countMap != 0) : ?>
		<div>&nbsp;</div>
		<div class="headerform">Mofifiez l'ic&ocirc;ne</div>
		<div class="corpsForm">
			<div id="photoArea" style="width:100%; margin-top:0px;">
				<div id="iconUploadArea"></div>
			</div>
		</div>
	<?php endif; ?>
	<div>&nbsp;</div>
	<div>&nbsp;</div>
	<?php if (((!empty($rMap->lat)) && (!empty($rMap->lng))) || ($countMap != 0)): ?>
	<div class="corpsForm">
		<div id="targetMap" style="width:100%; height:650px; border:1px;"></div>
	</div>
	<?php endif; ?>
	<div>&nbsp;</div>
	<div>&nbsp;</div>
	<div>&nbsp;</div>
	<div>&nbsp;</div>
	<div>&nbsp;</div>
	<div>&nbsp;</div>
	<div>&nbsp;</div>
</div>
<!-- /Content -->