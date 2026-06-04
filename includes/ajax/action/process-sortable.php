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
   // INIT CLASS SECURE
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
		$list=$secure->v('array', 'list', false) ?? [];

        
   //---------------------------------------------------------  
   // INIT CLASS
   //---------------------------------------------------------

		$action= new ActionManager($db, $table, $field, $target, $type);


   //---------------------------------------------------------  
   // PROCESS
   //---------------------------------------------------------

   		$action->sortable($list);


    //---------------------------------------------------------
    // RESPONSE
    //---------------------------------------------------------

		$response = [
			'error' => false
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;