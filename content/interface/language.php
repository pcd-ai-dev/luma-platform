<!-- Language -->
<?php
	echo '<div id="langarea">';
	foreach($tabLang as $value){
		echo '<a href="'.$siteHost.'/language/'.$value['id'].'/" title="'.$value['name'].'" target="_self" '.(($idLang==$value['id'])? 'class="current"':'').' >'.strtoupper($value['code']).'</a>';
	}
	echo '</div>';
?>
<!-- /Language -->