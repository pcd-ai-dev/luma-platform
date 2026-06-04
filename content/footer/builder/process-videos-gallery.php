<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */
 
 
	//---------------------------------------------------------  
	// FRONT VIDEO GALLERY
	//--------------------------------------------------------- 

?>

<!-- JS -->
<script language="JavaScript" type="text/javascript" >
var idCategory="<?= (isset($idCategory))? intval($idCategory) : 0; ?>";
var offsetvideolist="";
var idgallery="";
var idgallerymore="";
</script>
<script language="javascript" type="text/javascript" src="/jsp/lazyload.process.js"></script>
<script language="javascript" type="text/javascript" src="/js/front/process-video-list.js" defer></script>
<!-- /JS -->