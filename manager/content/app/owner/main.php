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

<form action="<?= $urlSSL; ?>" method="post" id="modOwnerform" name="modOwnerform" enctype="multipart/form-data">
	<div style="width:95%;border:0px;padding:20px;">
		<div>&nbsp;</div>
		<div>&nbsp;</div>
		<div>&nbsp;</div>
		<div class="headerform">Informations</div>
		<div class="corpsForm">
			<div class="dropdown">
				<select name="gender" id="gender">
					<option value="Mr" <?= ($rinfo->gender==1)? 'selected' : ''; ?>>Mr</option>
					<option value="Mme" <?= ($rinfo->gender==2)? 'selected' : ''; ?>>Mme</option>
					<option value="Mlle" <?= ($rinfo->gender==3)? 'selected' : ''; ?>>Mlle</option>
				</select>
			</div>
		</div>
		<div class="corpsForm">
			<div>&nbsp;</div>
			<div><input id="first_name" name="first_name" class="input-full requiredField" value="<?= $rinfo->first_name; ?>" placeholder="Pr&eacute;nom..."/></div>
			<div>&nbsp;</div>
			<div><input id="last_name" name="last_name" class="input-full requiredField" value="<?= $rinfo->last_name; ?>" placeholder="Nom..."/></div>
			<div>&nbsp;</div>
			<div><input id="phone" name="phone" class="input-full" value="<?= $rinfo->phone; ?>" placeholder="Num&eacute;ro de t&eacute;l&eacute;phone..."/></div>
		</div>
		<div>&nbsp;</div>
		<div class="headerform">Options</div>
		<div class="corpsForm">
			<table>
				<tr>
					<td><label for="type"><b>R&ocirc;le : </b></label></td>
					<td>&nbsp;</td>
					<td>
						<div class="dropdown">
							<select name="level" id="level">
								<option value="0" <?= ($rinfo->level==0)? 'selected' : ''; ?>>Administrateur</option>
								<option value="1" <?= ($rinfo->level==1)? 'selected' : ''; ?>>Formateur</option>
								<option value="2" <?= ($rinfo->level==2)? 'selected' : ''; ?>>Utilisateur</option>
								<option value="3" <?= ($rinfo->level==3)? 'selected' : ''; ?>>Financier</option>
								<option value="4" <?= ($rinfo->level==4)? 'selected' : ''; ?>>Mailing</option>
							</select>
						</div>
					</td>
				</tr>
			</table>
		</div>
		<div>&nbsp;</div>
		<div class="headerform">Photo</div>
		<div class="corpsForm">
			<div id="photoUploadArea"></div>
		</div>
		<div>&nbsp;</div>
		<div class="corpsForm">
			<table style="border:0px;" cellpadding="0" cellspacing="0">
				<tr>
					<th>Email :</th>
					<td>&nbsp;</td>
					<td><input type="text" id="emails" class="input-normal requiredField" name="emails" value="<?= $rinfo->email; ?>"/></td>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
					<th>Mot de passe :</th>
					<td>&nbsp;</td>
					<td><input type="password" id="pass" class="input-normal" name="pass" value=""/></td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</div>
		<div>&nbsp;</div>
		<div>&nbsp;</div>
		<div><button class="cubutton">Modifiez</button></div>
		<div>
			<input type="hidden" name="adminId" id="adminId" value="<?= $rinfo->id; ?>"/>
			<input type="hidden" name="modStart" id="modStart" value="1"/>
		</div>
		<div>&nbsp;</div>
		<div>&nbsp;</div>
		<div>&nbsp;</div>
		<div>&nbsp;</div>
		<div>&nbsp;</div>
		<div>&nbsp;</div>
	</div>
</form>