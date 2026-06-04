<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2025 | Luma Prod - Pierre Cosmao Dumanoir
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
	// Connexion
	//---------------------------------------------------------

		include (ADMINPROCESSPATH.'tree/index.php');


	//---------------------------------------------------------
	// Variables
	//---------------------------------------------------------

		$pageUId = uniqid('pageUId_');
		$linkPage= '<div style="padding-left: 20px;padding-top:60px;"><b>'.stripslashes((isset($r->name))? $r->name : '').' : </b> <a href="/'.(($_SESSION['siteData']['status']==1)? $_SESSION['siteData']['varLink'].'/' : '').$r->id.'-'.$string->cleanUrl(mb_convert_encoding((isset($r->name))? $r->name : '', 'UTF-8')).'.html" target="_blank">acc&egrave;der &agrave; la page</a></div>';
		$switchTab = $string->setInt($r?->switch);
		$typeTab = $string->setInt($r?->type);
?>

<!-- Script general -->

<!-- CSS File -->
<link href="/jsp/coloris/coloris.min.css" rel="stylesheet" type="text/css" />
<style>
	ul.tabs li.current {
		background-color: <?= $r->color;?>;
		padding: 12px;
		color: <?= $r->colorTxt;?>;
		border-bottom: 0px;
		border-right: 1px solid #444444;
	}
</style>
<!-- CSS File End -->

<div style="text-align:right;" id="close"><a href="/manager<?= $varLink; ?>/admin_tree.html" ><img src="/img/interface/btn/BtnClosePage.svg" alt="Fermer" name="Fermer" width="24" height="24" style="border:0px;" id="Fermer" /></a></div>

<!-- Update MSG -->
<div id="builderMsgUpdate">&nbsp;</div>
<?= (isset($msgUpdate))? $msgUpdate : ''; ?>
<!-- /Update MSG -->

