<?php declare(strict_types=1);

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */


    //---------------------------------------------------------  
   	// SESSION STATUS / HEADER
   	//---------------------------------------------------------

		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

		header('Content-Type: application/json');


   //---------------------------------------------------------  
   // VARIABLES
   //--------------------------------------------------------- 

		$pageToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';


   //---------------------------------------------------------
   // RESPONSE
   //---------------------------------------------------------

        $response = [
			'valid' => true
		];

	
    //---------------------------------------------------------  
	// EXPIRED SESSION
	//---------------------------------------------------------

		if (empty($_SESSION['csrf'])) {

			$response['valid'] = false;
			$response['reason'] = 'session_lost';

			echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
			exit;
		}


	//---------------------------------------------------------
  	// DIFFERENT TOKEN
   	//---------------------------------------------------------
 
		if (
			empty($pageToken) ||
			!hash_equals($_SESSION['csrf'], $pageToken)
		) {

			$response['valid'] = false;
			$response['reason'] = 'token_changed';

			echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
			exit;
		}

	//---------------------------------------------------------
  	// RESPONSE
   	//---------------------------------------------------------

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;