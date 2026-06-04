<?php

/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
*  @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*  @version  Release: 3
*/


	//---------------------------------------------------------  
	// FILE SECURE
	//---------------------------------------------------------  

		if (!$session->getAdminData()) {
			header('HTTP/1.1 403 Forbidden');
			exit('Accès interdit');
		}


    //--------------------------------------------------------------------------------\\
	//--------------------------------------------------------------------------------\\
	// Gestion du texte de la page
	//--------------------------------------------------------------------------------\\
	//--------------------------------------------------------------------------------\\


    //---------------------------------------------------------  
	// Modification
	//--------------------------------------------------------- 
   
   
		if(isset($_POST['modBodyStart'])){ 

		   //---------------------------------------------------------  
		   // Variable
		   //---------------------------------------------------------
			
				$idCategory=$secure->v('int', 'idCategory', false) ?? 0;

		   //---------------------------------------------------------  
		   // Modification page
		   //---------------------------------------------------------

            for($i=0; $i<$nbLang;$i++){

                $idLang=$tabLang[$i]['id'];

                $text='textCorps_'.$idLang;
                ${$text}=$secure->v('string', $text, false) ?? '';

                $reqpmodif = "UPDATE ".$prefixRoot."pages SET text=:text where idCategory=:idCategory AND idLang=:idLang";
                $respmodif = $db->prepare($reqpmodif);
                $respmodif->bindValue(':text', ${$text}, PDO::PARAM_STR);
                $respmodif->bindValue(':idCategory', $idCategory, PDO::PARAM_STR);
                $respmodif->bindValue(':idLang', $idLang, PDO::PARAM_STR);
                $respmodif->execute();
                $respmodif->closeCursor();
                $respmodif = NULL;

            }

			// Msg modif
			$msgUpdate='
				<script type="text/javascript">
					showWarningMessage("builderMsgUpdate", "Modifications enregistrées", "#81B929");
				</script>
			';

		}