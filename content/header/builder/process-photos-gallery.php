<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */


        //---------------------------------------------------------  
	// INIT
	//--------------------------------------------------------- 

                use Front\PhotoManager;


	//---------------------------------------------------------  
	// INIT CLASS
	//--------------------------------------------------------- 

                $photo= new PhotoManager($db);


	//---------------------------------------------------------  
	// GALLERY CONTENT
	//---------------------------------------------------------

                $galleryContent="";
                $galleryContent.='<div id="photoMenu"></div>';
                $galleryContent.='<ul id="photoList"></ul>';

?>
<!-- CSS -->
<link href="/css/type_galerie.css" rel="stylesheet" type="text/css" />
<style>
    #menuPhoto a, #menuPhoto a {
        color:<?= (isset($colorTxtPage))? $colorTxtPage : '#2F2F2F'; ?>!important;
    }
</style>
<!-- CSS end -->