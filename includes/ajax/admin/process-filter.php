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

		$id      = $secure->v('int', 'id', false) ?? 0;
		$adminId = $secure->v('int', 'adminId', false) ?? 0;

		if (!isset($_SESSION['adminFilterArray']) || !is_array($_SESSION['adminFilterArray'])) {$_SESSION['adminFilterArray'] = [];}

		$_SESSION['adminFilterArray'] = array_filter(
			$_SESSION['adminFilterArray'],
			function ($v) {
				if (filter_var($v, FILTER_VALIDATE_INT) !== false) {return true;}
				if (is_string($v) && preg_match('/^[a-zA-Z0-9_-]{1,50}$/', $v)) {return true;}
				return false;
			}
		);

		$_SESSION['adminFilterArray'] = array_values($_SESSION['adminFilterArray']);

		
   //---------------------------------------------------------  
   // PROCESS
   //--------------------------------------------------------- 
			
		if (($key = array_search($id, $_SESSION['adminFilterArray'])) !== false) {
			unset($_SESSION['adminFilterArray'][$key]);
		}else{
			array_push($_SESSION['adminFilterArray'], $id);
		}
			
		$output=implode(",", $_SESSION['adminFilterArray']);

		$nbOutput=count($_SESSION['adminFilterArray']);
		$nbo=0;
			
	
   //---------------------------------------------------------  
   // DB
   //--------------------------------------------------------- 
			
		$req = "UPDATE ".$prefixAdmin."user SET filter=:filter WHERE id=:adminId";
		$res = $db->prepare($req);
		$res->bindValue(':filter', $output, PDO::PARAM_STR);
		$res->bindValue(':adminId', $adminId, PDO::PARAM_STR);
		$res->execute();
		$res->closeCursor();
		$res = NULL;


  	//---------------------------------------------------------  
	// UPDATE OWNERLIST
	//--------------------------------------------------------- 
  
	// DB
		if($nbOutput!=0){
			$reqo="SELECT * FROM ".$prefixAdmin."user WHERE id IN($output) AND type IN(0,1) AND status=1 ORDER BY position ASC";
			$reso = $db->prepare($reqo);
			$reso->execute();
			$nbo=$reso->rowCount();
			$tabo = $reso->fetchAll();
			$reso->closeCursor();
			$reso = NULL;
		}

	// LIST
		$ownerListArray=array();
		for($i=0;$i<$nbo;$i++){
			$photoDisplay=(empty($tabo[$i]['img']))? $imgDefaultSquare : $tabo[$i]['img'];
			$ownerListArray[]=array('key'=>$tabo[$i]['id'], 'label'=>mb_convert_encoding($tabo[$i]['first_name'].'.'.$tabo[$i]['last_name'][0], 'UTF-8'), 'img'=>'/img/admin/square/'.$photoDisplay, 'phone'=>$tabo[$i]['phone']);
		}

		
   //---------------------------------------------------------  
   // RESPONSE
   //--------------------------------------------------------- 
 
		$response = [
			'status' => 1,
			'output' => $nbOutput,
			'adminId' => $adminId,
			'ownerList' => $ownerListArray 
		];

		header('Content-Type: application/json');
		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;