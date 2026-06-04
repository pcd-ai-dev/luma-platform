<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */

    //---------------------------------------------------------  
	// CLASS COMMON INTERFACE IMAGE FORM MANAGER
	//--------------------------------------------------------- 

        namespace Tools;

        use PDO;
        use Exception;
        use Tools\FileOwnerInterface;
        use Tools\SecureManager;

        class FileUploadManager{
            
            private array $entities;
            private string $docRoot;

            public function __construct(private PDO $db){
                $this->docRoot = $_SERVER['DOCUMENT_ROOT'];
                $this->entities = require $_SERVER['DOCUMENT_ROOT'].'/includes/ajax/action/process-entities.php';
            }

        //---------------------------------------------------------
        // SINGLE IMG
        //--------------------------------------------------------- 

            public function handleUpload(string $entity, int $id, int  $idParent, string $field, string $target, string $folder, array $file): array{
                
                if (!isset($this-> entities[$entity])) {throw new Exception('Entity non autorisée');}

                if (empty($file['tmp_name'])) {throw new Exception('Fichier manquant');}

                $manager = $this->createManager($entity);
                $this->validateFile($file);

                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $filename = $this->entities[$entity]['prefix'] . uniqid() . '.' . $ext;


                // UPLOAD PROCESS
                $uploadPath = $this->docRoot . $manager->getImagePath($idParent, $folder);

                if (!is_dir($uploadPath)) {
                    throw new Exception('Répertoire invalide : '.$uploadPath);
                }

                $oldImage = $manager->getFile($id, $field);

                $secure=new SecureManager();

                //$secure->upload($_FILES['file'], $uploadPath, ['jpg', 'jpeg','png','pdf','heic']);

                if (!move_uploaded_file($file['tmp_name'], $uploadPath.$filename)) {
                    throw new Exception('Erreur upload');
                }

                //IMG TREATMENT
                $manager->updateFile($id, $field, $target, $filename);

                $manager->imgTreatment($filename, $idParent, $field, $folder);

                $this->cleanupOldImage($oldImage, $idParent, $field, $folder, $manager);

                return [
                    'error' => false,
                    'output' => 'img',
                    'square'  => $manager->getSquarePath($idParent, $folder) . $filename,
                    'rec'  => $manager->getRecPath($idParent, $folder) . $filename,
                    'mobile'  => $manager->getMobilePath($idParent, $folder) . $filename
                ];
            }


        //---------------------------------------------------------
        // GALLERY INSERT IMG
        //--------------------------------------------------------- 

            public function handleGalleryUpload(string $entity, int $id, int  $idParent, string $field, string $target, string $folder, array $file): array{
                
                if (!isset($this-> entities[$entity])) {throw new Exception('Entity non autorisée');}

                if (empty($file['tmp_name'])) {throw new Exception('Fichier manquant');}

                $manager = $this->createManager($entity);
                $this->validateFile($file);

                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $filename = $this->entities[$entity]['prefix'] . uniqid() . '.' . $ext;


                // UPLOAD PROCESS
                $uploadPath = $this->docRoot . $manager->getImagePath($idParent, $folder);

                if (!is_dir($uploadPath)) {throw new Exception('Répertoire invalide : '.$uploadPath. ' - '.$idParent. ' - '.$folder);}

                if (!move_uploaded_file($file['tmp_name'], $uploadPath . $filename)) {throw new Exception('Erreur upload');}

                //IMG TREATMENT
                $manager->insertImage($id, $idParent, $filename);

                $manager->imgTreatment($filename, $idParent, $field, $folder);

                return [
                    'error' => false,
                    'output' => 'img'
                ];
            }

        //---------------------------------------------------------
        // GALLERY UPDATE IMG
        //--------------------------------------------------------- 

            public function handleImgUpdateUpload(string $entity, int $id, int  $idParent, string $field, string $target, string $folder, array $file): array{
                
                if (!isset($this-> entities[$entity])) {throw new Exception('Entity non autorisée');}

                if (empty($file['tmp_name'])) {throw new Exception('Fichier manquant');}

                $manager = $this->createManager($entity);
                $this->validateFile($file);

                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $filename = $this->entities[$entity]['prefix'] . uniqid() . '.' . $ext;


                // UPLOAD PROCESS
                $uploadPath = $this->docRoot . $manager->getImagePath($idParent, $folder);

                if (!is_dir($uploadPath)) {throw new Exception('Répertoire invalide Update : '.$uploadPath. ' - '.$idParent. ' - '.$folder);}

                if (!move_uploaded_file($file['tmp_name'], $uploadPath . $filename)) {throw new Exception('Erreur upload');}

                //IMG TREATMENT
                $manager->updateImage($id, $field, $target, $filename);

                $manager->imgTreatment($filename, $idParent, $field, $folder);

                return [
                    'error' => false,
                    'output' => 'img',
                    'square'  => $manager->getSquarePath($idParent, $folder) . $filename
                ];
            }


        //---------------------------------------------------------
        // DOCUMENT LIST UPLOAD
        //--------------------------------------------------------- 

            public function handleDocumentListUpload(string $entity, int $id, int  $idSite, string $field, string $target, string $folder, array $file): array{
                
                if (!isset($this-> entities[$entity])) {throw new Exception('Entity non autorisée');}

                if (empty($file['tmp_name'])) {throw new Exception('Fichier manquant');}

                $manager = $this->createManager($entity);
                $this->validateFile($file);

                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $filename = $this->entities[$entity]['prefix'] . uniqid() . '.' . $ext;

                // UPLOAD PROCESS
                $uploadPath = $this->docRoot . $manager->getFolderPath($idSite, $folder);

                if (!is_dir($uploadPath)) {throw new Exception('Répertoire invalide : '. $uploadPath);}

                if (!move_uploaded_file($file['tmp_name'], $uploadPath . $filename)) {throw new Exception('Erreur upload');}

                //IMG TREATMENT
                $manager->insertDocument($id, $idSite, $filename);

                return [
                    'error' => false,
                    'idParent' => $id,
                    'output' => 'file'
                ];
            }


        //---------------------------------------------------------
        // DOCUMENT SINGLE UPLOAD
        //--------------------------------------------------------- 

            public function handleDocumentUpload(string $entity, int $id, int  $idParent, string $field, string $target, string $folder, array $file): array{
                
                if (!isset($this-> entities[$entity])) {throw new Exception('Entity non autorisée');}

                if (empty($file['tmp_name'])) {throw new Exception('Fichier manquant');}

                $manager = $this->createManager($entity);
                $this->validateFile($file);

                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $filename = $this->entities[$entity]['prefix'] . uniqid() . '.' . $ext;

                // UPLOAD PROCESS
                $uploadPath = $this->docRoot . $manager->getFilePath($idParent, $folder);

                $oldFile = $manager->getFile($id, $field);

                if (!is_dir($uploadPath)) {throw new Exception('Répertoire invalide: '.$uploadPath);}

                if (!move_uploaded_file($file['tmp_name'], $uploadPath . $filename)) {throw new Exception('Erreur upload');}

                //IMG TREATMENT
                $manager->updateFile($id, $field, $target, $filename);
                $this->cleanupOldFile($oldFile, $idParent, $field, $folder, $manager);

                $map = [
                    'pdf' => 'pdf.svg',
                    'doc' => 'doc.svg',
                    'docx' => 'doc.svg',
                    'xls' => 'xls.svg',
                    'xlsx' => 'xls.svg',
                    'jpg' => 'jpg.svg',
                    'jpeg' => 'jpg.svg',
                    'png' => 'png.svg',
                    'zip' => 'zip.svg',
                ];

                $extension = strtolower(pathinfo($uploadPath.$filename, PATHINFO_EXTENSION));
                $icon = $map[$extension] ?? 'other.svg';
                $iconDisplay = "/img/interface/default/" . $icon;

                return [
                    'error' => false,
                    'output' => 'img',
                    'square'  => $iconDisplay
                ];
  
            }


        //---------------------------------------------------------
        // CREATE MANAGER
        //--------------------------------------------------------- 

            private function createManager(string $entity): FileOwnerInterface {
                $class = $this->entities[$entity]['manager'];
                $manager = new $class($this->db);

                if (!$manager instanceof FileOwnerInterface) {
                    throw new Exception('Manager invalide');
                }

                return $manager;
            }


        //---------------------------------------------------------
        // VALIDATE FILE
        //--------------------------------------------------------- 

            private function validateFile(array $file): void {
                if ($file['size'] > 10_000_000) {
                    throw new Exception('Fichier trop volumineux');
                }

                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg','jpeg','png','gif','heic','svg','pdf','doc','docx'], true)) {
                    throw new Exception('Format non autorisé');
                }
            }

        //---------------------------------------------------------
        // CLEAN UP OLD IMG
        //---------------------------------------------------------

            private function cleanupOldImage(?string $file, int $idParent, string $field, string $folder, FileOwnerInterface $manager): void             {
                if (!$file) return;

                if($field=='rec'){
                    $repArray=['rec'];
                } else if($field=='square'){
                    $repArray=['square'];
                }else{
                    $repArray=['images','square','rec'];
                }

                $base = $this->docRoot . $manager->getFolderPath($idParent,$folder);
                foreach ($repArray as $dir) {
                    $path = $base . $dir . '/' . $file;
                    if (is_file($path)) {
                        @unlink($path);
                    }
                }

            }


        //---------------------------------------------------------
        // CLEAN UP OLD FILE
        //---------------------------------------------------------

            private function cleanupOldFile(?string $file, int $idParent, string $field, string $folder, FileOwnerInterface $manager): void             {
                if (!$file) return;

                $base = $this->docRoot . $manager->getFolderPath($idParent,$folder);
                $path = $base.$file;
                if (is_file($path)) {@unlink($path);}
            }


        }