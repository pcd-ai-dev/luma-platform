<?php

/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
*  @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*  @version  Release: 3
*/


    //---------------------------------------------------------
   	// INIT NAME SPACES
   	//---------------------------------------------------------

		use Front\ContactManager;


	//---------------------------------------------------------  
	// FILE SECURE
	//---------------------------------------------------------  

		if (!$session->getAdminData()) {
			header('HTTP/1.1 403 Forbidden');
			exit('Accès interdit');
		}


    //--------------------------------------------------------------------------------\\
	//--------------------------------------------------------------------------------\\
	// Gestion des contacts
	//--------------------------------------------------------------------------------\\
	//--------------------------------------------------------------------------------\\
	
	//---------------------------------------------------------  
	// CLASS INIT
	//--------------------------------------------------------- 
	
		$contact= new ContactManager($db);


    //---------------------------------------------------------  
	// Ajout  contact
	//--------------------------------------------------------- 

		if(isset($_POST['addContactStart'])){
	
		$email = $secure->v('string', 'email', false) ?? '';
		$phone = $secure->v('string', 'phone', false) ?? '';

		$reqContactAdd = "INSERT INTO ".$prefixContact."item (idCategory,email,phone,status) VALUES(:idCategory,:email,:phone,0)";
		$resContactAdd = $db->prepare($reqContactAdd);
		$resContactAdd->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
		$resContactAdd->bindValue(':email', $email, PDO::PARAM_STR);
		$resContactAdd->bindValue(':phone', $phone, PDO::PARAM_STR);
		$resContactAdd->execute();
		$resContactAdd->closeCursor();
		$resContactAdd = NULL;
			
		$idContact=$db->lastInsertId();

		
		for($i=0; $i<$nbLang;$i++){
			
			$idLang=$tabLang[$i]['id'];

			$titleAdd        = 'titleAdd_' . $idLang;
			${$titleAdd}     = $secure->v('string', $titleAdd, false) ?? '';

			$descriptionAdd        = 'descriptionAdd_' . $idLang;
			${$descriptionAdd}     = $secure->v('string', $descriptionAdd, false) ?? '';

			$reqcontactladd = "INSERT INTO ".$prefixContact."lang (idContact,idLang,title,description,idCategory) VALUES(:idContact, :idLang, :title, :description, :idCategory)";
			$rescontactladd = $db->prepare($reqcontactladd);
			$rescontactladd->bindValue(':idContact', $idContact, PDO::PARAM_INT);
			$rescontactladd->bindValue(':idLang', $idLang, PDO::PARAM_INT);
			$rescontactladd->bindValue(':title', ${$titleAdd}, PDO::PARAM_STR);
			$rescontactladd->bindValue(':description', ${$descriptionAdd}, PDO::PARAM_STR);
			$rescontactladd->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
			$rescontactladd->execute();
			$rescontactladd->closeCursor();
			$rescontactladd = NULL;
		
			}
		}


    //---------------------------------------------------------  
	// Modification contact
	//--------------------------------------------------------- 
	

		if(isset($_POST['modContactStart'])){
			
			$modId     = $secure->v('int', 'modId', false) ?? 0;
			$modUrl    = $secure->v('string', 'modUrl', false) ?? '';
			$modEmail  = $secure->v('string', 'modEmail', false) ?? '';
			$modPhone  = $secure->v('string', 'modPhone', false) ?? '';
			
		
    //---------------------------------------------------------  
	// Modification contact db
	//--------------------------------------------------------- 
			
		$reqMod = "UPDATE ".$prefixContact."item SET email=:modEmail, phone=:modPhone WHERE id=:modId";
		$resMod = $db->prepare($reqMod);
		$resMod->bindValue(':modEmail', $modEmail, PDO::PARAM_STR);
		$resMod->bindValue(':modPhone', $modPhone, PDO::PARAM_STR);
		$resMod->bindValue(':modId', $modId, PDO::PARAM_INT);
		$resMod->execute();
		$resMod->closeCursor();
		$resMod = NULL; 
		
		for($i=0; $i<$nbLang;$i++){
			
			$idLang=$tabLang[$i]['id'];
					
			$modTitle        = 'modTitleContact_' . $idLang;
			${$modTitle}     = $secure->v('string', $modTitle, false) ?? '';

			$modDescription        = 'modDescriptionContact_' . $idLang;
			${$modDescription}     = $secure->v('string', $modDescription, false) ?? '';

			$reqContactMod = "UPDATE ".$prefixContact."lang SET title=:modTitle, description=:modDescription, idCategory=:idCategory WHERE idcontact=:modId AND idLang=:idLang";
			$resContactMod = $db->prepare($reqContactMod);
			$resContactMod->bindValue(':modId', $modId, PDO::PARAM_STR);
			$resContactMod->bindValue(':idLang', $idLang, PDO::PARAM_STR);
			$resContactMod->bindValue(':modTitle', ${$modTitle}, PDO::PARAM_STR);
			$resContactMod->bindValue(':modDescription', ${$modDescription}, PDO::PARAM_STR);
			$resContactMod->bindValue(':idCategory', $idCategory, PDO::PARAM_STR);
			$resContactMod->execute();
			$resContactMod->closeCursor();
			$resContactMod = NULL;
		
		}
		
		$modStatut="1";

        // Msg modif
		$msgUpdate='
			<script type="text/javascript">
				showWarningMessage("builderMsgUpdate", "Modifications enregistrées", "#81B929");
			</script>
		';
		
	}


    //---------------------------------------------------------  
	// Sélection mod contact
	//--------------------------------------------------------- 

		if((isset($modId)!='0')&&($tabItem=='11')) {

			$reqMod = "SELECT * FROM ".$prefixContact."item WHERE id=:modId";
			$resMod = $db->prepare($reqMod);
			$resMod->execute(array(':modId'=>$modId));
			$rMod=$resMod->fetch(PDO::FETCH_OBJ);
			$resMod->closeCursor();
			$resMod = NULL;
				
			$modEmail = (isset($rMod->email))? htmlspecialchars($rMod->email, ENT_QUOTES, 'UTF-8') : "";
			$modPhone = (isset($rMod->phone))? htmlspecialchars($rMod->phone, ENT_QUOTES, 'UTF-8') : "";
		}
		

    //---------------------------------------------------------  
	// Sélection affichage
	//--------------------------------------------------------- 

		if((($r?->type==1)&&($r?->switch==11))){
		
			$req = "SELECT p.*, l.* FROM ".$prefixContact."item p
				   INNER JOIN ".$prefixContact."lang l
				   ON p.id=l.idcontact
				   AND l.idLang='1'
				   WHERE p.idCategory=:idCategory
				   ORDER BY p.position ASC";
			$res = $db->prepare($req);
			$res->bindParam(':idCategory', $idCategory, PDO::PARAM_INT);
			$res->execute();
			$nb=$res->rowCount();
			$tab = $res->fetchAll();
			$res->closeCursor();
			$res = NULL;
				
		}