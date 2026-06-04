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
   // CLASS INIT SECURE
   //---------------------------------------------------------

		$secure = new SecureManager(requirePost: true, requireCsrf: true);
		
	
   //---------------------------------------------------------  
   // VARIABLES
   //---------------------------------------------------------

        $adminId = $secure->session('adminData.id', 'int', false) ?? 0;
		
	
   //---------------------------------------------------------  
   // PROCESS
   //--------------------------------------------------------- 

        if($_SESSION['adminData']['menuStatus']==0){
            $_SESSION['adminData']['menuStatus']=1;
        }else{
            $_SESSION['adminData']['menuStatus']=0;
        }

        $statusUpdate=$_SESSION['adminData']['menuStatus'];


   //---------------------------------------------------------  
   // DB
   //--------------------------------------------------------- 
			
		$req = "UPDATE ".$prefixAdmin."user SET menuStatus=:menuStatus WHERE id=:adminId";
		$res = $db->prepare($req);
        $res->bindValue(':menuStatus', $statusUpdate, PDO::PARAM_STR);
		$res->bindValue(':adminId', $adminId, PDO::PARAM_INT);
		$res->execute();
		$res->closeCursor();
		$res = NULL;
			
		
   //---------------------------------------------------------  
   // RESPONSE
   //--------------------------------------------------------- 
 
		$response = [
			'status' => $statusUpdate
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;