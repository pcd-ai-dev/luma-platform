<?php declare(strict_types=1);

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */


    //---------------------------------------------------------  
   	// INIT NAME SPACES
   	//---------------------------------------------------------

		use Tools\SecureManager;

   //---------------------------------------------------------
   // CONNEXIONS
   //---------------------------------------------------------

		include($_SERVER['DOCUMENT_ROOT'].'/config.php');


   //---------------------------------------------------------
   // INIT CLASS
   //---------------------------------------------------------

		$secure = new SecureManager(requirePost: true, requireCsrf: true);
		
		
   //---------------------------------------------------------  
   // VARIABLES
   //---------------------------------------------------------
		
		$term=$secure->v('string', 'term', false) ?? '';

		
   //---------------------------------------------------------  
   // PROCESS
   //---------------------------------------------------------
   
   		$req = "SELECT id, tag FROM ".$prefixParam."tags WHERE tag like :term" ;			 
		$res = $db->prepare($req);
		$res->bindValue(':term', '%'.$term.'%', PDO::PARAM_STR);
		$res->execute();
		$nb=$res->rowCount();
		$tab=$res->fetchAll();
		$res->closeCursor();
		$res = NULL;
		
		$return = array();
		
		for($i=0;$i<$nb;$i++){
			if(empty($tab[$i]['tag'])){}else{
			array_push($return,array('id'=>$tab[$i]['id'],'label'=>mb_convert_encoding($tab[$i]['tag'], 'UTF-8'),'value'=>mb_convert_encoding($tab[$i]['tag'], 'UTF-8')));
			}	
		}


   //---------------------------------------------------------  
   // Response
   //---------------------------------------------------------
   
		echo(json_encode($return));