<!-- Tabs -->
	<ul class="tabs" id="tabsholder" data-initial-tab="<?= isset($tabItem) ? (int)$tabItem : 1 ?>">
		<li data-tab="tab1">G&eacute;n&eacute;ral</li>
      	<li data-tab="tab2">Ent&ecirc;te</li>
      	<?=  ($typeTab!=1)? '<li data-tab="tab3">Contenu</li>' : ''; ?>
		<?=  ((($typeTab==1)&&($switchTab==2))||($typeTab=="3"))? '<li data-tab="tab4">Articles</li>' : ''; ?>
	 	<?=  (($typeTab==1)&&($switchTab==5))? '<li data-tab="tab5">Galerie photo</li>' : ''; ?>
      	<?=  (($typeTab==1)&&($switchTab==6))? '<li data-tab="tab6">Galerie vid&eacute;os</li>' : ''; ?>
		<?=  (($typeTab==1)&&($switchTab==11))? '<li data-tab="tab11">Gestion des contacts</li>' : ''; ?>
		<?=  (($typeTab==1)&&($switchTab==12))? '<li data-tab="tab12">Plan d\'acc&egrave;s</li>' : ''; ?>
		<?php 
	  		if($typeTab==1){
				for($i=0,$p=14; $i<$nbLang;$i++,$p++){
					$idLangCheck=$tabLang[$i]['id'];
					echo '<li data-tab="tab'.$p.'" class="flag'.$tabLang[$i]['id'].'" style="padding-left:40px;">Page Builder</li>';
	  			}
			}else{}
	  	?>
    </ul>
	 
	
	<?php if($typeTab==1){ $widthContent="100%";} else { $widthContent="80%";}?>
    
	<div id="contentContainer">
	
		<div class="contentLeft" style="min-width:<?= $widthContent; ?>;">
    
			<div class="contents marginbot">

				<div id="content1" class="tabscontent" style="margin:0px;">
			  		<?= (isset($linkPage))? $linkPage : ''; ?>
			  		<?php include 'main.php'; ?>
			  	</div>

			  	<div id="content2" class="tabscontent" style="margin:0px;">
			  		<?= (isset($linkPage))? $linkPage : ''; ?>
			  		<?php include 'headers.php'; ?>
			  	</div>
				
			 	<?php if($typeTab!=1):?>
			  		<div id="content3" class="tabscontent" style="margin:0px; ">
			  			<?= (isset($linkPage))? $linkPage : ''; ?>
			  			<?php include 'body.php'; ?>
			  		</div>
				<?php endif; ?>
				<?php if(($typeTab==3)||(($typeTab==1)&&($switchTab==1))||(($typeTab==1)&&($switchTab==2))){ ?>
			  	<div id="content4" class="tabscontent" style="margin:0px; ">
			  		<?= (isset($linkPage))? $linkPage : ''; ?>
			  		<?php include 'post.php'; ?>
			  	</div>
			  	<?php }else{}?>

				<?php if(($typeTab==1)&&($switchTab==5)){ ?>
				<div id="content5" class="tabscontent" style="margin: 20px;">
					<?= (isset($linkPage))? $linkPage : ''; ?>
					<?php include 'gallery.php'; ?>
				</div>
				<?php }else{}?>

				<?php if(($typeTab==1)&&($switchTab==6)){ ?>
				<div id="content6" class="tabscontent" style="margin: 20px;">
					<?= (isset($linkPage))? $linkPage : ''; ?>
					<?php include 'videos_gallery.php'; ?>
				</div>
				<?php }else{}?>

				<?php if((($typeTab==1)&&($switchTab==10))){ ?>
			  		<div id="content10" class="tabscontent" style="margin:0px; ">
			  			<?= (isset($linkPage))? $linkPage : ''; ?>
			  			<?php include 'location.php'; ?>
			  		</div>
			  	<?php }else{}?>

				<?php if((($typeTab==1)&&($switchTab==11))){ ?>
			  		<div id="content11" class="tabscontent" style="margin:0px; ">
			  			<?= (isset($linkPage))? $linkPage : ''; ?>
			  			<?php include 'contact.php'; ?>
			  		</div>
			  	<?php }else{}?>

				<?php if((($typeTab==1)&&($switchTab==12))){ ?>
			  		<div id="content12" class="tabscontent" style="margin:0px; ">
			  			<?= (isset($linkPage))? $linkPage : ''; ?>
			  			<?php include 'map.php'; ?>
			  		</div>
			  	<?php }else{}?>

				<?php if($typeTab==1){
						for($i=0,$p=14; $i<$nbLang;$i++,$p++){
							$idLangCheck=$tabLang[$i]['id'];
							$idLangPage='l_'.$idLangCheck;
							${$idLangPage}=$idLangCheck;
							echo '<div id="content'.$p.'" class="tabscontent" style="margin:0px;">';
							include 'builder.php';
							echo '</div>';
						}
					} 
			 	 ?>

			</div>
		</div>
  </div>
  <!-- /Tabs -->

