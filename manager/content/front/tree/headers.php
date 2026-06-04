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
  <form action="<?= '/manager'.$varLink.'/admin_tree/param_content/'.$idCategory.'/2/'.$statusForm.'/0/'; ?>" method="post" id="headerForm" name="headerForm" enctype="multipart/form-data" style="border:0px;">
    <div>&nbsp;</div>
    <div><b>Les champs pr&eacute;c&egrave;d&eacute;s d'un trait rouge sont obligatoires.</b></div>
    <div>&nbsp;</div>
    <!-- TITLE -->
    <div class="headerform">Titre de la page (Meta) &nbsp;<span class="redb">*</span></div>
    <div class="corpsForm">
      <?php for($i=0; $i<$nbLang;$i++){ $idLangCheck=$tabLang[$i]['id']; $repage=$page->infoPage($idCategory,$idLangCheck); ?>
        <input type="text" name="title_<?= $idLangCheck;?>" id="title_<?= $idLangCheck;?>" value="<?= $repage?->title;?>" class="input-full-lang flag<?= $tabLang[$i]['id'];?> requiredField" placeholder="Titre de la page..."/>
      <?php } ?>
    </div>
    <!-- /TITLE -->
    <div>&nbsp;</div>
    <div>&nbsp;</div>
    <!-- DESCRIPTION -->
    <div class="headerform">Description de la page (Meta)</div>
    <div class="corpsForm">
      <?php for($i=0; $i<$nbLang;$i++){ $idLangCheck=$tabLang[$i]['id']; $repage=$page->infoPage($idCategory,$idLangCheck); ?>
        <textarea name="description_<?= $idLangCheck;?>" rows="4" class="input-full-lang flag<?= $tabLang[$i]['id'];?>" placeholder="Description de la page..." style="background-position:10px 10px;"><?= $repage?->description;?></textarea>
      <?php } ?>
    </div>
    <!-- /DESCRIPTION -->
    <div>&nbsp;</div>
    <div>&nbsp;</div>
    <!-- KEYWORD -->
    <div class="headerform">Mots clefs (Meta)</div>
    <div class="corpsForm">
      <?php for($i=0; $i<$nbLang;$i++){
        $idLangCheck=$tabLang[$i]['id']; $repage=$page->infoPage($idCategory,$idLangCheck); ?>
        <textarea name="keywords_<?= $idLangCheck;?>" rows="4" class="input-full-lang flag<?= $tabLang[$i]['id'];?>" placeholder="Mots clefs de la page..." style="background-position:10px 10px;"><?= $repage?->keywords;?></textarea>
        <?php } ?>
    </div>
    <!-- /KEYWORD -->
    <div>&nbsp;</div>
    <div>&nbsp;</div>
    <!-- VERBATIM -->
    <div class="headerform" style="display:none;">Verbatim</div>
    <div class="corpsForm" style="display:none;">
      <?php for($i=0; $i<$nbLang;$i++){
        $idLangCheck=$tabLang[$i]['id']; $repage=$page->infoPage($idCategory,$idLangCheck); ?>
        <textarea name="verbatim_<?= $idLangCheck;?>" id="verbatim_<?= $idLangCheck;?>" rows="1" class="input-full-lang flag<?= $tabLang[$i]['id'];?>"><?= (isset($repage->verbatim)? $repage->verbatim : '' );?></textarea>
        <script type="text/javascript">
          CKEDITOR.replace( 'verbatim_<?= $idLangCheck;?>',
          {toolbarGroups: [
            {"name": "basicstyles", "groups": ["basicstyles"]},
            {name: 'document',	   groups: [ 'mode' ] },
            {name: 'links' }
          ],
          width : '100%',
          extraPlugins : 'autogrow',
          autoGrow_maxHeight : 800,
          removePlugins : 'resize',
          enterMode: CKEDITOR.ENTER_DIV,
          on: {
            paste: function(evt) {
               var editor = evt.editor;
               if (evt.data.dataValue.match(/object/)) {
                  evt.data.dataValue = evt.data.dataValue.replace('&lt;', '<').replace('&gt;', '>');
                  var element = CKEDITOR.dom.element.createFromHtml(evt.data.dataValue);
                  editor.insertElement(element);
                }
              }
           }});
        </script>
        <?php } ?>
    </div>
    <!-- /VERBATIM -->
    <div style="display:none;">&nbsp;</div>
    <div style="display:none;">&nbsp;</div>
    <!-- SLOGAN -->
    <div class="headerform" style="display:none;">Titre Slogan</div>
    <div class="corpsForm" style="display:none;">
      <?php for($i=0; $i<$nbLang;$i++){ $idLangCheck=$tabLang[$i]['id']; $repage=$page->infoPage($idCategory,$idLangCheck); ?>
      <input type="text" name="dataTitle_<?= $idLangCheck;?>" id="dataTitle_<?= $idLangCheck;?>" value="<?= (isset($repage->dataTitle)? $repage->dataTitle : '');?>" class="input-full-lang flag<?= $tabLang[$i]['id'];?>"/>
      <?php } ?>
    </div>
    <!-- /SLOGAN -->
    <div style="display:none;">&nbsp;</div>
    <div style="display:none;">&nbsp;</div>
    <!-- SLOGAN TEXT -->
    <div class="corpsForm" style="display:none;">
      <?php for($i=0; $i<$nbLang;$i++){ $idLangCheck=$tabLang[$i]['id']; $repage=$page->infoPage($idCategory,$idLangCheck); ?>
        <input type="text" name="dataText_<?= $idLangCheck;?>" id="dataText_<?= $idLangCheck;?>" value="<?= (isset($repage->dataText)? $repage->dataText: '');?>" class="input-full-lang flag<?= $tabLang[$i]['id'];?>"/>
      <?php } ?>
    </div>
    <!-- /SLOGAN TEXT -->
    <div>&nbsp;</div>
    <div>&nbsp;</div>
    <!-- IMAGE -->
    <div class="headerform">Photo de couverture</div>
    <div class="corpsForm">
      <div id="photoUploadPageArea"></div>
    </div>
    <!-- /IMAGE -->
    <div>&nbsp;</div>
    <div>&nbsp;</div>
    <!-- OPTIONS -->
    <div class="headerform">Options</div>
    <div class="corpsForm">
      <table style="border:0px;">
          <tr>
            <td>
              <label for="paramPage">Param&egrave;tre</label>
            </td>
            <td>
              <input type="text" id="paramPage" name="paramPage" class="input-full" value="<?= $r?->paramPage;?>" style="width:250px;"/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </td>
            <td>
              <label for="color"><b>Fond</b> &nbsp;&nbsp;</label>
            </td>
            <td><input type="text" id="color" name="color" data-coloris value="<?= $r?->color;?>" class="colorInput"/></td>
            <td><label for="textColor">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Texte</b>&nbsp;&nbsp;</label></td><td><input type="text" id="colorTxt" name="colorTxt" data-coloris value="<?= $r?->colorTxt;?>" class="colorInput" /></td>
          </tr>
      </table>
      </div>
    <!-- /OPTIONS -->
    <div>&nbsp;</div>
    <div>&nbsp;</div>
    <!-- SUBMIT -->
    <div>
        <input type="hidden" name="modHeaderStart" id="modHeaderStart" value="1" />
        <input type="hidden" name="idCategory" id="idCategory"  value="<?= $r?->idCategory;?>"/>
    </div>
    <div><button class="cubutton">Modifiez</button></div>
    <!-- /SUBMIT -->
  </form>
  <div>&nbsp;</div>
  <div>&nbsp;</div>
  <div>&nbsp;</div>
  <div>&nbsp;</div>
  <div>&nbsp;</div>
  <div>&nbsp;</div>
  <div>&nbsp;</div>
   <div>&nbsp;</div> 
</div>