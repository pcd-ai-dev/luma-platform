<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */
 
 
	//---------------------------------------------------------  
	// LOCATION PROCESS
	//--------------------------------------------------------- 

   //---------------------------------------------------------  
   // MAP
   //---------------------------------------------------------

		$req = "SELECT * FROM ".$prefixLoc."item WHERE idCategory=:idCategory";				
		$res = $db->prepare($req);
		$res->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
		$res->execute();
		$nb=$res->rowCount();
		$rmap = $res->fetch(PDO::FETCH_OBJ);
		$res->closeCursor();
		$res = NULL;

?>
<!-- CSS -->
<link href="/css/type_map.css" rel="stylesheet" type="text/css" />
<!-- /CSS -->