<script type="text/javascript">
	const idInfo="tab<?= intval($_GET['tab']);?>";
	const ajaxPath="<?= AJAXPATH; ?>";
	const adminColor= "<?= $colorCustomAdmin; ?>";
	const adminTextColor= "<?= $colorCustomTextAdmin; ?>";
	const idCategory=<?= intval($idCategory); ?>;
	const idSite=<?= intval($idSite); ?>;
	const autocompledPath=ajaxPath+"/action/process-autocomplete.php";
	var modId=<?= intval($modId); ?>;
	<?= (($modId!="0")&&($modStatus=="0"))? "var modStatus=1;" : "var modStatus=0;"; ?>
	/* Page Param */
    const prefixPage="<?= $prefixRoot; ?>";
    const entityPage="page";
    const pageUId="<?= $pageUId; ?>";
	/* Contact Param */
	const prefixContact="<?= $prefixContact; ?>";
	const entityContact="contact";

	<?php if(($typeTab==3)||(($typeTab==1)&&($switchTab==1))||(($typeTab==1)&&($switchTab==2))): ?>
		/* Post Param */
        const prefixPost="<?= $prefixPost; ?>";
		const entityPost="post";
		const postUId="<?= $postUId; ?>";
	<?php endif; ?>

	<?php if(($typeTab==1)&&($switchTab==5)): ?>
		/* Gallery Photos Param */
		const prefixImg="<?= $prefixGallery; ?>";
		const entityImg="photo";
		const photoUId="<?= $photoUId; ?>";
	<?php endif; ?>

	/* Location/Map Param */
	const prefixLocation="<?= $prefixLoc; ?>";
	const entityLocation="location";
	<?php if((($typeTab==1)&&($switchTab==12))): ?>
		const countMap=<?= (isset($countMap))? (int)$countMap : 0; ?>;
		const latHost = "<?= (isset($rMap->lat))? $rMap->lat : 0; ?>";
		const lngHost = "<?= (isset($rMap->lng))? $rMap->lng : 0; ?>";
		const modAddress="<?= (isset($rMap->address))? $rMap->address : ''; ?>";
		const MAP_ID = "<?= MAP_ID; ?>";
		const idMap = <?= (isset($rMap->id))? $rMap->id : 0; ?>;
		const imgMapHost = "<?= (isset($rMap->img)) ? '/img/location/'.$idCategory.'/img/square/'.$rMap->img : '/tmp/map/default.png'; ?>";
		const adressMap = <?= (isset($rMap->address)) ? json_encode($rMap->address) : json_encode('""'); ?>
	<?php endif; ?>

	<?php if(($typeTab==1)&&($switchTab==6)): ?>
		/* Video Param */
        const prefixVideo="<?= $prefixVideo; ?>";
        const entityVideo="video";
	<?php endif; ?>

</script>


<!-- JS File -->
<script type="text/javascript" src="/jsp/coloris/coloris.min.js"></script>
<script type="text/javascript" src="/jsp/inPlaceEditing/xeditable.js"></script>
<script type="text/javascript" src="/jsp/flatpickr/flatpickr.js"></script>
<script type="text/javascript" src="/jsp/flatpickr/flatpickr_local_fr.js"></script>
<script type="text/javascript" src="/js/class/tags.class.js"></script>
<script type="text/javascript" src="/js/admin/action/process-tabs.js"></script>
<script type="text/javascript" src="/js/admin/action/process-file-add.js"></script>
<script type="text/javascript" src="/js/admin/action/process-sortable.js"></script>
<script type="text/javascript" src="/js/admin/action/process-status.js"></script>
<script type="text/javascript" src="/js/admin/page/process-category-mod.js"></script>
<?php if(($typeTab==1)&&($switchTab==6)): ?>
<!-- Videos -->
<script type="text/javascript" src="/js/admin/video/process-video.js"></script>
<?php endif; ?>

<?php if(($typeTab==1)&&($switchTab==2)): ?>
<!-- Post -->
<script type="text/javascript" src="/js/admin/post/process-post.js"></script>
<?php endif; ?>

<?php if(($typeTab==1)&&($switchTab==5)): ?>
<!-- Gallery Photo -->
<script type="text/javascript" src="/js/class/file.uploader.class.js"></script>
<script type="text/javascript" src="/js/admin/gallery/process-gallery.js"></script>
<?php endif; ?>

<?php if((($typeTab==1)&&($switchTab==11))): ?>
<!-- Contact -->
<script type="text/javascript" src="/js/admin/contact/process-contact.js"></script>
<?php endif; ?>

<?php if((($typeTab==1)&&($switchTab==12))): ?>
<!-- Map -->
<script src="https://maps.googleapis.com/maps/api/js?key=<?= htmlspecialchars(GOOGLE_API_KEY, ENT_QUOTES, 'UTF-8'); ?>&libraries=places,marker,geometry&loading=async&callback=initMap" async defer></script>
<script type="text/javascript" src="/js/admin/location/process-map.js"></script>
<?php endif; ?>

<!-- JS -->