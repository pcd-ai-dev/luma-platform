<?php declare(strict_types=1);

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */


    //---------------------------------------------------------
   	// INIT NAME SPACES
   	//---------------------------------------------------------

        use Tools\FileUploadManager;
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
        $folder=$secure->v('string', 'folder', false) ?? '';
        $field=$secure->v('string', 'field', false) ?? '';
        $target=$secure->v('string', 'target', false) ?? '';
        $id=$secure->v('int', 'id', false) ?? 0;
        $idParent=$secure->v('int', 'idParent', false) ?? 0;
        $type=$secure->v('int', 'type', false) ?? 0;
        $idSite=$secure->v('int', 'idSite', false) ?? 0;


   //---------------------------------------------------------  
   // PROCESS
   //---------------------------------------------------------

        try {

            if (!$entity || !$id) {
                throw new Exception('Paramètres manquants');
            }

            if (empty($_FILES['file'])) {
                throw new Exception('Aucun fichier envoyé');
            }

            $uploadManager = new FileUploadManager($db);

            if($type==1){ // SINGLE IMG
                $result = $uploadManager->handleUpload($entity, $id, $idParent, $field, $target, $folder, $_FILES['file']);
            } else if($type==2){ // GALLERY IMG
                $result = $uploadManager->handleGalleryUpload($entity, $id, $idParent, $field, $target, $folder, $_FILES['file']);
            } else if($type==3){ // SINGLE DOC
                $result = $uploadManager->handleDocumentUpload($entity, $id, $idParent, $field, $target, $folder, $_FILES['file']);
            } else if($type==4){ // LIST DOC
                $result = $uploadManager->handleDocumentListUpload($entity, $id, $idSite, $field, $target, $folder, $_FILES['file']);
             } else if($type==5){ // UPDATE IMG
                $result = $uploadManager->handleImgUpdateUpload($entity, $id, $idParent, $field, $target, $folder, $_FILES['file']);
            }else{
                $result = null;
            }


            //---------------------------------------------------------
            // RESPONSE
            //---------------------------------------------------------

                echo json_encode($result, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
                exit;

        } catch (Exception $e) {

            //---------------------------------------------------------
            // RESPONSE
            //---------------------------------------------------------

                $response = [
                    'formContent' => [
                        'error'   => true,
                        'type'    => $type,
                        'message' => $e->getMessage()
                    ]
                ];

                echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
                exit;

        }