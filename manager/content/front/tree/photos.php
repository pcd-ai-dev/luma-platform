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

<!-- PAGE GALLERY -->
<div style="width:95%;border:0px;padding:20px;">
  <div>&nbsp;</div>
  <div class="headerformtoggle" style="width:100%;"><div class="modifAreaSelector">Envoyer des photos</div></div>
  <div id="uploadarea" style="display:none">
			<div class="corpsForm">
        <form id="uploadform_<?= $pageUId; ?>">
          <div>&nbsp;</div>
          <button type="button" id="addFileBtn_<?= $pageUId; ?>" class="addFileBtn">Ajouter un fichier</button>
          <button type="button" id="startUpload_<?= $pageUId; ?>" class="startUpload" disabled>Start Upload</button>

					<!-- Zone drag & drop -->
					<div id="dropZone_<?= $pageUId; ?>" class="dropZone">
						<b>Glissez vos fichiers ici ou utilisez le bouton Ajouter</b>
					</div>

					<!-- Prévisualisation des fichiers -->
					<div id="filePreview_<?= $pageUId; ?>" class="filePreview"></div>

            <!-- Progression globale -->
            <div id="progressWrapper_<?= $pageUId; ?>" style="display:none; width:100%; margin-top:10px;">
              <div id="progressBar_<?= $pageUId; ?>" style="width:0%; height:20px; background:#4caf50;"></div>
              <span id="progressPercent_<?= $pageUId; ?>">0%</span>
            </div>
        </form>
      </div>
  </div>
  <div>&nbsp;</div>
  <div class="headerformtoggle"><div class="modifAreaSelector">Photos</div></div>
  <div id="showPagePhotoFolder" style="width:100%; margin-top:-10px; display:none;"></div>
</div>
<!-- /PAGE GALLERY -->