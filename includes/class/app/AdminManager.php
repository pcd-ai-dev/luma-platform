<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */

    //---------------------------------------------------------  
	// CLASS APP ADMIN MANAGER
	//--------------------------------------------------------- 

    namespace App;
    use PDO;
    use InvalidArgumentException;
    use Exception;
    use Tools\FileOwnerInterface;
    use Tools\ImgManager;

    class AdminManager implements FileOwnerInterface {
        protected PDO $pdo;
        protected array $opts = [
            'table_user' => 'admin_user',
            'table_session' => 'admin_session',
        ];
        
        private array $translations;

        //---------------------------------------------------------
        // UTILITAIRES
        //--------------------------------------------------------- 

            protected function validateTableName(string $n): string {
                if (!preg_match('/^[a-zA-Z0-9_]+$/', $n)) {
                    throw new InvalidArgumentException("Nom de table invalide");
                }
                return $n;
            }

        //---------------------------------------------------------
        // CONSTRUCT
        //---------------------------------------------------------

            public function __construct(PDO $pdo, array $options = []){

                $this->pdo = $pdo;
                $this->opts = array_merge($this->opts, $options);

                $this->opts['table_user'] = $this->validateTableName($this->opts['table_user']);
                $this->opts['table_session'] = $this->validateTableName($this->opts['table_session']);

            }


        //---------------------------------------------------------  
        // ADMIN USER INFO
        //--------------------------------------------------------- 

            public function getAdminInfo(int $adminId): ?object{
                
                $req = "SELECT * FROM ".$this->opts['table_user']." WHERE id=:adminId";
                $res = $this->pdo->prepare($req);
                $res->bindValue(':adminId', $adminId, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);
            
                return $r?:null;	
            }


        //---------------------------------------------------------  
        // FUNCTIONS DOCUMENT UPLOAD
        //--------------------------------------------------------- 

            public function insertDocument(int $id, int $idParent, string $filename): void{
            }

            public function deleteDocument(int $id, int $idSite): void {
            }

        
        //---------------------------------------------------------  
        // FUNCTIONS IMAGE/FILE
        //--------------------------------------------------------- 

            public function getFile(int $id, string $field): string{
                $infoFile=$this->getAdminInfo($id);
                $currentFile=$infoFile->$field;
                return $currentFile?:'';
            }

            public function updateFile(int $id, string $field, string $target, string $filename): void {
                $req = "UPDATE ".$this->opts['table_user']." SET ".$field."=:filename WHERE ".$target."=:id";
				$res = $this->pdo->prepare($req);
				$res->bindValue(':filename', $filename, PDO::PARAM_STR);
				$res->bindValue(':id', $id, PDO::PARAM_STR);
				$res->execute();
				$res->closeCursor();
				$res = NULL;
            }

            public function resetFile(int $id, string $field, string $target): void {
                $req = "UPDATE ".$this->opts['table_user']." SET ".$field."= NULL WHERE ".$target."=:id";
				$res = $this->pdo->prepare($req);
				$res->bindValue(':id', $id, PDO::PARAM_STR);
				$res->execute();
				$res->closeCursor();
				$res = NULL;
            }

            public function getFilePath(int $idCategory, string $folder): string {
                return '';
            }


        //---------------------------------------------------------  
        // FUNCTIONS IMAGE TREATMENT
        //---------------------------------------------------------

            public function getGalleryImage(int $id): ?string {     
                return '';
            }

            public function insertImage(int $id, int $idCategory, string $filename): void {
            }

            public function updateImage(int $id, string $field, string $target, string $filename): void {
            }

            public function deleteImage(int $id): void {
            }

            public function getFolderPath(int $idSite, string $folder): string {
                return '';
            }  

            public function getImagePath(int $idCategory, string $folder): string {
                return '/img/admin/images/';
            }

            public function getRecPath(int $idCategory, string $folder): string {
                return '';
            }

            public function getSquarePath(int $idCategory, string $folder): string {
                return '/img/admin/square/';
            }
            
            public function getMobilePath(int $idCategory, string $folder): string {
                return '';
            }

            public function imgTreatment(string $fileName, int $idCategory, string $field, string $folder): void{

                $img= new ImgManager($this->pdo);

                $imagePath=$_SERVER['DOCUMENT_ROOT'].$this->getImagePath($idCategory, $folder);
                $squarePath=$_SERVER['DOCUMENT_ROOT'].$this->getSquarePath($idCategory, $folder);
                $recPath=$_SERVER['DOCUMENT_ROOT'].$this->getRecPath($idCategory, $folder);

                $extension = strtolower(pathinfo($imagePath.$fileName, PATHINFO_EXTENSION));

                if($field=='img'){
                     if($extension=='svg'){
                        copy($imagePath.$fileName, $recPath.$fileName);
                        copy($imagePath.$fileName, $squarePath.$fileName);
                     }else{
                        $img->imgResizeWidth($imagePath,$fileName,1200);
                        $img->imgProfile($imagePath.$fileName,$squarePath,$fileName,300, 300, 300, 300, "#151515", 80);
                     }
                } else if($field=='imgSquare'){
                     if($extension=='svg'){
                        copy($imagePath.$fileName, $squarePath.$fileName);
                     }else{
                        $img->imgResizeWidth($imagePath,$fileName,1200);
                        $img->imgProfile($imagePath.$fileName,$squarePath,$fileName,300, 300, 300, 300, "#151515", 80);
                     }
                }
                
            }


}