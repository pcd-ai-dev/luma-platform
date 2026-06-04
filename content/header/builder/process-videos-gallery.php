<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2025 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */
 
	//---------------------------------------------------------  
	// INIT
	//---------------------------------------------------------

		use Front\VideoManager;

		$video= new VideoManager($db,[
            'table_item' => $prefixVideo.'item',
            'table_lang' => $prefixVideo.'lang',
            'table_gallery' => $prefixVideo.'gallery',
            'table_gallery_lang' => $prefixVideo.'gallery_lang'
			]
		);

	//---------------------------------------------------------  
	// PHOTO DB
	//--------------------------------------------------------- 

        $tab=$video->galleryVideoList($idCategory);
        $nb=count($tab);

	//---------------------------------------------------------  
	// GALLERY CONTENT
	//---------------------------------------------------------

        $galleryContent="";
        $galleryContent.='<div id="videoMenu" style="width:100%; margin-top:0px;text-align:center;">';
        $galleryContent.='<a href="" id="" class="current">'.$translations['ALLRESULTTXT'].'</a>';

        for ( $i = 0; $i < $nb; $i++ ) {
            $glang = $video->infoGalleryLang( $tab[ $i ][ 'id' ], $idLang );
            $galleryContent.='<a href="" id="' . $tab[ $i ][ 'id' ] . '">'.$glang?->title. '</a>';
        }
        $galleryContent.='</div>';
        $galleryContent.='<div style="width: 100%;"><div id="videoList"></div></div>';

?>

<!-- CSS -->
<link href="/css/type_video.css" rel="stylesheet" type="text/css" />
<style>
    /*
    #videoMenu a, #videoMenu a {
        color:<?= (isset($colorTxtPage))? $colorTxtPage : '#2F2F2F'; ?>!important;
    }
    */
</style>
<!-- CSS end -->