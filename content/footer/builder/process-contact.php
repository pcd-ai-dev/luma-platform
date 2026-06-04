<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */
 
 
	//---------------------------------------------------------  
	// CONTACT FORM
	//--------------------------------------------------------- 

?>

<!-- JS -->
<script type="text/javascript" src="/js/class/validator.class.js"></script>
<script type="text/javascript">
	const emailRequired = "<?= $translations['MAILERRORTXT']; ?>";
	const emailFormat = "<?=  $translations['MAILVALIDERRORTXT']; ?>";
	const nameRequired = "<?=  $translations['NAMEENTERTXT']; ?>";
	const cgvRequired = "<?=  $translations['CHECKCGU']; ?>";
</script>
<script type="text/javascript" src="/js/front/process-contact.js"></script>
<!-- /JS -->