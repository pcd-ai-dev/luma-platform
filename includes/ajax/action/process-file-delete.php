<?php declare(strict_types=1);

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */


    //---------------------------------------------------------
   	// INIT NAME SPACES
   	//---------------------------------------------------------

        use Tools\FileOwnerInterface;
        use Tools\SecureManager;

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

        $entity=$secure->v('string', 'entity', false) ?? '';
        $id=$secure->v('int', 'id', false) ?? 0;
        $field=$secure->v('string', 'field', false) ?? '';
        $target=$secure->v('string', 'target', false) ?? '';
        $folder=$secure->v('string', 'folder', false) ?? '';
        $idParent=$secure->v('int', 'idParent', false) ?? 0;


   //---------------------------------------------------------  
   // PROCESS
   //---------------------------------------------------------

        try {

            if (!$entity || !$id) {
                throw new Exception('Paramètres manquants');
            }

             $entities = require PROCESSPATH.'ajax/action/process-entities.php';

            if (!isset($entities[$entity])) {
                throw new Exception('Entity non autorisée');
            }

            $managerClass = $entities[$entity]['manager'];
            $manager = new $managerClass($db);

            if (!$manager instanceof FileOwnerInterface) {
                throw new Exception('Manager invalide');
            }

            //---------------------------------------------------------
            // CURENT IMAGE INFO
            //---------------------------------------------------------

                if($folder!='gallery'){
                    $currentFile = $manager->getFile($id, $field);
                }else{
                    $currentFile=$manager->getGalleryImage($id);
                }

                if(($currentFile)&&($folder!='files')){
                        // Delete IMG
                        $fullImagePath = $_SERVER['DOCUMENT_ROOT'].$manager->getImagePath($idParent, $folder).$currentFile;
                        if (is_file($fullImagePath)) {unlink($fullImagePath);}

                        // Delete IMG REC
                        $fullRecPath = $_SERVER['DOCUMENT_ROOT'].$manager->getRecPath($idParent, $folder).$currentFile;
                        if (is_file($fullRecPath)) {unlink($fullRecPath);}

                        // Delete IMG SQUARE
                        $fullSquarePath = $_SERVER['DOCUMENT_ROOT'].$manager->getSquarePath($idParent, $folder).$currentFile;
                        if (is_file($fullSquarePath)) {unlink($fullSquarePath);}
                }

                if(($currentFile)&&($folder=='files')){
                        // Delete IMG
                        $fullFilesPath = $_SERVER['DOCUMENT_ROOT'].$manager->getFilePath($idParent, $folder).$currentFile;
                        if (is_file($fullFilesPath)) {unlink($fullFilesPath);}
                }


            //---------------------------------------------------------
            // RESET IMG DB
            //---------------------------------------------------------

                if($folder=='gallery'){
                    $manager->deleteImage($id);
                }else{
                    $manager->resetFile($id, $field, $target);
                }

                $response = [
                    'error' => false,
                    'status' => 1
                ];

		        echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
                exit;

        } catch (Throwable $e) {

            //---------------------------------------------------------
            // ERROR RESPONSE
            //---------------------------------------------------------

                $response = [
                    'error' => true,
                    'status' => 0,
                    'message' => $e->getMessage()
                ];

		        echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
                exit;
        }