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
        <div id="contentContainer" style="display:block;width:96%; margin-bottom:100px;padding-top:10px;padding-left:15px;">
            <div>&nbsp;</div>
            <div>&nbsp;</div>
            <!-- MODIF LANGUAGE -->
            <?php if ((intval($_GET['mod']) != "0") && ($modStatus == "0")) : ?>
                <!-- INFO GALERIE FORM MOD -->
                <form action="<?= '/manager'.$varLink.'/admin_root/param_content/'.$idSite.'/2/'.$statusForm.'/0/'; ?>" method="post" id="modLangForm" name="modLangForm" enctype="multipart/form-data">
                    <div class="headerform">Modification d'une langue : Titre &nbsp;<span class="redb">*</span></div>
                    <div class="corpsForm">
                        <div><input type="text" name="modNameLanguage" id="modNameLanguag" value="<?= $rLang->name; ?>" class="input-full requiredField" placeholder="Nom de la langue..."/></div>
                        <div><input type="text" name="modCodeLanguage" id="modCodeLanguag" value="<?= $rLang->code; ?>" class="input-full" placeholder="Code de la langue..."/></div>
                    </div>
                    <div>&nbsp;</div>
                    <div class="headerform">Image</div>
                    <div class="corpsForm">
                        <div id="imgUploadArea"></div>
                    </div>
                    <div>&nbsp;</div>
                    <div>
						<table style="border:0px;" cellspacing="0" cellpadding="0">
							<tr>
								<td>
									<div id="cuButtonRed"><a href="<?= '/manager' . $varLink . '/admin_root/param_content/' . $idSite . '/2/0/0/'; ?>" style="color:#FFFFFF;">Fermer</a></div>
								</td>
								<td>&nbsp;</td>
								<td>
                                    <input type="hidden" name="modLanguageStart" id="modLanguageStart" value="1" />
                                    <input type="hidden" name="modid" id="modid" value="<?= $modId; ?>" />
                                    <button class="cubutton">Modifiez</button>
                                </td>
							</tr>
						</table>
					</div>
                    <div>&nbsp;</div>
                     <div>&nbsp;</div>
                    <!-- /INFO LANGUAGE FORM -->
                    <!-- /MODIF LANGUAGE -->
                </form>
            <?php else:  ?>
                <?php if(intval($_GET['status'])==1): ?>
                    <!-- ADD GALERIE PHOTOS -->
                        <form action="<?= '/manager' . $varLink . '/admin_root/param_content/' . $idSite . '/2/0/0/'; ?>" method="post" id="addLangForm" name="addLangForm" enctype="multipart/form-data">
                            <!-- INFO LANGUAGE ADD -->
                            <div class="headerform">Ajout d'une langue : Titre &nbsp;<span class="redb">*</span></div>
                            <div class="corpsForm">
                                <div><input type="text" name="nameAdd" id="nameAdd" value="" class="input-full requiredField" placeholder="Nom de la langue..." /></div>
                                <div><input type="text" name="codeAdd" id="codeAdd" value="" class="input-full" placeholder="Code de la langue..." /></div>
                            </div>
                            <div>&nbsp;</div>
                            <div>&nbsp;</div>
                            <div>
                                <table style="border:0px;" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td>
                                            <div id="cuButtonRed"><a href="<?= '/manager' . $varLink . '/admin_root/param_content/' . $idSite . '/2/0/0/'; ?>" style="color:#FFFFFF;">Fermer</a></div>
                                        </td>
                                        <td>&nbsp;</td>
                                        <td>
                                            <input type="hidden" name="addLanguageStart" id="addLanguageStart" value="1" />
                                             <button class="cubutton">Ajoutez</button>
                                        </td>
                                    </tr>
                                </table>   
                            </div>
                        </form>
                    <!-- /ADD LANGUAGE -->
                <?php endif; ?>
            <?php endif; ?>
            <div>&nbsp;</div>
            <div class="headerformglobal">
                <table style="border:0px;" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td style="text-align:left;">Langues <?= (($_SESSION['siteData']['status']==1)? $_SESSION['siteData']['name'] : ''); ?></td>
                        <td style="text-align:right;" height="35"><a href="<?= '/manager' . $varLink . '/admin_root/param_content/' . $idSite . '/2/1/0/'; ?>" id="btnadd" class="adminSmallButton" style="color:#ffffff;">Ajoutez une langue</a></td>
                    </tr>
                </table>
            </div>
                <div class="corpsForm">
                    <table width="100%" style="border:0px;" cellpadding="0" cellspacing="0">
                        <tr>
                            <td>
                                <?php if (count($tabLang) == 0) {
                                    echo "Aucune langue n'a &eacute;t&eacute; post&eacute;.";
                                } else {
                                    echo '<ol class="sortable language" style="margin-left:-40px; width:100%;">';
                                    for ($i = 0; $i < count($tabLang); $i++) {

                                        $id = intval($tabLang[$i]['id']);

                                        $photoshow = (isset($rphoto->url)) ? $rphoto->url : '';

                                        $urlLink = '/manager' . $varLink . '/admin_root/param_content/' . $idSite . '/2/' . $statusForm . '/' . $id . '/';

                                        $imgStatus = ($tabLang[$i]['status'] == '1')? 'statusOnGreen.png' : 'statusOff.png';

                                        $imgLanguage = (empty($tabLang[$i]['img']))? $imgDefaultSquare : '/img/l/'.$tabLang[$i]['img'].'?'. rand();

                                        $nameLanguage = (empty($tabLang[$i]['name']))? 'Sans titre' : $tabLang[$i]['name'];

                                        echo '<li data-id="'.$id.'" id="list_site_'.$id.'">';
						                echo '<div class="item">';
                                        echo '<table style="border:0px;" width="100%" cellspacing="0" cellpadding="0">';
                                        echo '<tr>';
                                        echo '<td width="20">';
                                        echo '<img src="/img/interface/icons/poubelleoff.png" width="16" height="16" style="border:0px;" class="deletepost"  id="'.$id.'"/>';
                                        echo '</td>';
                                        echo '<td width="5">&nbsp;</td>';
                                        echo '<td  width="65">';
                                        echo '<img src="'.$imgLanguage.'" width="35" style="border:0px;"/>';
                                        echo '</td>';
                                        echo '<td width="5">&nbsp;</td>';
                                        echo '<td style="text-align:left;"><a href="'.$urlLink.'" target="_self">'.$nameLanguage . '</a></td>';

                                        echo '<td style="text-align:right;">';
                                        echo '<button id="checkStatusGallery'.$id.'" data-id="'.$id.'" data-table="'.$prefixRoot.'lang" data-field="status" data-target-id="id" data-output="red" style="border:0px;background:none;">';
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
            <div>&nbsp;</div>
            <div>&nbsp;</div>
        </div>
    </div>