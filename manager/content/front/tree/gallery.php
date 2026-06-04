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

    <div style="text-align:left;">
        <div id="contentContainer" style="display:block;width:95%; margin-bottom:100px;padding-top:10px;padding-left:15px;">
            <!-- MODIF GALERIE PHOTOS -->
            <?php if ((intval($_GET['mod']) != "0") && ($modStatus == "0")) : ?>
                <!-- PHOTO UPLOAD MOD -->
                <div class="headerformtoggle">
                    <div class="modifAreaSelector">Envoyer des photos</div>
                </div>
                <div id="uploadarea" style="display:none">
					<form id="uploadform_<?= $photoUId; ?>">

						<div>&nbsp;</div>

						<button type="button" id="addFileBtn_<?= $photoUId; ?>" class="addFileBtn">Ajouter un fichier</button>
						<button type="button" id="startUpload_<?= $photoUId; ?>" class="startUpload" disabled>Commencer le chargement</button>

						<!-- Zone drag & drop -->
						<div id="dropZone_<?= $photoUId; ?>" class="dropZone">
							<b>Glissez vos fichiers ici ou utilisez le bouton Ajouter</b>
						</div>

						<!-- Prévisualisation des fichiers -->
						<div id="filePreview_<?= $photoUId; ?>" class="filePreview"></div>

						<!-- Progression globale -->
						<div id="progressWrapper_<?= $photoUId; ?>" style="display:none; width:100%; margin-top:10px;">
							<div id="progressBar_<?= $photoUId; ?>" style="width:0%; height:20px; background:#4caf50;"></div>
							<span id="progressPercent_<?= $photoUId; ?>">0%</span>
						</div>
                        <div>&nbsp;</div>
					</form>
                </div>
                <div>&nbsp;</div>
                <!-- /PHOTO UPLOAD MOD -->

                <!-- GALERIE PHOTO DISPLAY MOD -->
                <div class="headerformtoggle">
                    <div class="modifAreaSelector">Photos</div>
                </div>
                <div id="showFolder" style="margin-top:-10px; display:none;"></div>
                <div>&nbsp;</div>
                <!-- /GALERIE PHOTO DISPLAY MOD -->

                <!-- INFO GALERIE FORM MOD -->
                <form action="<?= '/manager' . $varLink . '/admin_tree/param_content/' . $idCategory . '/5/' . $statusForm . '/0/'; ?>" method="post" id="galleryModPhoto" name="galleryModPhoto" enctype="multipart/form-data">
                    <div class="headerform">Modification d'une galerie : Titre &nbsp;<span class="redb">*</span></div>
                    <div class="corpsForm">
                        <?php for ($i = 0; $i < $nbLang; $i++) {
                            $idLangCheck = $tabLang[$i]['id'];
                            $rgallery = $photo->galleryLang($modId, $idLangCheck); ?>
                            <div><input type="text" name="modTitleGallery_<?= $idLangCheck; ?>" id="modTitleGallery_<?= $idLangCheck; ?>" value="<?= $rgallery->title; ?>" class="input-full-lang flag<?= $tabLang[$i]['id'];?> requiredField" placeholder="Titre de la galerie photos..."/></div>
                        <?php } ?>
                    </div>
                    <div>&nbsp;</div>
                    <div class="headerform">Description : (3 lignes maximum)</div>
                    <div class="corpsForm">
                        <?php for ($i = 0; $i < $nbLang; $i++) {
                            $idLangCheck = $tabLang[$i]['id'];
                            $rgallery = $photo->galleryLang($modId, $idLangCheck); ?>
                            <textarea name="modDescriptionGallery_<?= $idLangCheck; ?>" id="modDescriptionGallery_<?= $idLangCheck; ?>" class="input-full-lang flag<?= $tabLang[$i]['id'];?>" style="background-position:10px 10px;" rows="3" placeholder="Titre de la galerie photos..."><?= $rgallery->description; ?></textarea>
                        <?php } ?>
                    </div>
                    <div>&nbsp;</div>
                    <div class="headerform">Photo de couverture</div>
                    <div class="corpsForm">
                        <div id="photoUploadareaGallery"></div>
                    </div>
                    <div>&nbsp;</div>
                    <div>
                        <input type="hidden" name="modGalleryStart" id="modGalleryStart" value="1" />
                        <input type="hidden" name="modId" id="modId" value="<?= $modId; ?>" />
                    </div>
                            <div>
                                <table style="border:0px;" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td>
                                            <div id="cuButtonRed"><a href="<?= '/manager' . $varLink . '/admin_tree/param_content/' . $idCategory . '/5/0/0/'; ?>" style="color:#FFFFFF;">Fermer</a></div>
                                        </td>
                                        <td>&nbsp;</td>
                                        <td>
                                            <button class="cubutton">Modifiez</button>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                    <div>&nbsp;</div>
                    <!-- /INFO GALERIE FORM -->
                    <!-- /MODIF GALERIE PHOTOS -->
                </form>
            <?php else:  ?>
                <?php if(intval($_GET['status'])==1): ?>
                    <!-- ADD GALERIE PHOTOS -->
                        <form action="<?= '/manager' . $varLink . '/admin_tree/param_content/' . $idCategory . '/5/0/0/'; ?>" method="post" id="galleryAddPhoto" name="galleryAddPhoto" enctype="multipart/form-data">
                            <!-- INFO GALERIE PHOTOS ADD -->
                            <div class="headerform">Ajout d'une galerie : Titre &nbsp;<span class="redb">*</span></div>
                            <div class="corpsForm">
                                <?php for ($i = 0; $i < $nbLang; $i++) {
                                    $idLangCheck = $tabLang[$i]['id'];  ?>
                                    <div><input type="text" name="titleAdd_<?= $idLangCheck; ?>" id="titleAdd_<?= $idLangCheck; ?>" value="" class="input-full-lang flag<?= $tabLang[$i]['id'];?> requiredField" placeholder="Titre de la galerie photos..." /></div>
                                <?php } ?>
                            </div>
                            <div>&nbsp;</div>
                            <div class="headerform">Description : (3 lignes maximum)</div>
                            <div class="corpsForm">
                                <?php for ($i = 0; $i < $nbLang; $i++) {
                                    $idLangCheck = $tabLang[$i]['id']; ?>
                                    <div><textarea name="descriptionAdd_<?= $idLangCheck; ?>" id="descriptionAdd_<?= $idLangCheck; ?>" class="input-full-lang flag<?= $tabLang[$i]['id'];?>" style="background-position:10px 10px;" rows="3" placeholder="Description de la galerie photos..."></textarea></div>
                                    <div>&nbsp;</div>
                                <?php } ?>                                                         
                            </div>
                            <div><input type="hidden" name="addGalleryStart" id="addGalleryStart" value="1" />&nbsp;</div>
                            <div>
                                <table style="border:0px;" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td>
                                            <div id="cuButtonRed"><a href="<?= '/manager' . $varLink . '/admin_tree/param_content/' . $idCategory . '/5/0/0/'; ?>" style="color:#FFFFFF;">Fermer</a></div>
                                        </td>
                                        <td>&nbsp;</td>
                                        <td>
                                            <button class="cubutton">Ajoutez</button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div>&nbsp;</div>
                    <!-- /ADD GALERIE PHOTOS -->
                <?php endif; ?>
            <?php endif; ?>
            <div>&nbsp;</div>
            <div class="headerformglobal">
                <table style="border:0px;" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td style="text-align:left;">Photos <?= (($_SESSION['siteData']['status']==1)? $_SESSION['siteData']['name'] : ''); ?></td>
                        <td style="text-align:right;" height="35"><a href="<?= '/manager' . $varLink . '/admin_tree/param_content/' . $idCategory . '/5/1/0/'; ?>" id="btnadd" class="adminSmallButton" style="color:#ffffff;">Ajoutez une galerie photo</a></td>
                    </tr>
                </table>
            </div>
            <div class="headerform" style=" width:100%;">
                <div id="modifareasort">Classement des photos</div>
            </div>
            <div id="showFolderSort" style="width:100%;display:none;"></div>
                <div class="corpsForm">
                    <table width="100%" style="border:0px;" cellpadding="0" cellspacing="0">
                        <tr>
                            <td>
                                <?php if (count($tabGallery) == 0) {
                                    echo "Aucune galerie n'a &eacute;t&eacute; post&eacute;.";
                                } else {
                                    echo '<ol class="sortable gallery" width:100%;" style="margin-left:-40px;">';
                                    for ($i = 0; $i < count($tabGallery); $i++) {

                                        $id = intval($tabGallery[$i]['idGallery']);

                                        $urlLink = '/manager' . $varLink . '/admin_tree/param_content/'.$idCategory.'/5/0/'.$id.'/';

                                        $imgStatus=($tabGallery[$i]['status']==1)? 'statusOnRed.png' : 'statusOff.png';

                                        $imgGallery=(!empty($tabGallery[$i]['img']))? '/img/gallery/' . $idCategory . '/img/square/' . $tabGallery[$i]['img'] . '?' . rand() : $imgDefaultSquare;

                                        $titleGallery=(!empty($tabGallery[$i]['title']))? $tabGallery[$i]['title'] : 'Sans titre';

                                        echo '<li data-id="'.$id.'" id="list_gallery_'.$id.'">';
    
                                        echo '<div class="item">';

                                        echo '<table style="border:0px;" width="100%" cellspacing="0" cellpadding="0">';
                                        echo '<tr>';
                                        echo '<td width="20">';
                                        echo '<img src="/img/interface/icons/poubelleoff.png" width="16" height="16" style="border:0px;" class="deleteGallery"  id="'.$id.'"/>';
                                        echo '</td>';
                                        echo '<td width="5">&nbsp;</td>';
                                        echo '<td  width="65">';
                                        echo '<img src="'.$imgGallery.'" width="65" style="border:0px;"/>';
                                        echo '</td>';
                                        echo '<td width="5">&nbsp;</td>';
                                        echo '<td style="text-align:left;"><a href="'.$urlLink.'" target="_self">'.$titleGallery.'</a></td>';
                                        echo '<td style="text-align:right;">';
                                        echo '<button id="checkStatusGallery'.$id.'" data-id="'.$id.'" data-table="'.$prefixGallery.'item" data-field="status" data-target-id="id" data-output="red" style="border:0px;background:none;">';
                                        echo '<img src="/img/interface/icons/'.$imgStatus.'" width="13" height="13" style="border:0px;"/>';
                                        echo '</button>';
                                        echo '</td>';
                                        echo '<td width="5">&nbsp;</td>';
                                        echo '</tr>';
                                        echo '</table>';
                                        echo '</div>';

                                        echo '</li>';
                                    }
                                    echo '</ol>';
                                } ?>
                            </td>
                        </tr>
                    </table>
                </div>
            <div>&nbsp;</div>
            <div style="text-align:left;">
                <?php if(count($tabGallery)==0) {echo "";} else {echo "<b>Total de ".count($tabGallery)." galerie(s) photos</b>";} ?>
            </div>
        </div>
    </div>