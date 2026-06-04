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

<!-- Début mofif entete page -->
<form action="<?= '/manager'.$varLink.'/admin_tree/param_content/'.$idCategory.'/3/0/0/'; ?>" method="post" id="info" name="info" enctype="multipart/form-data">
  <table width="95%" cellpadding="0" cellspacing="0" style="padding: 20px;border:0px;">
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td><table width="100%" cellpadding="0" cellspacing="0" style="border:0px;">
          <?php for($i=0; $i<$nbLang;$i++){ $idLangCheck=$tabLang[$i]['id']; $repage=$page->infopage($idCategory,$idLangCheck); ?>
          <tr>
            <td><div id="imglang"><img src="/img/l/<?= $tabLang[$i]['id'];?>.svg" width="21" /></div>
            <div class="corpsForm" style="text-align: center;">
              <textarea name="textcorps_<?= $idLangCheck;?>" id="textcorps_<?= $idLangCheck;?>"><?php if($repage->text){echo $repage->text;}else{} ?></textarea>
              </div>
              <script type="text/javascript">
					  <!--
					 CKEDITOR.replace( 'textcorps_<?= $idLangCheck;?>', {
						enterMode: CKEDITOR.ENTER_DIV,
						width:'100%',
						removePlugins : 'resize',
						extraPlugins : 'autogrow',
						autoGrow_maxHeight : 800,
					 });
					 -->
					</script></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <?php } ?>
        </table></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;
        <input type="hidden" name="modifBodytart" id="modifBodyStart" value="1" />
        <input type="hidden" name="idCategory" id="idCategory"  value="<?= $r?->category;?>"/></td>
    </tr>
    <tr>
      <td><button class="cubutton">Modifiez</button></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
  </table>
</form>
<!-- Fin mofif entete page -->