<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2025 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */
 
 

	//---------------------------------------------------------  
	// INIT
	//---------------------------------------------------------

		include($_SERVER['DOCUMENT_ROOT'].'/content/contact/index.php');

 
	//---------------------------------------------------------  
	// CONTACT FORM
	//--------------------------------------------------------- 


		$reqc = "SELECT p.*, l.* FROM ".$prefixContact."item p
				INNER JOIN ".$prefixContact."lang l
				ON p.id=l.idcontact
				AND l.idLang=:idLang
				WHERE p.idCategory=:idCategory AND p.status=1
				ORDER BY p.position ASC";
		$resc = $db->prepare($reqc);
		$resc->bindParam(':idCategory', $idCategory, PDO::PARAM_INT);
		$resc->bindParam(':idLang', $idLang, PDO::PARAM_INT);
		$resc->execute();
		$nbc=$resc->rowCount();
		$tabc = $resc->fetchAll();
		$resc->closeCursor();
		$resc = NULL;


	//---------------------------------------------------------  
	// MUTI-CONTACT
	//--------------------------------------------------------- 

		$contactBox="";

		if($nbc==1 ){
			$contactBox.='<input type="hidden" name="recipient" id="recipient" value="'.$tabc[0]['email'].'">';
	  	}else{
			$contactBox.='<div class="dropdown" style="margin-bottom:15px;">';
			$contactBox.='<select name="recipient" id="recipient" class="input-normal">';
			$contactBox.='<option value="">'.$translations['SELECTTXT'].'</option>';
			for($i=0; $i<$nbc; $i++){
				$contactBox.='<option value="'.$tabc[$i]['email'].'">'.$tabc[$i]['title'].'</option>';
			}
			$contactBox.='</select>';
			$contactBox.='</div>';
		}

?>
<!-- CSS -->
<link href="/css/form.css" rel="stylesheet" type="text/css" />
<!-- /CSS -->