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
<!-- CSS -->
<link href="/css/admin_home.css" rel="stylesheet" type="text/css" />
<!-- /CSS -->

<!-- Content -->
<div style="border:0px;text-align:left;padding-right:20px;">
	<div style="margin-left:15px;margin-top:15px;">
		<div id="indexContainer">
			<?php
				foreach ($menuStructureSummary as $groupId => $group) {

					$visibleItems = array_filter($group['items'], function ($item) use ($levelOwner) {
						return in_array($levelOwner, $item['level']);
					});

					if (empty($visibleItems)) {continue;}

					$iconGroupItem=$_SERVER['DOCUMENT_ROOT'].'/img/interface/Admin/main/'.$group['label']['icon'];
					$svgGroupItem = file_get_contents($iconGroupItem);

					echo '<div class="item">';
					echo '<div class="headerFormHome">';
					echo '<table width="100%" style="border:0px;" cellspacing="0" cellpadding="0">';
					echo '<tr>';
					echo '<td style="text-align:left;width:30px;" width="1%">'.$svgGroupItem.'</td>';
					echo '<td width="2%">&nbsp;</td>';
					echo '<td style="text-align:left;">'.htmlspecialchars($group['label'][0]).'</td>';
					echo '</tr>';
					echo '</table>';
					echo '</div>';

					echo '<div class="corpsForm" style="width:100%">';
					echo '<div id="iconeItemAdmin" style="margin-top:20px;margin-bottom:30px;">';
					echo '<table style="border:0px;" cellspacing="0" cellpadding="0" width="100%">';
					echo '<tr>';

					foreach ($visibleItems as $item) {
						$firstDisplayedItem = true;

						if (!$firstDisplayedItem) {
							//echo '<td><div style="width:0px;">&nbsp;</div></td>';
						} else {
							//echo '<td><div style="width:35px;">&nbsp;</div></td>';
						}

						$url = '/manager' . $varLink . '/' . $item['key'] . '.html';
						$iconItem = $_SERVER['DOCUMENT_ROOT'].'/img/interface/Admin/main/'.$item['icon'];
						$svgItem = file_get_contents($iconItem);
						$label = nl2br(htmlspecialchars($item['label']));

						echo '<td>';
						echo '<div><a href="' . $url . '" target="_self">'.$svgItem.' </a></div>';
						echo '<div><a href="' . $url . '" target="_self">'.$label.'</a></div>';
						echo '</td>';
					}

					echo '</tr>';
					echo '</table>';
					echo '</div>';
					echo '</div>';
		
					echo '</div>';

					$firstDisplayedItem = false;
				}
			?>
		</div>
		<div>&nbsp;</div>
</div>
<div>&nbsp;</div>
<div>&nbsp;</div>
<div>&nbsp;</div>
<div>&nbsp;</div>
<div>&nbsp;</div>
<div>&nbsp;</div>
<div>&nbsp;</div>
<div>&nbsp;</div>
<div>&nbsp;</div>
<!-- Content -->