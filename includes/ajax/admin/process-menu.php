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

		if (!isset($_SESSION['adminMenuArray']) || !is_array($_SESSION['adminMenuArray'])) {$_SESSION['adminMenuArray'] = [];}

		$_SESSION['adminMenuArray'] = array_filter(
			$_SESSION['adminMenuArray'],
			function ($v) {
				if (filter_var($v, FILTER_VALIDATE_INT) !== false) {return true;}
				if (is_string($v) && preg_match('/^[a-zA-Z0-9_-]{1,50}$/', $v)) {return true;}
				return false;
			}
		);

		$_SESSION['adminMenuArray'] = array_values($_SESSION['adminMenuArray']);
			
		
   //---------------------------------------------------------  
   // PROCESS
   //--------------------------------------------------------- 

		if (($key = array_search($idlauch, $_SESSION['adminMenuArray'])) !== false) {
			unset($_SESSION['adminMenuArray'][$key]);
		}else{
			array_push($_SESSION['adminMenuArray'], $idlauch);
		}

        $adminMenuArray = array_values(
            array_unique(
                array_filter(
                    $_SESSION['adminMenuArray'],
                    fn($v) => $v !== null && trim((string)$v) !== ''
                )
            )
        );
			
		$menu=implode(",", $adminMenuArray);

		$_SESSION['adminData']['menuStatus']=1;

        if(count($_SESSION['adminMenuArray'])==0){
            $_SESSION['adminMenuArray']=[];
            $_SESSION['adminData']['menu']='';
        }else{
            $_SESSION['adminMenuArray']=$adminMenuArray;
            $_SESSION['adminData']['menu']=$menu;
        }

        

   //---------------------------------------------------------  
   // DB
   //--------------------------------------------------------- 
			
		$req = "UPDATE ".$prefixAdmin."user SET menu=:menu WHERE id=:adminId";
		$res = $db->prepare($req);
        $res->bindValue(':menu', $menu, PDO::PARAM_STR);
		$res->bindValue(':adminId', $adminId, PDO::PARAM_STR);
		$res->execute();
		$res->closeCursor();
		$res = NULL;
			
		
   //---------------------------------------------------------  
   // RESPONSE
   //--------------------------------------------------------- 
 
		$response = [
			'status' => 1,
			'menuList' => $menu
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;