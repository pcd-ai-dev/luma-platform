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
    <?php if(($_GET['mod']!="0")&&($modStatus=="0")): ?>
        <div class="headerformtoggle" style=" width:100%;">
            <div id="addVideoTarget" class="modifAreaSelector">Ajoutez une vid&eacute;o</div>
        </div>
        <div id="showVideoForm" style="display:none;">&nbsp;</div>
        <div>&nbsp;</div>
        <div class="headerformtoggle" style=" width:100%;">
             <div class="modifAreaSelector">Liste Vid&eacute;os</div>
        </div>
        <div id="showfolder" style="width:100%; margin-top:-10px; display:none;"></div>
        <div>&nbsp;</div>
        <form action="<?= '/manager'.$varLink.'/admin_tree/param_content/'.$idCategory.'/6/0/0/'; ?>" method="post" id="galleryModVideo" name="galleryModVideo" enctype="multipart/form-data">
            <div>&nbsp;</div>
            <div><b>Les champs pr&eacute;c&egrave;d&eacute;s d'un trait rouge sont obligatoires.</b></div>
            <div>&nbsp;</div>
             <div class="headerform">Modification d'une galerie : Titre &nbsp;<span class="redb">*</span></div>
            <div class="corpsForm">
                 <?php for ($i = 0; $i < $nbLang; $i++) {
                    $idLangCheck = $tabLang[$i]['id'];
                    $rgallery = $video->infoGalleryLang($modId, $idLangCheck); ?>
                    <div><input type="text" name="modTitleGallery_<?= $idLangCheck; ?>" id="modTitleGallery_<?= $idLangCheck; ?>" value="<?= $rgallery->title; ?>" class="input-full-lang flag<?= $tabLang[$i]['id'];?> requiredField" placeholder="Titre de la galerie vi&eacute;o..."/></div>
                 <?php } ?>
            </div>
            <div>&nbsp;</div>
            <div class="corpsForm">
            <?php for ($i = 0; $i < $nbLang; $i++) {
                $idLangCheck = $tabLang[$i]['id'];
                $rgallery = $video->infoGalleryLang($modId, $idLangCheck); ?>
                <div><textarea name="modDescriptionGallery_<?= $idLangCheck; ?>" id="modDescriptionGallery_<?= $idLangCheck; ?>" class="input-full-lang flag<?= $tabLang[$i]['id']; ?>" style="background-position:10px 10px;" rows="3" placeholder="Description de la galerie vi&eacute;o..."><?= $rgallery->description; ?></textarea></div>
            <?php } ?>
            </div>
            <div>&nbsp;</div>
            <div class="headerform">Description : (3 lignes maximum)</div>
            <div>&nbsp;</div>
            <div>
                <input type="hidden" name="modGalleryStart" id="modGalleryStart" value="1" />
                <input type="hidden" name="modId" id="modId" value="<?= $modId; ?>" />
            </div>
             <div>
                <table style="border:0px;" cellspacing="0" cellpadding="0">
                    <tr>
                        <td>
                            <div id="cuButtonRed"><a href="<?= '/manager'.$varLink.'/admin_tree/param_content/'.$idCategory.'/6/0/0/'; ?>" style="color:#FFFFFF;">Fermer</a></div>
                        </td>
                        <td>&nbsp;</td>
                        <td>
                            <button class="cubutton">Modifiez</button>
                        </td>
                    </tr>
                </table>
            </div>
        </form>
    <?php else:  ?>
        <?php if ($statusForm): ?>
            <form action="<?= '/manager'.$varLink.'/admin_tree/param_content/'.$idCategory.'/6/0/0/'; ?>" method="post" id="galleryAddVideo" name="galleryAddVideo" enctype="multipart/form-data">
                <div>&nbsp;</div>
                <div><b>Les champs pr&eacute;c&egrave;d&eacute;s d'un trait rouge sont obligatoires.</b></div>
                <div>&nbsp;</div>
                <div class="headerform">Ajout d'une galerie : Titre &nbsp;<span class="redb">*</span></div>
                <div class="corpsForm">
                    <?php for ($i = 0; $i < $nbLang; $i++) {
                        $idLangCheck = $tabLang[$i]['id'];  ?>
                        <input type="text" name="titleAdd_<?= $idLangCheck; ?>" id="titleAdd_<?= $idLangCheck; ?>" value="" class="input-full-lang flag<?= $tabLang[$i]['id'];?> requiredField" placeholder="Titre de la galerie vid&eacute;o..." />
                    <?php } ?>
                </div>
                <div>&nbsp;</div>
                <div class="headerform">Description : (3 lignes maximum)</div>
                <div class="corpsForm">
                    <?php for ($i = 0; $i < $nbLang; $i++) {
                        $idLangCheck = $tabLang[$i]['id']; ?>
                        <div><textarea name="descriptionAdd_<?= $idLangCheck; ?>" id="descriptionAdd_<?= $idLangCheck; ?>" class="input-full-lang flag<?= $tabLang[$i]['id'];?>" rows="3"  style="background-position:10px 10px;" placeholder="Description de la galerie vid&eacute;o..."></textarea></div>
                    <?php } ?>
                </div>
                <div>&nbsp;</div>
                <div>
                    <table style="border:0px;" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <div id="cuButtonRed"><a href="<?= '/manager'.$varLink.'/admin_tree/param_content/'.$idCategory.'/6/0/0/'; ?>" style="color:#FFFFFF;">Fermer</a></div>
                            </td>
                            <td>&nbsp;</td>
                            <td>
                                <input type="hidden" name="addGalleryStart" id="addgallerystart" value="1" />
                                    <button class="cubutton">Ajoutez</button>
                            </td>
                        </tr>
                    </table>
                </div>
            </form>
        <?php endif; ?>
    <?php endif; ?>
    <div>&nbsp;</div>
    <div class="headerformglobal">
        <table style="border:0px;" cellpadding="0" cellspacing="0" width="100%">
             <tr>
                <td style="text-align:left;">Work <?= (($_SESSION['siteData']['status']==1)? $_SESSION['siteData']['name'] : ''); ?></td>
                <td style="text-align:right;" height="35"><a href="<?= '/manager'.$varLink.'/admin_tree/param_content/'.$idCategory.'/6/1/0/'; ?>" id="btnadd" class="adminSmallButton" style="color:#ffffff;">Ajoutez une galerie vid&eacute;o</a></td>
            </tr>
        </table>
    </div>
    <div class="headerformtoggle" style=" width:100%;">
        <div class="modifAreaSelector">Classement des vid&eacute;os</div>
    </div>
    <div id="showFolderSort" style="width:100%; display:none;"></div>
    <div class="corpsForm">
        <table width="100%" style="border:0px;" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <?php if ($nb == 0) {
                            echo "Aucune galerie n'a &eacute;t&eacute; post&eacute;e.";
                        } else {
                            echo '<ol class="sortable gallery" width:100%;" style="margin-left:-40px;">';

                            for ($i = 0; $i < $nb; $i++) {

                                $id = intval($tab[$i]['idGallery']);
                                $urlLink = '/manager'.$varLink.'/admin_tree/param_content/'.$idCategory.'/6/1/'.$id.'/';
                                $titleGallery=(!empty($tab[$i]['title']))? $tab[$i]['title'] : 'Sans titre';
                                $imgStatus=($tab[$i]['status']==1)? 'statusOnRed.png' : 'statusOff.png';
                                $imgGallery = $imgDefaultSquare;

                                echo '<li data-id="'.$id.'" id="list_gallery_'.$id.'">';
                                echo '<div class="item">';
                                echo '<table style="border:0px;" width="100%" cellspacing="0" cellpadding="0">';
                                echo '<tr>';
                                echo '<td width="20">';
                                echo '<img src="/img/interface/icons/poubelleoff.png" width="16" height="16" style="border:0px;" class="deleteGallery"  id="' . $id . '"/>';
                                echo '</td>';
                                echo '<td width="5">&nbsp;</td>';
                                echo '<td  width="65">';
                                echo '<img src="'.$imgGallery.'" width="65" style="border:0px;"/>';
                                echo '</td>';
                                echo '<td width="5">&nbsp;</td>';
                                echo '<td style="text-align:left;"><a href="'.$urlLink.'" target="_self">'.$titleGallery.'</a></td>';
                                echo '<td style="text-align:right;">';
                                echo '<button id="checkStatusGallery'.$id.'" data-id="'.$id.'" data-table="'.$prefixVideo.'gallery" data-field="status" data-target-id="id" data-output="red" style="border:0px;background:none;">';
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
                        }
                    ?>
                </td>
            </tr>
        </table>
    </div>
     <div>&nbsp;</div>
    <div style="text-align:left;">
        <?php if ($nb == 0) {echo "";} else {echo "<b>Total de " . $nb . " galerie(s)</b>";} ?>
    </div>
</div>
<!-- /Content -->