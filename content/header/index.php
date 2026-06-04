	<!-- Header -->
	<header id="header">
		<div class="leftHeader">
		</div>
		<div class="centerHeader">
			<?php if(count($tabLang)<=1){}else{include($_SERVER['DOCUMENT_ROOT'] . '/content/interface/language.php');} ?>
		</div>
		<div class="rightHeader">
			<!-- Nav -->
			<nav>
				<!-- Menu -->
				<div id="menu" style="text-align: left;"><?php include('content/interface/menu.php'); ?></div>
				<!-- /Menu -->
				<!-- MenuMobile -->
				<input type="checkbox" id="toggle-menu" style="display:none;" />
				<label for="toggle-menu"><span class="menu-icon"></span></label>
				<div id="menuRight"><?php include('content/interface/menuMobile.php'); ?></div>
				<!-- /MenuMobile -->
			</nav>
			<!-- /Nav -->
		</div>
	</header>
	<!-- /Header -->