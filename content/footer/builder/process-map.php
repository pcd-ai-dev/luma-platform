<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */
 
 
	//---------------------------------------------------------  
	// FRONT MAP
	//--------------------------------------------------------- 

?>

<!-- JS Files -->
<script type="text/javascript">
	const latHost = <?= (isset($rmap->lat))? (float)$rmap->lat : 48.858370; ?>;
	const lngHost = <?= (isset($rmap->lng))? (float)$rmap->lng : 2.294481; ?>;
	const destinationConfig = <?= json_encode((isset($rmap->address))? $rmap->address : '') ?>;
	const bulletxt = <?= json_encode((isset($rmap->address))? $rmap->address : 'Paris') ?>;
	const imagehostUrl = <?= json_encode(isset($rmap->img) ? '/tmp/map/' . $rmap->img : '/tmp/map/default.png') ?>;
	const MAP_ID = "<?= MAP_ID; ?>";
	const GOOGLE_MAPS_API_KEY = "<?= GOOGLE_API_KEY; ?>";
</script>
<script type="text/javascript" src="/js/front/process-map.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?= htmlspecialchars(GOOGLE_API_KEY, ENT_QUOTES, 'UTF-8'); ?>&libraries=places,marker,geometry&loading=async&callback=initialize" async defer></script>
<!-- JS Files end -->