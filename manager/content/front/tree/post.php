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

<!--CSS -->
<link href="/jsp/flatpickr/flatpickr.min.css" rel="stylesheet" type="text/css"/>
<!--CSS end -->

<!-- Début mofif post -->
<div style="margin-bottom:100px;padding-top:10px;padding-left:15px;width:75%;">
	<?php if ((intval($_GET['mod']) != "0") && ($modStatus == "0")): ?>
<!-- PHOTO UPLOAD MOD -->
					<div class="headerformtoggle">
						<div class="modifAreaSelector">Envoyer des photos</div>
					</div>
					<div id="uploadarea" style="display:none">
						<form id="uploadform_<?= $postUId; ?>">

							<div>&nbsp;</div>
							<div>&nbsp;</div>

							<button type="button" id="addFileBtn_<?= $postUId; ?>" class="addFileBtn">Ajouter un fichier</button>
							<button type="button" id="startUpload_<?= $postUId; ?>" class="startUpload" disabled>Commencer le chargement</button>

							<!-- Zone drag & drop -->
							<div id="dropZone_<?= $postUId; ?>" class="dropZone">
								<b>Glissez vos fichiers ici ou utilisez le bouton Ajouter</b>
							</div>

							<!-- Prévisualisation des fichiers -->
							<div id="filePreview_<?= $postUId; ?>" class="filePreview"></div>

							<!-- Progression globale -->
							<div id="progressWrapper_<?= $postUId; ?>" style="display:none; width:100%; margin-top:10px;">
								<div id="progressBar_<?= $postUId; ?>" style="width:0%; height:20px; background:#4caf50;"></div>
								<span id="progressPercent_<?= $postUId; ?>">0%</span>
							</div>
							<div>&nbsp;</div>
						</form>
					</div>
					<div>&nbsp;</div>
					<!-- /PHOTO UPLOAD MOD -->

					<!-- GALERIE PHOTO DISPLAY MOD -->
					<div class="headerformtoggle" style=" width:100%;">
						<div class="modifAreaSelector">Gallerie photo</div>
					</div>
					<div id="showFolderArea" style="width:100%; margin-top:-10px; display:none;"></div>
					<div>&nbsp;</div>
                	<!-- /GALERIE PHOTO DISPLAY MOD -->

					<!-- MAIN PHOTO -->
					<div class="headerformtoggle">
						<div class="modifAreaSelector">Photo de mise en avant</div>
					</div>
					<div class="corpsForm" style="display:none">
						<div id="photoUploadAreaPost"></div>
					</div>
					<div>&nbsp;</div>
					<!-- /MAIN PHOTO -->

					<!-- MOD POst FORM -->
					<form action="<?= '/manager' . $varLink . '/admin_tree/param_content/' . $idCategory . '/4/0/0/'; ?>" method="post" id="postModForm" name="postModForm" enctype="multipart/form-data">
						<div class="headerformtoggle">
							<div class="modifAreaSelector">Titre / Date de publication</div>
						</div>
						<div class="corpsForm" style="display:none">
							<div class="headerform">Date de publication</div>
							<div class="corpsForm">
								<input type="text" name="modDate" id="modDate" value="<?= $rsmod->date; ?>" class="input-normal" />
							</div>
						
							<div>&nbsp;</div>
							<div class="headerform">
								Modification d'un article : Titre &nbsp;<span class="redb">*</span>
							</div>
							<div class="corpsForm">
								<?php for ($i = 0; $i < $nbLang; $i++) {
									$idLangCheck = $tabLang[$i]['id'];
									$rpost = $post->postLang($modId, $idLangCheck); ?>
									<div><input type="text" name="modTitlePost_<?= $idLangCheck; ?>" id="modTitlePost_<?= $idLangCheck; ?>" value="<?= $rpost->title; ?>" class="input-full-lang flag<?= $tabLang[$i]['id'];?> requiredField" placeholder="Titre de l'article ..." /></div>
								<?php } ?>
							</div>
						</div>
						<div>&nbsp;</div>
						<div class="headerformtoggle">
							<div class="modifAreaSelector">Description Courte</div>
						</div>
						<div class="corpsForm" style="display:none">
							<?php for ($i = 0; $i < $nbLang; $i++) {
								$idLangCheck = $tabLang[$i]['id'];
								$rpost = $post->postLang($modId, $idLangCheck); ?>
								<div id="imglang"><img src="/img/l/<?= $tabLang[$i]['id']; ?>.svg" width="21" /></div>
								<textarea name="modShortText_<?= $idLangCheck; ?>" id="modShortText_<?= $idLangCheck; ?>"><?= $rpost->shortText; ?></textarea>
								<script type="text/javascript">
									CKEDITOR.replace('modShortText_<?= $idLangCheck; ?>', {
										width: '100%',
										extraPlugins: 'autogrow',
										autoGrow_maxHeight: 800,
										removePlugins: 'resize',
										on: {
											paste: function(evt) {
												var editor = evt.editor;
												if (evt.data.dataValue.match(/object/)) {
													evt.data.dataValue = evt.data.dataValue.replace('&lt;', '<').replace('&gt;', '>');
													var element = CKEDITOR.dom.element.createFromHtml(evt.data.dataValue);
													editor.insertElement(element);
												}
											}
										}
									});
								</script>
								<div>&nbsp;</div>
							<?php } ?>
						</div>
						<div>&nbsp;</div>
						<div class="headerformtoggle">
							<div class="modifAreaSelector">Description longue</div>
						</div>
						<div class="corpsForm" style="display:none">
							<?php for ($i = 0; $i < $nbLang; $i++) {
								$idLangCheck = $tabLang[$i]['id'];
								$rpost = $post->postLang($modId, $idLangCheck); ?>
								<div id="imglang"><img src="/img/l/<?= $tabLang[$i]['id']; ?>.svg" width="21" /></div>
								<textarea name="modTextPost_<?= $idLangCheck; ?>" id="modTextPost_<?= $idLangCheck; ?>"><?= $rpost->text; ?></textarea>
								<script type="text/javascript">
									CKEDITOR.replace('modTextPost_<?= $idLangCheck; ?>', {
										width: '100%',
										extraPlugins: 'autogrow',
										autoGrow_maxHeight: 800,
										removePlugins: 'resize',
										on: {
											paste: function(evt) {
												var editor = evt.editor;
												if (evt.data.dataValue.match(/object/)) {
													evt.data.dataValue = evt.data.dataValue.replace('&lt;', '<').replace('&gt;', '>');
													var element = CKEDITOR.dom.element.createFromHtml(evt.data.dataValue);
													editor.insertElement(element);
												}
											}
										}
									});
								</script>
							<?php } ?>
						</div>
						<div>&nbsp;</div>
						<div>&nbsp;</div>
						<div>
							<input type="hidden" name="modPostStart" id="modPostStart" value="1" />
							<input type="hidden" name="modId" id="modId" value="<?= $modId; ?>" />
							<input type="hidden" name="modLattitude" id="modLattitude" value="<?= $modLattitude; ?>" />
							<input type="hidden" name="modLongitude" id="modLongitude" value="<?= $modLongitude; ?>" />
							<input type="hidden" name="modAddress" id="modAddress" value="<?= mb_convert_encoding($modAddress, 'UTF-8', 'ISO-8859-1'); ?>"/>
							<input type="hidden" name="modPhone" id="modPhone" value="<?= $modPhone; ?>" />
							<input type="hidden" name="modUrl" id="modUrl" value="<?= $modUrl; ?>" />
							<input type="hidden" name="modLink" id="modLink" value="<?= (isset($modlink))? $modLink : ''; ?>" />
						</div>
						<div>
							<table style="border:0px;" cellspacing="0" cellpadding="0">
								<tr>
									<td>
										<div id="cuButtonRed"><a href="<?= '/manager' . $varLink . '/admin_tree/param_content/' . $idCategory . '/4/0/0/'; ?>" style="color:#FFFFFF;">Fermer</a></div>
									</td>
									<td>&nbsp;</td>
									<td><button class="cubutton">Modifiez</button></td>
								</tr>
							</table>
						</div>
						<div>&nbsp;</div>
						<div>&nbsp;</div>
					</form>
	<?php else: ?>

		<?php if ($statusForm): ?>
						<div id="showadd">
							<form action="<?= '/manager' . $varLink . '/admin_tree/param_content/' . $idCategory . '/4/0/0/'; ?>" method="post" id="postAddForm" name="postAddForm" enctype="multipart/form-data">
								<div class="headerformglobal">
									Ajout d'un article
								</div>
								<div>&nbsp;</div>
								<div class="headerform">Date</div>
								<div class="corpsForm">
									<input type="text" name="datePost" id="datePost" value="<?= date("Y-m-d h:i:s"); ?>" class="input-normal" />
								</div>
								<div>&nbsp;</div>
								<div class="headerform">Ajout d'un article : Titre</div>
								<div class="corpsForm">
									<?php for ($i = 0; $i < $nbLang; $i++) {
										$idLangCheck = $tabLang[$i]['id'];  ?>
										<div>
											<input type="text" name="titleAdd_<?= $idLangCheck; ?>" id="titleAdd_<?= $idLangCheck; ?>" value="" class="input-full-lang flag<?= $tabLang[$i]['id']; ?> requiredField" placeholder="Titre de l'article..."/>
										</div>
									<?php } ?>
								</div>
								<div>&nbsp;</div>
								<div>
									<table style="border:0px;" cellspacing="0" cellpadding="0">
										<tr>
											<td>
												<div id="cuButtonRed"><a href="<?= '/manager' . $varLink . '/admin_tree/param_content/' . $idCategory . '/4/0/0/'; ?>" style="color:#FFFFFF;">Fermer</a></div>
											</td>
											<td>&nbsp;</td>
											<td><button class="cubutton">Ajoutez</button></td>
										</tr>
									</table>
									<input type="hidden" name="addPostStart" id="addPostStart" value="1" />
								</div>
							</form>
						</div>

		<?php endif; ?>

	<?php endif; ?>
				<div>&nbsp;</div>
				<div class="headerformglobal">
					<table style="border:0px;" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td nowrap>
								<table width="100%" style="border:0px;" cellspacing="0" cellpadding="0">
									<tr>
										<td style="text-align:left;">Articles <?= (($_SESSION['siteData']['status']==1)? $_SESSION['siteData']['name'] : ''); ?></td>
									</tr>
								</table>
							</td>
							<td style="text-align:right;" height="35"><a href="<?= '/manager' . $varLink . '/admin_tree/param_content/' . $idCategory . '/4/1/0/'; ?>" class="adminSmallButton" style="color:#ffffff;">Ajoutez un article</a></td>
						</tr>
					</table>
				</div>
				<div class="corpsForm">
					<div class="search-wrapper">
						<input type="text" name="q" id="q" class="input-full search-input" placeholder="Recherche ...">
						<button type="button" class="clear-search" aria-label="Effacer">
							<svg viewBox="0 0 100 100" class="clear-icon" aria-hidden="true">
								<rect x="40.44" y="-3.43" width="19.12" height="106.86" transform="translate(-20.71 50) rotate(-45)" fill="#1d1d1b"/>
								<rect x="-3.43" y="40.44" width="106.86" height="19.12" transform="translate(-20.71 50.01) rotate(-45)" fill="#1d1d1b"/>
							</svg>
						</button>
					</div>
					<div id="results"></div>
				</div>
	<div>&nbsp;</div>
	<div>&nbsp;</div>
	<div>&nbsp;</div>
	<div>&nbsp;</div>
	<div>&nbsp;</div>
	<div>&nbsp;</div>
</div>
<!-- Fin mofif post -->