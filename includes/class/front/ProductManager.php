<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */

    //---------------------------------------------------------  
	// CLASS FRONT PRODUCT MANAGER
	//--------------------------------------------------------- 

    namespace Front;
    use PDO;
    use InvalidArgumentException;
    use Tools\FileOwnerInterface;
    use Tools\ImgManager;
    
    class ProductManager implements FileOwnerInterface {
        protected PDO $pdo;
        protected array $opts = [
            'table_item' => 'product_item',
            'table_lang' => 'product_lang',
            'table_img' => 'product_img',
            'table_img_lang' => 'product_img_lang'
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

            public function __construct(PDO $pdo, array $translations = [], array $options = []){

                $this->pdo = $pdo;
                $this->opts = array_merge($this->opts, $options);

                $this->opts['table_item'] = $this->validateTableName($this->opts['table_item']);
                $this->opts['table_lang'] = $this->validateTableName($this->opts['table_lang']);
                $this->opts['table_img'] = $this->validateTableName($this->opts['table_img']);
                $this->opts['table_img_lang'] = $this->validateTableName($this->opts['table_img_lang']);

                $this->translations = $translations;
            }


        //---------------------------------------------------------
        // FUNCTIONS INFO PRODUCT
        //---------------------------------------------------------

            public function infoProduct(int $idProd): ?object {
                
                $req = "SELECT * FROM ".$this->opts['table_item']." WHERE id=:idProd";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idProd', $idProd, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);
                
                return $r?:null;
            }


            public function productLang(int $idProd, int $idLang): ?object{
                
                $req = "SELECT * FROM ".$this->opts['table_lang']." WHERE idProd=:idProd and idLang=:idLang";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idProd', $idProd, PDO::PARAM_INT);
                $res->bindValue(':idLang', $idLang, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);
                
                return $r?:null;
            }

            public function getProductPhoto(int $idProd): ?string {
                
                $req = "SELECT img FROM ".$this->opts['table_item']." WHERE id=:idProd";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idProd', $idProd, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);
                
                return $r->img?:null;
            }

        //---------------------------------------------------------
        // FUNCTION INFO PRODUCT LIST
        //---------------------------------------------------------

            public function getProductListcat(int $idCategory, int $idLang): ?array{

                $reqprod = "SELECT p.id as modProdId, p.idCategory, p.date, p.status, p.homeStatus, p.print, p.url, p.img, p.square, l.id, l.idProd, l.title, l.city, l.environnement, l.idLang
                            FROM ".$this->opts['table_item']." p
                            INNER JOIN ".$this->opts['table_lang']." l
                            ON p.id=l.idProd
                            AND l.idLang=:idLang
                            WHERE p.idCategory=:idCategory
                            ORDER BY p.position ASC";				
                $resprod = $this->pdo->prepare($reqprod);
                $resprod->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
                $resprod->bindValue(':idLang', $idLang, PDO::PARAM_INT);
                $resprod->execute();
                $tabprod = $resprod->fetchAll();
                
                return $tabprod?:[];
                    
            }

		
        //---------------------------------------------------------
        // FUNCTION POSITION
        //---------------------------------------------------------

            public function recalculPositionProduct(): void{
                
                $req = "SELECT * FROM ".$this->opts['table_item']." ORDER BY position";				
                $res = $this->pdo->prepare($req);
                $res->execute();
                $tab = $res->fetchAll();
                $nb=count($tab);
                
                for($i=0;$i<$nb;$i++){
                    $newposition=$i+1;
                    $reqmod = "UPDATE ".$this->opts['table_item']." SET position=:position WHERE id=:iditem";
                    $resmod = $this->pdo->prepare($reqmod);
                    $resmod->bindValue(':position', $newposition, PDO::PARAM_INT);
                    $resmod->bindValue(':iditem', $tab[$i]['id'], PDO::PARAM_INT);
                    $resmod->execute();
                }
            }

  
        //---------------------------------------------------------  
        // NEXT PROD
        //---------------------------------------------------------

            public function getNextProject(int $idCategory, int $idProd, int $idLang): ?object{

                $req = "SELECT position FROM ".$this->opts['table_item']." WHERE id=:idProd AND idCategory=:idCategory AND status=1";
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idProd', $idProd, PDO::PARAM_INT);
                $res->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
                $res->execute();
                $current = $res->fetch(PDO::FETCH_OBJ);

                if (!$current) {return null;}

                $currentPosition = $current->position;

                $req = "SELECT p.id as nextProdId, l.title 
                        FROM ".$this->opts['table_item']." p
                        INNER JOIN ".$this->opts['table_lang']." l
                            ON p.id = l.idProd
                            AND l.idLang = :idLang
                            AND p.idCategory = :idCategory
                        WHERE p.position > :currentPosition
                        AND status=1
                        ORDER BY p.position ASC
                        LIMIT 1";
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
                $res->bindValue(':idLang', $idLang, PDO::PARAM_INT);
                $res->bindValue(':currentPosition', $currentPosition, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);

                return $r?:null; 

            }


        //---------------------------------------------------------  
        // PREV PROD
        //---------------------------------------------------------

            public function getPrevProject(int $idCategory, int $idProd, int $idLang): ?object{

                $req = "SELECT position FROM ".$this->opts['table_item']." WHERE id=:idProd AND idCategory=:idCategory AND status=1";
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idProd', $idProd, PDO::PARAM_INT);
                $res->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
                $res->execute();
                $current = $res->fetch(PDO::FETCH_OBJ);

                if (!$current) {return null;}

                $currentPosition = $current->position;

                $req = "SELECT p.id as prevProdId, l.title 
                        FROM ".$this->opts['table_item']." p
                        INNER JOIN ".$this->opts['table_lang']." l
                            ON p.id = l.idProd
                            AND l.idLang = :idLang
                            AND p.idCategory = :idCategory
                        WHERE p.position < :currentPosition
                        AND status=1
                        ORDER BY p.position DESC
                        LIMIT 1";

                $res = $this->pdo->prepare($req);
                $res->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
                $res->bindValue(':idLang', $idLang, PDO::PARAM_INT);
                $res->bindValue(':currentPosition', $currentPosition, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);

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
                $infoFile=$this->infoProduct($id);
                $currentFile=$infoFile->$field;
                return $currentFile?:'';
            }

            public function updateFile(int $id, string $field, string $target, string $filename): void {
                $req = "UPDATE ".$this->opts['table_item']." SET ".$field."=:filename WHERE ".$target."=:id";
				$res = $this->pdo->prepare($req);
				$res->bindValue(':filename', $filename, PDO::PARAM_STR);
				$res->bindValue(':id', $id, PDO::PARAM_STR);
				$res->execute();
				$res->closeCursor();
				$res = NULL;
            }

            public function resetFile(int $id, string $field, string $target): void {
                $req = "UPDATE ".$this->opts['table_item']." SET ".$field."= NULL WHERE ".$target."=:id";
				$res = $this->pdo->prepare($req);
				$res->bindValue(':id', $id, PDO::PARAM_STR);
				$res->execute();
				$res->closeCursor();
				$res = NULL;
            }

            public function getFilePath(int $idCategory, string $folder): string {
                return '/tmp/product/'.$idCategory.'/'.$folder.'/files/';
            }

        //---------------------------------------------------------  
        // FUNCTIONS IMAGE TREATMENT
        //--------------------------------------------------------- 

            public function getGalleryImage(int $id): ?string {
                
                $req = "SELECT * FROM ".$this->opts['table_img']." WHERE id=:id";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':id', $id, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);
                
                return $r->img?:'';
            }

            public function insertImage(int $id, int $idCategory, string $filename): void {

                $req = "INSERT INTO ".$this->opts['table_img']." (idProd, img, idCategory) VALUES (:id, :filename, :idCategory)";
				$res = $this->pdo->prepare($req);
				$res->bindValue(':filename', $filename, PDO::PARAM_STR);
                $res->bindValue(':id', $id, PDO::PARAM_STR);
				$res->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
				$res->execute();
				$res->closeCursor();
				$res = NULL;
            }

            public function updateImage(int $id, string $field, string $target, string $filename): void {
                $req = "UPDATE ".$this->opts['table_img']." SET ".$field."=:filename WHERE ".$target."=:id";
				$res = $this->pdo->prepare($req);
				$res->bindValue(':filename', $filename, PDO::PARAM_STR);
				$res->bindValue(':id', $id, PDO::PARAM_STR);
				$res->execute();
				$res->closeCursor();
				$res = NULL;
            }

            public function deleteImage(int $id): void {

                $req = "DELETE  FROM ".$this->opts['table_img']." WHERE id=:id";
				$res = $this->pdo->prepare($req);
				$res->bindValue(':id', $id, PDO::PARAM_STR);
				$res->execute();
				$res->closeCursor();
				$res = NULL;

            }

            public function getFolderPath(int $idCategory, string $folder): string {
                return '/img/product/'.$idCategory.'/'.$folder.'/';
            }     

            public function getImagePath(int $idCategory, string $folder): string {
                return '/img/product/'.$idCategory.'/'.$folder.'/images/';
            }

            public function getRecPath(int $idCategory, string $folder): string {
                return '/img/product/'.$idCategory.'/'.$folder.'/rec/';
            }

            public function getSquarePath(int $idCategory, string $folder): string {
                return '/img/product/'.$idCategory.'/'.$folder.'/square/';
            }
            
            public function getMobilePath(int $idCategory, string $folder): string {
                return '';
            }

            public function imgTreatment(string $fileName, int $idCategory, string $field, string $folder): void{

                $img= new ImgManager($this->pdo);

                $imagePath=$_SERVER['DOCUMENT_ROOT'].$this->getImagePath($idCategory, $folder);
                $squarePath=$_SERVER['DOCUMENT_ROOT'].$this->getSquarePath($idCategory, $folder);
                $recPath=$_SERVER['DOCUMENT_ROOT'].$this->getRecPath($idCategory, $folder);

                 if($field=='rec'){
                    $img->imgProfile($imagePath.$fileName, $recPath, $fileName, 900, 300, 900, 300, "#FFF", 100);
                } else if($field=='square'){
                    $img->imgProfile($imagePath.$fileName, $squarePath, $fileName, 1600, 1600, 1600, 1600, "#FFF", 100);
                }else{
                    $img->imgProfile($imagePath.$fileName,$squarePath,$fileName,300, 300, 300, 300, "#151515", 100);
                    $img->imgRectangle($imagePath.$fileName,$recPath,$fileName,1024, 576, 1024, 576, "#151515", 100);
                }
                   
            }

    }