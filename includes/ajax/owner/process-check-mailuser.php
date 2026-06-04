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

		$emailPost = $secure->v('string', 'emails', false) ?? '';
        $idSite = $secure->v('int', 'idSite', false) ?? '';
		

   //---------------------------------------------------------  
   // PROCESS
   //---------------------------------------------------------
   
		$reqmail = "SELECT email FROM ".$prefixAdmin."user WHERE email=:emailpost AND idSite=:idSite";
		$resmail = $db->prepare($reqmail);
		$resmail->execute(array(':emailpost'=>$emailPost,':idSite'=>$idSite));
		$nbmail=$resmail->rowCount();
		$resmail->closeCursor();
		$resmail = NULL;


   //---------------------------------------------------------  
   // RESPONSE
   //--------------------------------------------------------- 	

   		$status=($nbmail==0) ? true : false;

		$response = [
			'error' => false,
			'status' => $status
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;