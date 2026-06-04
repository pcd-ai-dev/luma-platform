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

<form action="<?= '/manager'.$varLink .'/admin_root/param_content/'.$idSite.'/1/0/0/'; ?>" method="post" id="modRootForm" name="modRootForm" enctype="multipart/form-data">
	<div style="width:95%;border:0px;padding:20px;">
		<div>&nbsp;</div>
		<div>&nbsp;</div>
		<div>&nbsp;</div>
		<div class="headerform">Informations</div>
		<div class="corpsForm">
			<div><input id="name" name="name" class="input-full requiredField" value="<?= $rinfo->name; ?>" placeholder="Nom du site..."/></div>
			<div><input id="varlink" name="varlink" class="input-full" value="<?= $rinfo->varLink; ?>" placeholder="Variable d'adressage..."/></div>
		</div>
		<div>&nbsp;</div>
		<div>&nbsp;</div>
		<div class="headerform">Param&eacute;tres</div>
		<div class="corpsForm">
			<div><label for="contactRecipient"><b>Email par d&eacute;fault : </b><label><input id="contactRecipient" name="contactRecipient" class="input-normal" value="<?= $rinfo->contactRecipient; ?>" placeholder="Adresse mail par d&eacute;fault ..."/></div>
			<div>&nbsp;</div>
			<div>
				<table>
					<tr>
						<td><label for="modHomePageId"><b>Page d'accueil : </b><label></td>
						<td>&nbsp;</td>
						<td>
							<div class="dropdown">
								<?php  
									echo '<select name="modHomePageId" id="modHomePageId" />';
									$incn=0;
									echo '<option value="">S&eacute;lectionnez...</option>';
									foreach ($catTree as $category => $catName){
										echo '<optgroup label="'.$page->catNameCheck($category).'" >';
										foreach($catName as $catElement => $catvalue){
											$categorycheck=$tabCat[$incn]['idCheck'];
											$getCat=intval($rinfo->homePage);
											if($categorycheck==$getCat){$check='selected="selected"';}else{$check='';}
											echo '<option value="'.$tabCat[$incn]['idCheck'].'" '.$check.' >'.$tabCat[$incn]['name'].'</option>';
											$incn++;
										}
										echo "</optgroup>";
									}
									echo '</select>';
								?>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div>&nbsp;</div>
			<div>
				<table>
					<tr>
						<td><label for="modNewsPageId"><b>Page Blog : </b><label></td>
						<td>&nbsp;</td>
						<td>
							<div class="dropdown">
								<?php  
									echo '<select name="modNewsPageId" id="modNewsPageId" />';
									$incn=0;
									echo '<option value="">S&eacute;lectionnez...</option>';
									foreach ($catTree as $category => $catName){
										echo '<optgroup label="'.$page->catNameCheck($category).'" >';
										foreach($catName as $catElement => $catvalue){
											$categorycheck=$tabCat[$incn]['idCheck'];
											$getCat=intval($rinfo->newsPage);
											if($categorycheck==$getCat){$check='selected="selected"';}else{$check='';}
											echo '<option value="'.$tabCat[$incn]['idCheck'].'" '.$check.' >'.$tabCat[$incn]['name'].'</option>';
											$incn++;
										}
										echo "</optgroup>";
									}
									echo '</select>';
								?>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div>&nbsp;</div>
			<div>
				<table>
					<tr>
						<td><label for="modArtclePageId"><b>Page Template Blog : </b><label></td>
						<td>&nbsp;</td>
						<td>
							<div class="dropdown">
								<?php  
									echo '<select name="modArtclePageId" id="modArtclePageId" />';
									$incn=0;
									echo '<option value="">S&eacute;lectionnez...</option>';
									foreach ($catTree as $category => $catName){
										echo '<optgroup label="'.$page->catNameCheck($category).'" >';
										foreach($catName as $catElement => $catvalue){
											$categorycheck=$tabCat[$incn]['idCheck'];
											$getCat=intval($rinfo->articlePage);
											if($categorycheck==$getCat){$check='selected="selected"';}else{$check='';}
											echo '<option value="'.$tabCat[$incn]['idCheck'].'" '.$check.' >'.$tabCat[$incn]['name'].'</option>';
											$incn++;
										}
										echo "</optgroup>";
									}
									echo '</select>';
								?>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div>&nbsp;</div>
			<div>
				<table>
					<tr>
						<td><label for="modNewsPageId"><b>Page Privacy : </b><label></td>
						<td>&nbsp;</td>
						<td>
							<div class="dropdown">
								<?php  
									echo '<select name="modPrivacyPageId" id="modPrivacyPageId" />';
									$incn=0;
									echo '<option value="">S&eacute;lectionnez...</option>';
									foreach ($catTree as $category => $catName){
										echo '<optgroup label="'.$page->catNameCheck($category).'" >';
										foreach($catName as $catElement => $catvalue){
											$categorycheck=$tabCat[$incn]['idCheck'];
											$getCat=intval($rinfo->privacyPage);
											if($categorycheck==$getCat){$check='selected="selected"';}else{$check='';}
											echo '<option value="'.$tabCat[$incn]['idCheck'].'" '.$check.' >'.$tabCat[$incn]['name'].'</option>';
											$incn++;
										}
										echo "</optgroup>";
									}
									echo '</select>';
								?>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div>&nbsp;</div>
			<div>
				<table>
					<tr>
						<td><label for="modContactSuccessPageId"><b>Page de confirmation d'envoi de mail : </b><label></td>
						<td>&nbsp;</td>
						<td>
							<div class="dropdown">
								<?php  
									echo '<select name="modContactSuccessPageId" id="modContactSuccessPageId" />';
									$incn=0;
									echo '<option value="">S&eacute;lectionnez...</option>';
									foreach ($catTree as $category => $catName){
										echo '<optgroup label="'.$page->catNameCheck($category).'" >';
										foreach($catName as $catElement => $catvalue){
											$categorycheck=$tabCat[$incn]['idCheck'];
											$getCat=intval($rinfo->contactSuccessPage);
											if($categorycheck==$getCat){$check='selected="selected"';}else{$check='';}
											echo '<option value="'.$tabCat[$incn]['idCheck'].'" '.$check.' >'.$tabCat[$incn]['name'].'</option>';
										$incn++;
										}
									echo "</optgroup>";
									}
									echo '</select>';
								?>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div>&nbsp;</div>
		</div>
		<div>&nbsp;</div>
		<div>&nbsp;</div>
		<div>&nbsp;</div>
		<div class="headerform">Couleurs</div>
		<div>&nbsp;</div>
		<div><table style="border:0px;"><tbody><tr><td><label for="bgColor" ><b>Couleur d'arri&egrave;re plan</b> &nbsp;&nbsp;</label></td><td><input type="text" id="bgColor" name="bgColor" data-coloris value="<?= $rinfo->bgColor;?>" class="colorInput"/></td></table></div>
		<div>&nbsp;</div>
		<div><table style="border:0px;"><tbody><tr><td><label for="adminColor" ><b>Couleur de Boutons/Ic&ocir;nes Espace Admin</b> &nbsp;&nbsp;</label></td><td><input type="text" id="adminColor" data-coloris name="adminColor" value="<?= $rinfo->adminColor;?>" class="colorInput"/></td></table></div>
		<div>&nbsp;</div>
		<div><table style="border:0px;"><tbody><tr><td><label for="adminTextColor" ><b>Couleur du texte Espace Admin</b> &nbsp;&nbsp;</label></td><td><input type="text" id="adminTextColor" data-coloris name="adminTextColor" value="<?= $rinfo->adminTextColor;?>" class="colorInput"/></td></table></div>
		<div>&nbsp;</div>
		<div>&nbsp;</div>
		<div><button class="cubutton">Modifiez</button></div>
		<div>
			<input type="hidden" name="idSite" id="idSite" value="<?= $rinfo->id; ?>"/>
			<input type="hidden" name="modStart" id="modStart" value="1"/>
		</div>
		<div>&nbsp;</div>
		<div>&nbsp;</div>
		<div>&nbsp;</div>
		<div>&nbsp;</div>
		<div>&nbsp;</div>
		<div>&nbsp;</div>
		<div>&nbsp;</div>
		<div>&nbsp;</div>
		<div>&nbsp;</div>
	</div>
</form>