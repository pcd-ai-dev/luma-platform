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
        use Action\ActionManager;

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
          
      $id=$secure->v('int', 'id', false) ?? 0;
   	  $table=$secure->v('string', 'table', false) ?? '';
		  $field=$secure->v('string', 'field', false) ?? '';
		  $target=$secure->v('string', 'target', false) ?? '';
      $output=$secure->v('string', 'output', false) ?? '';
      $type=$secure->v('string', 'type', false) ?? '';

        
   //---------------------------------------------------------  
   // INIT CLASS
   //---------------------------------------------------------

	    $action= new ActionManager($db, $table, $field, $target, $type);


   //---------------------------------------------------------  
   // PROCESS
   //---------------------------------------------------------

   		$newStatus = $action->toggleStatus($id);

      if($output=="red"){
        $outputRender = $newStatus == 0 ? '/img/interface/icons/statusOff.png' : '/img/interface/icons/statusOnRed.png';
      } else if($output=="light"){
        $outputRender = $newStatus == 0 ? 'style="color:#b9b9b9;"' : 'style="color:green;"';
      } else if($output=="pen"){
        $outputRender = $newStatus == 0 ? 'style="color:#b9b9b9;"' : 'style="color:green;"';
       } else if($output=="signUp"){
        $outputRender = $newStatus == 0 ? '/img/interface/btn/statusFormOff.svg' : '/img/interface/btn/statusFormOn.svg';
      } else {
        $outputRender = $newStatus == 0 ? '/img/interface/icons/statusOff.png' : '/img/interface/icons/statusOnGreen.png';
      }


    //---------------------------------------------------------
    // RESPONSE
    //---------------------------------------------------------

		$response = [
			'status' => $newStatus,
			'output' => $output,
			'outputRender' => $outputRender
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;