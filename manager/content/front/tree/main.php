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
<div style="width:95%;border:0px;padding:20px;">
  <div>&nbsp;</div>
  <div><b>Remplissez les champs pr&eacute;c&egrave;d&eacute;s d'un trait rouge sont obligatoires.</b></div>
  <div>&nbsp;</div>
  <form action="<?= '/manager'.$varLink.'/admin_tree/param_content/'.$idCategory.'/1/'.$statusForm.'/0/'; ?>" method="post" id="mainForm" name="mainForm" enctype="multipart/form-data">
    <div class="headerform">Nom de la page</div>
    <div class="corpsForm">
      <?php for($i=0; $i<$nbLang;$i++){ $idLangCheck=$tabLang[$i]['id']; $rcat=$page->infoPage($idCategory,$idLangCheck); ?>
        <input type="text" name="name_<?= $idLangCheck;?>" id="name_<?= $idLangCheck;?>" value="<?= (isset($rcat->name)? $rcat->name : '');?>" class="input-full-lang flag<?= $tabLang[$i]['id'];?> requiredField" placeholder="Nom de la page..."/>
      <?php } ?>
    </div>
    <div>&nbsp;</div>
    <div>&nbsp;</div>
    <!-- TYPE -->
    <div class="headerform">Type de page</div>
    <div class="corpsForm">
      <table style="border:0px;" cellspacing="0" cellpadding="0">
        <tr>
          <td style="text-align:left;">
            <div class="dropdown">
              <select name="type" id="type" class="requiredField">
                <option value="0">S&eacute;lectionnez ...</option>
                <option value="1" <?= ($r?->type=="1")? 'selected="selected"' : ''; ?>>Page Builder</option>
						    <option value="2" <?= ($r?->type=="2")? 'selected="selected"' : ''; ?>>Page normal</option>
                <option value="3" <?= ($r?->type=="3")? 'selected="selected"' : ''; ?>>Page article</option>
              </select>
            </div>
          </td>
          <?php if($r?->type=="1"){ ?>
            <td>&nbsp;&nbsp;&nbsp;</td>
            <th>Option :&nbsp;</th>
            <td>&nbsp;</td>
            <td>
              <div class="dropdown">
                  <select name="switch" id="switch" class="requiredField">
                    <?php
                      $current = $string->setInt($r?->switch);

                      $options = [
                          "0" => "Builder",
                          "1" => "Builder/HomePage",
                          "2" => "Builder/Articles",
                          "3" => "Builder/Page/Article",
                          "5" => "Builder/Photos",
                          "6" => "Builder/Vidéos",
                          "11" => "Builder/Contact",
                          "12" => "Builder/Map",
                      ];

                      foreach ($options as $value => $label) {
                          $selected = ($current == $value) ? 'selected' : '';
                          echo "<option value=\"$value\" $selected>$label</option>";
                      }
                    ?>
                  </select>
                </div>
              </td>
              <td>&nbsp;</td>
          <?php } ?>
        </tr>
      </table>
    </div>
    <!-- /TYPE -->
    <div>&nbsp;</div>
    <div>&nbsp;</div>
    <!-- REVISIONS -->
    <?php if ($r?->type == "1"): ?>
     
      <?php foreach ($tabLang as $lang): 
          $idLangRev = $lang['id'];
          $revData = $page->infoRevisionByCategory($idCategory, $idLangRev);

          if (empty($revData)) continue;
      ?>
      <div class="headerform"><span class="flag<?= $idLangRev; ?>"></span>Révisions - <?= $lang['name']; ?></div>
      <div class="corpsForm">
        <table style="border:0;width:100%;" cellspacing="0" cellpadding="0">
            <tr>
                <th>Versions :&nbsp;</th>
                <td>
                  <div class="dropdown">
                    <select id="revision_<?= $idLangRev; ?>"  name="revision_<?= $idLangRev; ?>">
                      <option value="">Sélectionnez ..</option>
                      <?php foreach ($revData as $rev): 
                        $date = strtotime($rev['date']);
                        $dateshow = date("d.m.Y", $date). " à ".date("H\hi", $date);
                      ?>
                      <option value="<?= $rev['id']; ?>">Version du <?= $dateshow; ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </td>
                <td style="width:40px;"></td>
                <th>Aperçu :&nbsp;</th>
                <td>
                  <div class="dropdown">
                    <select id="view_<?= $idLangRev; ?>" name="view_<?= $idLangRev; ?>" onchange="if(this.value) window.open(this.value, '_blank');">                
                      <option value="">Sélectionnez ..</option>
                      <?php foreach ($revData as $rev): 
                        $date = strtotime($rev['date']);
                        $dateshow = date("d.m.Y", $date) . " à " . date("H\hi", $date);
                        $url = "/revision/$idSite/$idLangRev/{$rev['id']}/";
                      ?>
                      <option value="<?= $url; ?>">Version du <?= $dateshow; ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </td>
              </tr>
            </table>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
    <!-- /REVISIONS -->
    <div>&nbsp;</div>
    <div>&nbsp;</div>
    <!-- LINK -->
    <div class="headerform">Lien optionnel</div>
    <div class="corpsForm">
      <input type="text" name="link" id="link" value="<?= $r?->link; ?>" class="input-full" placeholder="Lien externe..."/>
    </div>
    <!-- /LINK -->
    <div>&nbsp;</div>
    <div>&nbsp;</div>
    <!-- CHILD SELECTION -->
    <?php if(count($rc)!=0):?>
      <div class="headerform">Autres</div>
      <div class="corpsForm">
        <table style="border:0;" cellspacing="0" cellpadding="0">
        <?php 
        $currentPage = $r->idPage;
        $defaultChecked = ($rc[0]['ckid'] == $currentPage) ? '' : 'checked';
        ?>
        <tr>
            <td>
                <label class="containerRB">
                    Aucune
                    <input type="radio" name="idPage" value="" <?= $defaultChecked; ?>>
                    <span class="checkmarkRB"></span>
                </label>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <?php foreach ($rc as $item): 
            $checked = ($item['ckid'] == $currentPage) ? 'checked' : '';
        ?>
        <tr>
            <td>
                <label class="containerRB">
                    <?= $item['name']; ?>
                    <input type="radio" 
                          name="idPage" 
                          value="<?= $item['ckid']; ?>" 
                          <?= $checked; ?>>
                    <span class="checkmarkRB"></span>
                </label>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <?php endforeach; ?>
        </table>
      </div>
    <?php endif; ?>
    <!-- /CHILD SELECTION -->
    <div>&nbsp;</div>
    <div>
      <input type="hidden" name="modCategoryStart" id="modCategoryStart" value="1" />
      <input type="hidden" name="idCategory" id="idCategory" value="<?= $idCategory; ?>" />
      <input type="hidden" name="checkPosition" id="checkPosition" value="<?= $r->position; ?>" />
      <input type="hidden" name="parent" id="parent" value="<?= $r->parent; ?>" />
    </div>
    <div><button class="cubutton">Modifiez</button></div>
    <div>&nbsp;</div>
    <div>&nbsp;</div>
    <div>&nbsp;</div>
    <div>&nbsp;</div>
    <div>&nbsp;</div>
    <div>&nbsp;</div>
    <div>&nbsp;</div>
    <div>&nbsp;</div>
  </form>
</div>