<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */

    //---------------------------------------------------------  
	// CLASS ROOT MANAGER
	//--------------------------------------------------------- 

    namespace Front;
    use PDO;
    use InvalidArgumentException;
    use Tools\FileOwnerInterface;
    use Tools\ImgManager;
    

    class LangManager implements FileOwnerInterface{
        protected PDO $pdo;
        protected array $opts = [
            'table_lang' => 'root_lang'
        ];


        //---------------------------------------------------------
        // UTILITAIRES
        //--------------------------------------------------------- 

            protected function validateTableName(string $n): string {
                if (!preg_match('/^[a-zA-Z0-9_]+$/', $n)) {
                    throw new InvalidArgumentException("Nom de table invalide");
                }
                return $n;
            }

            public function deleteDir(string $dirPath): void {
                if (! is_dir($dirPath)) {
                    throw new InvalidArgumentException("$dirPath must be a directory");
                }
                if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
                    $dirPath .= '/';
                }
                $files = glob($dirPath . '*', GLOB_MARK);
                foreach ($files as $file) {
                    if (is_dir($file)) {
                        $this->deleteDir($file);
                    } else {
                        unlink($file);
                    }
                }
                rmdir($dirPath);
            }

        //---------------------------------------------------------
        // CONSTRUCT
        //---------------------------------------------------------

            public function __construct(PDO $pdo, array $translations = [], array $options = []){

                $this->pdo = $pdo;
                $this->opts = array_merge($this->opts, $options);

                $this->opts['table_lang'] = $this->validateTableName($this->opts['table_lang']);

            }


        //---------------------------------------------------------  
        // LANGUAGE DB
        //---------------------------------------------------------

            public function listLang(): ?array{
                
                $req = "SELECT * FROM ".$this->opts['table_lang']." ORDER BY position ASC";	
                $res = $this->pdo->prepare($req);
                $res->execute();
                $tab=$res->fetchAll();
                
                return $tab?:[];
            }

            public function activeListLang(): ?array{
                
                $req = "SELECT * FROM ".$this->opts['table_lang']." WHERE status=1 ORDER BY position ASC";	
                $res = $this->pdo->prepare($req);
                $res->execute();
                $tab=$res->fetchAll();
                
                return $tab?:[];
            }


            public function infoLang(int $idLang): ?object{
                
                $req = "SELECT * FROM ".$this->opts['table_lang']." WHERE id=:idLang";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idLang', $idLang, PDO::PARAM_INT);
                $res->execute();
                $r=$res->fetch(PDO::FETCH_OBJ);
                
                return $r?:null;
            }


        //---------------------------------------------------------  
        // FUNCTIONS DOCUMENT UPLOAD
        //--------------------------------------------------------- 

            public function insertDocument(int $id, int $idParent, string $filename): void{}
            public function deleteDocument(int $id, int $idSite): void {}


        //---------------------------------------------------------  
        // FUNCTIONS IMAGE/FILE
        //--------------------------------------------------------- 

            public function getFile(int $id, string $field): string{
                $infoFile=$this->infoLang($id);
                $currentFile=$infoFile->$field;
                return $currentFile?:'';
            }

            public function updateFile(int $id, string $field, string $target, string $filename): void {
                $req = "UPDATE ".$this->opts['table_lang']." SET ".$field."=:filename WHERE ".$target."=:id";
				$res = $this->pdo->prepare($req);
				$res->bindValue(':filename', $filename, PDO::PARAM_STR);
				$res->bindValue(':id', $id, PDO::PARAM_STR);
				$res->execute();
				$res->closeCursor();
				$res = NULL;
            }

            public function resetFile(int $id, string $field, string $target): void {
                $req = "UPDATE ".$this->opts['table_lang']." SET ".$field."= NULL WHERE ".$target."=:id";
				$res = $this->pdo->prepare($req);
				$res->bindValue(':id', $id, PDO::PARAM_STR);
				$res->execute();
				$res->closeCursor();
				$res = NULL;
            }

            public function getFilePath(int $idCategory, string $folder): string {
                return '/img/root/'.$folder.'/files/';
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
               return '/img/l/';
            }  

            public function getImagePath(int $idCategory, string $folder): string {
                return '/img/l/';
            }

            public function getRecPath(int $idCategory, string $folder): string {
                return '/img/l/';
            }

            public function getSquarePath(int $idCategory, string $folder): string {
                return '/img/l/';
            }
            
            public function getMobilePath(int $idCategory, string $folder): string {
                return '';
            }

            public function imgTreatment(string $fileName, int $idCategory, string $field, string $folder): void{
                   
            }  


    }