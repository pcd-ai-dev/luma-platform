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

        $idlauch = $secure->v('int', 'idlauch', false) ?? 0;
        $adminId = $secure->session('adminData.id', 'int', false) ?? 0;

		if (!isset($_SESSION['adminMenuItemArray']) || !is_array($_SESSION['adminMenuItemArray'])) {$_SESSION['adminMenuItemArray'] = [];}

		$_SESSION['adminMenuItemArray'] = array_filter(
			$_SESSION['adminMenuItemArray'],
			function ($v) {
				if (filter_var($v, FILTER_VALIDATE_INT) !== false) {return true;}
				if (is_string($v) && preg_match('/^[a-zA-Z0-9_-]{1,50}$/', $v)) {return true;}
				return false;
			}
		);

		$_SESSION['adminMenuItemArray'] = array_values($_SESSION['adminMenuItemArray']);
			
		
   //---------------------------------------------------------  
   // PROCESS
   //--------------------------------------------------------- 

		if (($key = array_search($idlauch, $_SESSION['adminMenuItemArray'])) !== false) {
			unset($_SESSION['adminMenuItemArray'][$key]);
		}else{
			array_push($_SESSION['adminMenuItemArray'], (int)$idlauch);
		}

        $adminMenuItemArray = array_values(
            array_unique(
                array_filter(
                    $_SESSION['adminMenuItemArray'],
                    fn($v) => $v !== null && trim((string)$v) !== ''
                )
            )
        );
        
        $menuItem=implode(",", $adminMenuItemArray);

        $_SESSION['adminData']['menuItem']=$menuItem;

        $countMenuItem=count($adminMenuItemArray);

        if($countMenuItem==0){
            $_SESSION['adminData']['menuStatus']=0;
        }else{
             $_SESSION['adminData']['menuStatus']=1;
        }

        $_SESSION['countMenuItem']=$countMenuItem;
        $_SESSION['adminMenuItemArray']=$adminMenuItemArray;


   //---------------------------------------------------------  
   // DB
   //--------------------------------------------------------- 
			
		$req = "UPDATE ".$prefixAdmin."user SET menuItem=:menuItem WHERE id=:adminId";
		$res = $db->prepare($req);
        $res->bindValue(':menuItem', $menuItem, PDO::PARAM_STR);
		$res->bindValue(':adminId', $adminId, PDO::PARAM_STR);
		$res->execute();
		$res->closeCursor();
		$res = NULL;
 

   //---------------------------------------------------------  
   // RESPONSE
   //--------------------------------------------------------- 
 
		$response = [
			'status' => 1,
			'countMenuItem' => $countMenuItem
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;