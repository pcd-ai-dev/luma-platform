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


    //---------------------------------------------------------  
    // Variables
    //---------------------------------------------------------

        if (empty($_SESSION['adminMenuArray'])) {
            $_SESSION['adminMenuArray']=(isset($_SESSION['adminData']['menu']))? explode(",", $_SESSION['adminData']['menu']) : []; 
        } else {
        }

        if (empty($_SESSION['adminMenuItemArray'])) {
           $_SESSION['adminMenuItemArray']=(isset($_SESSION['adminData']['menuItem']))? explode(",", $_SESSION['adminData']['menuItem']) : [];
        } else {}

        if (isset($_SESSION['countMenuItem'])) {
            $countMenuItem = (isset($_SESSION['countMenuItem']))? $_SESSION['countMenuItem']: 0;
        }else{
            $countMenuItem = count($_SESSION['adminMenuItemArray']);
        }

        $chekMenuItemArray = $_SESSION['adminMenuItemArray'];
        $chekMenuArray = $_SESSION['adminMenuArray'];
        $checkMenuStatus=(isset($_SESSION['adminData']['menuStatus']))? $_SESSION['adminData']['menuStatus'] : 0; 
        $levelOwner = $_SESSION['adminData']['level'];


    //---------------------------------------------------------  
    // Content
    //--------------------------------------------------------- 

        echo '<style>';
        if(($countMenuItem==0)||($checkMenuStatus==0)){
            echo '.block_gauche {width:60px;}';
            echo '#menuRight {width:0px;opacity:0;}';
            echo '.leftHeader {min-width:60px;}';
            echo '#adminheader .centerHeader {padding-left:10px;}';
            echo '.titleAdminHeader {width:0px;opacity:0;}';
        }else{
            echo '.block_gauche {width:340px;}';
            echo '#menuRight {width:224px;opacity:1;}';
            echo '.leftHeader {min-width:300px;} ';
            echo '#adminheader .centerHeader {padding-left:30px;}';
            echo '.titleAdminHeader {width:200px;opacity:1;}';
        }
        echo '</style>';

 
?>
    <div id="adminMenuArea" >
        <div id="adminmenu">
            <?php 
                if($checkMenuStatus==0){$imgCollapse="collapse_off.svg";} else {$imgCollapse="collapse_on.svg";}
                if($countMenuItem==0){$imgCollapse="collapse_disable.svg";} else{$imgCollapse=$imgCollapse;}
            ?>
                <div id="menuLeft">
                    <div class="collapseMenu"><img src="/img/interface/Admin/small/<?= $imgCollapse; ?>" width="25" height="25" id="collapseIMG"/></div>
                   <?php
                    foreach ($menuStructure as $groupId => $group) {

                        $visibleItems = array_filter($group['items'], function ($item) use ($levelOwner) {
                            return in_array($levelOwner, $item['level']);
                        });

                        if (empty($visibleItems)) {
                            continue;
                        }

                        if (in_array($groupId, $chekMenuItemArray)) {
                            $TmenuStatusCSS = 'itemSeparationIconExpanded';
                            $menuStatusCSS = '';
                        } else {
                            $TmenuStatusCSS = 'itemSeparationIcon';
                            $menuStatusCSS = 'style="display:none;"';
                        }

                        $icon = '/img/interface/Admin/small/' . $group['label']['icon'] . '';
                        $labelGroup=$group['label'][0];

                        echo '<div class=" ' . $TmenuStatusCSS . '" id="'.intval($groupId).'" title="'.$labelGroup.'"><img src="'.$icon.'" width="25" height="25"/></div>';
                    }
                    ?>
                </div>
                <div id="menuRight">
                    <?php
                    foreach ($menuStructure as $groupId => $group) {
                        $visibleItems = array_filter($group['items'], function ($item) use ($levelOwner) {
                            return in_array($levelOwner, $item['level']);
                        });

                        if (empty($visibleItems)) {
                            continue;
                        }

                        if (in_array($groupId, $chekMenuItemArray)) {
                            $menuItemStatusCSS = 'style="display:block;"';
                            //$TmenuStatusCSS = 'itemSeparationExpanded';
                            $menuStatusCSS = 'style="display:block;"';
                        }else{
                            $menuItemStatusCSS = 'style="display:none;"';
                            //$TmenuStatusCSS = 'itemSeparation';
                            $menuStatusCSS = 'style="display:none;"';
                        }
                        if (in_array($groupId, $chekMenuArray)) {
                           $TmenuStatusCSS = 'itemSeparationExpanded';
                            // $menuStatusCSS = 'style="display:block;"';
                        } else {
                            $TmenuStatusCSS = 'itemSeparation';
                            $menuStatusCSS = 'style="display:none;"';
                        }

                        echo '<div class="itemSeparation menuItem_'.intval($groupId).' '. $TmenuStatusCSS . '" id="' . intval($groupId) . '" '.$menuItemStatusCSS.'>' . $group['label'][0] . ' ' . $group['label']['extra'] . '</div>';
                        echo '<div class="menuArea_' . intval($groupId) . '" ' . $menuStatusCSS . '>';

                        foreach ($visibleItems as $itemMenu) {

                            $urlItem = '/manager' . $varLink . '/' . $itemMenu['key'] . '.html';
                            $icon = $itemMenu['icon'];
                            $label = htmlspecialchars($itemMenu['label']);

                            echo '<a href="'.$urlItem.'" target="_self" ' . $itemMenu['cat'] . '>';
                            echo $label.(!empty($itemMenu['count']) ? ' <span class="countMenuArea">' . intval($itemMenu['count']) . '</span>' : '');
                            echo '</a>';

                        }
                        echo '</div>';
                    }
                    ?>
                </div>
        </div>
    </div>
    <script type="text/javascript">var chekMenuItemArray=<?= (isset($countMenuItem))? $countMenuItem : 0; ?>;</script>
    <script type="text/javascript" src="/js/admin/menu/process-menu.js"></script>