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

<!-- Début mofif post -->
<div style="width:95%;border:0px;padding:20px;">
  <?php if(($modId!="0")&&($modStatus=="0")) { ?>
  <form action="<?= '/manager'.$varLink.'/admin_tree/param_content/'.$idCategory.'/11/0/0/'; ?>" method="post" id="contactModForm" name="contactModForm" enctype="multipart/form-data">
    <div>&nbsp;</div>
    <div>&nbsp;</div>
    <div class="headerform">Modification d'un contact : Titre  &nbsp;<span class="redb">*</span></div>
    <div class="corpsForm">
      <?php
        for($i=0; $i<$nbLang;$i++){ $idLangCheck=$tabLang[$i]['id']; $rContact=$contact->contactLang($modId,$idLangCheck); ?>
          <div><input type="text" name="modTitleContact_<?= $idLangCheck;?>" id="modTitleContact_<?= $idLangCheck;?>" value="<?= $rContact->title; ?>" class="input-full-lang flag<?= $idLangCheck;?> requiredField" placeholder="Nom du contact..."/></div>
        <?php } ?>
      </div>
      <div>&nbsp;</div>
      <div class="headerform">Description : (3 lignes maximum)</div>
      <div class="corpsForm">
          <?php
            for($i=0; $i<$nbLang;$i++){ $idLangCheck=$tabLang[$i]['id']; $rContact=$contact->contactLang($modId,$idLangCheck); ?>
                <div><textarea name="modDescriptionContact_<?= $idLangCheck;?>" id="modDescriptionContact_<?= $idLangCheck;?>" rows="4" class="input-full-lang flag<?= $idLangCheck;?>" style="background-position:10px 10px;" placeholder="Description du contact..."><?= $rContact->description; ?></textarea></div>
          <?php } ?>
      </div>
      <div>&nbsp;</div>
      <div class="headerform">Compl&eacute;ments</div>
      <div class="corpsForm">
        <table width="100%" cellspacing="0" cellpadding="0" style="border:0;">
            <tr>
              <th>E-mail :</th>
              <td>&nbsp;</td>
              <td><input type="text" name="modEmail" id="modEmail" value="<?= $modEmail; ?>" class="input-normal" placeholder="Email..."/></td>
              <td>&nbsp;</td>
              <th>T&eacute;l&eacute;phone :</th>
              <td>&nbsp;</td>
              <td><input type="text" name="modPhone" id="modPhone" value="<?= $modPhone; ?>" class="input-normal" placeholder="t&eacute;l&eacute;phone..."/></td>
              <td>&nbsp;</td>
            </tr>
        </table>
      </div>
      <div>
        <input type="hidden" name="modContactStart" id="modContactStart" value="1" />
        <input type="hidden" name="modId" id="modId" value="<?= $modId; ?>" />
      </div>
      <div>&nbsp;</div>
      <div>&nbsp;</div>
      <div>
				<table style="border:0px;" cellspacing="0" cellpadding="0">
					<tr>
						<td>
							<div id="cuButtonRed"><a href="<?= '/manager'.$varLink.'/admin_tree/param_content/'.$idCategory.'/11/0/0/'; ?>" style="color:#FFFFFF;">Fermer</a></div>
						</td>
						<td>&nbsp;</td>
						<td><button class="cubutton" >Modifiez</button></td>
					</tr>
				</table>
			</div>
      <div>&nbsp;</div>
      <div>&nbsp;</div>
      <div>&nbsp;</div>
  </form>
  <?php }else { ?>
  <div <?php if($statusForm=='1'){echo '';}else{echo 'style="display:none;"';}?>>
    <form action="<?= '/manager'.$varLink.'/admin_tree/param_content/'.$idCategory.'/11/0/0/'; ?>" method="post" id="contactAddForm" name="contactAddForm" enctype="multipart/form-data">
      <div>&nbsp;</div>
      <div class="headerform">Ajout d'un contact : Titre &nbsp;<span class="redb">*</span></div>
      <div class="corpsForm">
          <?php for($i=0; $i<$nbLang;$i++){ $idLangCheck=$tabLang[$i]['id'];  ?>
            <div><input type="text" name="titleAdd_<?= $idLangCheck;?>" id="titleAdd_<?= $idLangCheck;?>" value="" class="input-full-lang flag<?= $idLangCheck;?> requiredField" placeholder="Nom du contact..."/></div>
           <?php } ?>
      </div>
      <div>&nbsp;</div>
      <div class="headerform">Description : (3 lignes maximum)</div>
      <div class="corpsForm">
          <?php for($i=0; $i<$nbLang;$i++){ $idLangCheck=$tabLang[$i]['id']; ?>
             <div><textarea name="descriptionAdd_<?= $idLangCheck;?>" rows="4" class="input-full-lang flag<?= $idLangCheck;?>" id="descriptionAdd_<?= $idLangCheck;?>" style="background-position:10px 10px;" placeholder="Description du contact..."></textarea></div>
          <?php } ?>
      </div>
      <div>&nbsp;</div>
      <div class="headerform">Compl&eacute;ments</div>
      <div class="corpsForm">
        <table width="100%" cellspacing="0" cellpadding="0" style="border:0px;">
          <tr>
            <th>E-mail :</th>
            <td>&nbsp;</td>
            <td><input type="text" name="email" id="email" value="" class="input-normal" placeholder="Email..."/></td>
            <td>&nbsp;</td>
            <th>T&eacute;l&eacute;phone :</th>
            <td>&nbsp;</td>
            <td><input type="text" name="phone" id="phone" value="" class="input-normal" placeholder="Tl&eacute;phone..."/></td>
            <td>&nbsp;</td>
          </tr>
        </table>
      </div>
      <div><input type="hidden" name="addContactStart" id="addContactStart" value="1" /></div>
      <div>&nbsp;</div>
      <div>&nbsp;</div>
      <div>
				<table style="border:0px;" cellspacing="0" cellpadding="0">
					<tr>
						<td>
							<div id="cuButtonRed"><a href="<?= '/manager'.$varLink.'/admin_tree/param_content/'.$idCategory.'/11/0/0/'; ?>" style="color:#FFFFFF;">Fermer</a></div>
						</td>
						<td>&nbsp;</td>
						<td><button class="cubutton">Ajoutez</button></td>
					</tr>
				</table>
			</div>
      <div>&nbsp;</div>
      <div>&nbsp;</div>
      <div>&nbsp;</div>
    </form>
  </div>
  <?php } ?>
  <div>&nbsp;</div>
  <form action="<?= '/manager'.$varLink.'/admin_tree/param_content/'.$idCategory.'/11/0/0/'; ?>" method="post" id="ContactListForm" name="ContactListForm" enctype="multipart/form-data">
    <div class="headerform">
      <div style=" margin-top:2px;">
        <table width="100%" cellpadding="0" cellspacing="0" style="border:0px;text-align: left;">
          <tr>
            <td style="text-align: left;"><a href="<?=  '/manager'.$varLink.'/admin_tree/param_content/'.$idCategory.'/11/1/0/'; ?>"  class="adminSmallButton" style="color:#ffffff;">Ajoutez une contact</a></td>
          </tr>
        </table>
       </div>
      </div>
      <div>&nbsp;</div>
      <div class="corpsForm">
        <?php 
          if ($nb==0){
            echo "Aucun contact n'a &eacute;t&eacute; post&eacute;.";
          }else{ 
            echo '<ol class="sortable contact" style="margin-left:-40px; width:100%;">';

            for($i=0;$i<$nb;$i++){

							$id = $tab[$i]['idContact']; 
							$urlLink = '/manager'.$varLink.'/admin_tree/param_content/'.$idCategory.'/11/'.$statusForm.'/'.$id.'/';
              $imgStatus = ($tab[$i]['status']=='1')? 'statusOnRed.png' : 'statusOff.png';
              $contactName=(!empty($tab[$i]['title']))? $tab[$i]['title'] : 'Sans nom';

              echo '<li data-id="'.$id.'" id="list_contact_'.$id.'">';
              echo '<div class="item">';
              echo '<table style="border:0px;" width="100%" cellspacing="0" cellpadding="0">';
              echo '<tr>';
              echo '<td width="20">';
              echo '<img src="/img/interface/icons/poubelleoff.png" width="16" height="16" style="border:0px;" class="deleteContact"  id="'.$id.'"/>';
              echo '</td>';;
              echo '<td width="5">&nbsp;</td>';
              echo '<td style="text-align:left;"><a href="'.$urlLink.'" target="_self">'.$contactName.'</a></td>';
              echo '<td style="text-align:right;">';
              echo '<button id="checkStatusGallery'.$id.'" data-id="'.$id.'" data-table="'.$prefixContact.'item" data-field="status" data-target-id="id" data-output="red" style="border:0px;background:none;">';
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
      </div>
  </form>
  <div>&nbsp;</div>
  <div>&nbsp;</div>
  <div><?= ($nb==0)? '' : '<b>Total de '.$nb.' contact(s)</b>'; ?></div>
  <div>&nbsp;</div>
  <div>&nbsp;</div>
  <div>&nbsp;</div>
  <div>&nbsp;</div>
  <div>&nbsp;</div>
  <div>&nbsp;</div>
</div>
<!-- Fin mofif post --> 