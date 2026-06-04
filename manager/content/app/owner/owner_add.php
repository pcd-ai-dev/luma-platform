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

<!-- Header -->
<!-- Btn Fermeture -->
<div style="text-align:right;" id="close"><a href="/manager<?= $varLink; ?>/admin_owner.html" ><img src="/img/interface/btn/BtnClosePage.svg" alt="Fermer" name="Fermer" width="24" height="24" style="border:0px;" id="Fermer" /></a></div>
<!-- Btn Fermeture -->

<!-- Tabs -->
<div id="tabsholder">
	<ul class="tabs">
		<li id="tab1" style="background-color:#81B929;color:#FFFFFF">Ajout d'un utilisateur</li>
	</ul>
<!-- Tabs -->
</div>
<!-- /Header -->
<div style="width:95%;padding:20px 20px 0px 20px;">
	<table width="100%" style="border:0px;" cellpadding="0" cellspacing="0">
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>
				<form action="/manager<?= $varLink; ?>/admin_owner.html" method="post" id="addOwnerform" name="addOwnerform" enctype="multipart/form-data">
					<table width="100%" style="border:0px;text-align:left;" cellpadding="0" cellspacing="1">
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<th valign="top" nowrap="nowrap" style="text-align:left;">
								<div class="headerform">Informations</div>
							</th>
						</tr>
						<tr>
							<td>
								<div class="corpsForm">
									<div class="dropdown">
										<select name="gender" id="gender">
											<option value="Mr">Mr</option>
											<option value="Mme">Mme</option>
											<option value="Mlle">Mlle</option>
										</select>
									</div>
									<div>&nbsp;</div>
									<div><input id="first_name" name="first_name" class="input-full requiredField" data-required="true" value="" placeholder="Pr&eacute;nom..."/>
									</div>
									<div>&nbsp;</div>
									<div><input id="last_name" name="last_name" class="input-full requiredField" data-required="true"value="" placeholder="Nom..."/>
									</div>
									<div>&nbsp;</div>
									<div><input id="phone" name="phone" class="input-full" value="" placeholder="Num&eacute;ro de t&eacute;l&eacute;phone..."/>
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<th valign="top" nowrap="nowrap" style="text-align:left;">
								<div class="headerform">Options</div>
							</th>
						</tr>
						<tr>
							<td>
								<div class="corpsForm">
									<table>
										<tr>
											<td>&nbsp;</td>
											<td><label for="type"><b>R&ocirc;le : </b></label></td>
											<td>&nbsp;</td>
											<td>
												<div class="dropdown">
													<select name="level" id="level">
														<option value="0">Administrateur</option>
														<option value="1">Formateur</option>
														<option value="2">Utilisateur</option>
														<option value="3">Financier</option>
													</select>
												</div>
											</td>
										</tr>
									</table>
								</div>
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<th valign="top" nowrap="nowrap" style="text-align:left;">
								<div class="headerform">Identifiants</div>
							</th>
						</tr>
						<tr>
							<td>
								<div class="corpsForm">
									<table style="border:0px;" cellpadding="0" cellspacing="0">
										<tr>
											<th>Email :</th>
											<td>&nbsp;</td>
											<td><input type="text" id="emails" class="input-normal requiredField" name="emails"  placeholder="Email..." value=""/>
											</td>
											<td>&nbsp;</td>
											<th>Mot de passe :</th>
											<td>&nbsp;</td>
											<td><input type="password" id="pass" class="input-normal" name="pass" placeholder="Mot de passe ..." value=""/>
											</td>
											<td>&nbsp;</td>
										</tr>
									</table>
								</div>
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td><button class="cubutton">Ajoutez</button>
							</td>
						</tr>
						<tr>
							<td><input type="hidden" name="addstart" id="addstart" value="1"/>
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
					</table>
				</form>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
	</table>
</div>

<!-- JS File -->
<script>
	var idSite="<?= $_SESSION['siteData']['id'];?>";
	var ajaxPath="<?= AJAXPATH; ?>"; 
</script>
<script type="text/javascript" src="/js/admin/owner/process-owner-add.js"></script>
<!-- JS FileEnd  -->