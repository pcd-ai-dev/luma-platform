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

    <div id="contentContainer" style="display:block;width:96%; margin-bottom:100px;padding-top:10px;padding-left:15px;">
        <div>&nbsp;</div>
        <div>&nbsp;</div>
        <div>&nbsp;</div>
        <div class="headerform">Image site | Grand format</div>
        <div class="corpsForm">
            <div id="photoUploadArea"></div>
        </div>
        <div>&nbsp;</div>
        <div>&nbsp;</div>
        <div class="headerform">Image site | Petit format</div>
        <div class="corpsForm">
            <div id="photoSquareUploadArea"></div>
        </div>
        <div>&nbsp;</div>
        <div>&nbsp;</div>
        <div class="headerform">Image site | Mobile</div>
        <div class="corpsForm">
            <div id="photoMobileUploadArea"></div>
        </div>
        <div>&nbsp;</div>
        <div>&nbsp;</div>
        <div>&nbsp;</div>
        <div>&nbsp;</div>
    </div>