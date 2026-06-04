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


    //-------------------------------------------------------------------------------------\\
	//-------------------------------------------------------------------------------------\\
	// Modification Entete
	//-------------------------------------------------------------------------------------\\
    //-------------------------------------------------------------------------------------\\

   
		if(isset($_POST['modHeaderStart'])){

			$keywords   = $secure->v('string', 'keywords', false) ?? '';
			$paramPage  = $secure->v('string', 'paramPage', false) ?? '';
			$color      = $secure->v('string', 'color', false) ?? '';
			$colorTxt   = $secure->v('string', 'colorTxt', false) ?? '';
			

			$reqcmodif = "UPDATE ".$prefixRoot."category SET paramPage=:paramPage, color=:color, colorTxt=:colorTxt WHERE id=:idCategory";
			$rescmodif = $db->prepare($reqcmodif);
			$rescmodif->bindValue(':color', $color, PDO::PARAM_STR);
			$rescmodif->bindValue(':colorTxt', $colorTxt, PDO::PARAM_STR);
			$rescmodif->bindValue(':paramPage', $paramPage, PDO::PARAM_STR);
			$rescmodif->bindValue(':idCategory', $idCategory, PDO::PARAM_STR);
			$rescmodif->execute();
			$rescmodif->closeCursor();
			$rescmodif = NULL;

			for($i=0; $i<$nbLang;$i++){

				$idLang=intval($tabLang[$i]['id']);

				$title = 'title_' . $idLang;
				${$title} = $secure->v('string', $title, false) ?? '';

				$verbatim = 'verbatim_' . $idLang;
				${$verbatim} = $secure->v('string', $verbatim, false) ?? '';

				$dataTitle = 'dataTitle_' . $idLang;
				${$dataTitle} = $secure->v('string', $dataTitle, false) ?? '';

				$dataText = 'dataText_' . $idLang;
				${$dataText} = $secure->v('string', $dataText, false) ?? '';

				$description = 'description_' . $idLang;
				${$description}  = $secure->v('string', $description, false) ?? '';



	   //---------------------------------------------------------  
	   // Check idLang txt
	   //---------------------------------------------------------

			$reql = "SELECT * FROM ".$prefixRoot."category_lang WHERE idCategory=:idCategory AND idLang=:idLang";			 
			$resl = $db->prepare($reql);
			$resl->bindValue(':idCategory', $idCategory, PDO::PARAM_STR);
			$resl->bindValue(':idLang', $idLang, PDO::PARAM_STR);
			$resl->execute();
			$nbl=$resl->rowCount();
			$resl->closeCursor();
			$resl = NULL;

			if($nbl==0){

		   //---------------------------------------------------------  
		   // Insertion db langues category
		   //---------------------------------------------------------

			   //---------------------------------------------------------  
			   // Insertion db langue category
			   //---------------------------------------------------------

					$reqcaddl = "INSERT INTO ".$prefixRoot."category_lang (dCategory,idLang) VALUES(:dCategory,:idLang)";
					$rescaddl = $db->prepare($reqcaddl);
					$rescaddl->bindValue(':idCategory', $idCategory, PDO::PARAM_STR);
					$rescaddl->bindValue(':idLang', $idLang, PDO::PARAM_STR);
					$rescaddl->execute();
					$rescaddl->closeCursor();
					$rescaddl = NULL;


			   //---------------------------------------------------------  
			   // Insertion db nouvelle  page
			   //---------------------------------------------------------

					$reqpadd = "INSERT INTO ".$prefixRoot."pages (idCategory,idLang) VALUES(:idCategory,:idLang)";
					$respadd = $db->prepare($reqpadd);
					$respadd->bindValue(':idCategory', $idCategory, PDO::PARAM_STR);
					$respadd->bindValue(':idLang', $idLang, PDO::PARAM_STR);
					$respadd->execute();
					$respadd->closeCursor();
					$respadd = NULL;

			}else{


	   //---------------------------------------------------------  
	   // Modification page
	   //---------------------------------------------------------

			$reqpmodif = "UPDATE ".$prefixRoot."pages SET title=:title, verbatim=:verbatim, dataTitle=:dataTitle, dataText=:dataText, description=:description, keywords=:keywords WHERE idCategory=:idCategory AND idLang=:idLang";
			$respmodif = $db->prepare($reqpmodif);
			$respmodif->bindValue(':title', ${$title}, PDO::PARAM_STR);
			$respmodif->bindValue(':verbatim', ${$verbatim}, PDO::PARAM_STR);
			$respmodif->bindValue(':dataTitle', ${$dataTitle}, PDO::PARAM_STR);
			$respmodif->bindValue(':dataText', ${$dataText}, PDO::PARAM_STR);
			$respmodif->bindValue(':description', ${$description}, PDO::PARAM_STR);
			$respmodif->bindValue(':keywords', $keywords, PDO::PARAM_STR);
			$respmodif->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
			$respmodif->bindValue(':idLang', $idLang, PDO::PARAM_INT);
			$respmodif->execute();
			$respmodif->closeCursor();
			$respmodif = NULL;

			}
		}
			
	   //---------------------------------------------------------  
	   // INFO CAT
	   //---------------------------------------------------------

			$r=$page->infoCat($idCategory,1);

			$parentcheck=(isset($r->parent))? intval($r->parent) : 0;
			$typecategory=(isset($r->type))? intval($r->type) : 0;
			$statusswitch=(isset($r->switch))? intval($r->switch) : 0;
			$statuscategory=(isset($r->status))? intval($r->status) : 0;
			$positioncat=(isset($r->position))? intval($r->position) : 0;
			$colorCat=(isset($r->color))? htmlspecialchars($r->color, ENT_QUOTES, 'UTF-8') : '';
			$colorTxtCat=(isset($r->colortxt))? htmlspecialchars($r->colortxt, ENT_QUOTES, 'UTF-8') : '';


			// Msg modif
			$msgUpdate='
				<script type="text/javascript">
					showWarningMessage("builderMsgUpdate", "Modifications enregistrées", "#81B929");
				</script>
			';
			
		}