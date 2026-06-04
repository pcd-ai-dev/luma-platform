<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */

    //---------------------------------------------------------  
	// CLASS FRONT PHOTO MANAGER
	//--------------------------------------------------------- 

    namespace Front;
    use PDO;
    use InvalidArgumentException;
    use Tools\FileOwnerInterface;
    use Tools\ImgManager;

    class PhotoManager implements FileOwnerInterface {
        protected PDO $pdo;
        protected array $opts = [
            'table_item' => 'gallery_item',
            'table_lang' => 'gallery_lang',
            'table_img' => 'gallery_img',
            'table_img_lang' => 'gallery_img_lang'
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
        // Function gallery
        //---------------------------------------------------------

            public function listGalleryPhoto(int $idCategory): ?array{

                $req = "SELECT p.id, p.url, p.img, p.date, p.idCategory, p.status, p.img,l.id, l.idGallery, l.idLang, l.title, l.description
                        FROM ".$this->opts['table_item']." p
                        INNER JOIN ".$this->opts['table_lang']." l
                        ON p.id=l.idGallery
                        AND l.idLang='1'
                        WHERE p.idCategory=:idCategory
                        ORDER BY p.position asc";
                $res = $this->pdo->prepare($req);
                $res->bindParam(':idCategory', $idCategory, PDO::PARAM_INT);
                $res->execute();
                $tab = $res->fetchAll();
                
                return $tab?:[];
            }
   
            public function checkPositionGallery(int $position): ?int{
                
                $req = "SELECT * FROM ".$this->opts['table_item']." WHERE position=:position";			 
                $res = $this->pdo->prepare($req);
                $res->bindValue(':position', $position, PDO::PARAM_INT);
                $res->execute();
                $tab = $res->fetchAll();
                $nb=count($tab);
                
                return $nb?:0;
            }
	

            public function maxPositionGallery(): ?int{
                
                $req = "SELECT Max(position) AS pos FROM ".$this->opts['table_item'];
                $res = $this->pdo->prepare($req);
                $res->execute();		
                $r = $res->fetch(PDO::FETCH_OBJ);

                return $r->pos?:0;
                   
            }
	
            public function recalculPositionGallery(): void{

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
        // Récupération info page par id
        //---------------------------------------------------------

            public function infoGalleryType(int $idGallery, int $idLang): ?object{
                    
                    $req = "SELECT * FROM ".$this->opts['table_lang']." WHERE idGallery=:idGallery AND idLang=:idLang";	
                    $res = $this->pdo->prepare($req);
                    $res->bindValue(':idGallery', $idGallery, PDO::PARAM_INT);
                    $res->bindValue(':idLang', $idLang, PDO::PARAM_INT);
                    $res->execute();
                    $r = $res->fetch(PDO::FETCH_OBJ);
                    
                    return $r?:null;
                }


        //---------------------------------------------------------
        // InfiImgGallery
        //---------------------------------------------------------
            
            public function galleryInfo(int $idGallery): ?object{
                
                $req = "SELECT * FROM ".$this->opts['table_item']." WHERE id=:idGallery";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idGallery', $idGallery, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);
                
                return $r?:null;
            }

		
        //---------------------------------------------------------  
        // Récupération texte gallery
        //---------------------------------------------------------
            
            public function galleryLang(int $idGallery, int $idLang): ?object{
                
                $req = "SELECT * FROM ".$this->opts['table_lang']." WHERE idGallery=:idGallery AND idLang=:idLang";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idGallery', $idGallery, PDO::PARAM_INT);
                $res->bindValue(':idLang', $idLang, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);
                
                return $r?:null;
            }


        //---------------------------------------------------------
        // IngInfo
        //---------------------------------------------------------
		
            public function galleryImg(int $id): ?object{
                
                $req = "SELECT * FROM ".$this->opts['table_img']." WHERE id=:id";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':id', $id, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);
                
                return $r?:null;
            }
		
		
        //---------------------------------------------------------
        // Récupération texte gallery img lang
        //---------------------------------------------------------
		
            public function galleryImgLang(int $idImg, int $idLang): ?object{
                
                $req = "SELECT * FROM ".$this->opts['table_img_lang']." WHERE idImg=:idImg AND idLang=:idLang";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idImg', $idImg, PDO::PARAM_INT);
                $res->bindValue(':idLang', $idLang, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);
                
                return $r?:null;
            }
		
		
        //---------------------------------------------------------
        // Gallery get photo
        //---------------------------------------------------------
		
            public function getGalleryPhoto(int $idGallery): ?object{
                
                $req = "SELECT * FROM ".$this->opts['table_img']." WHERE idGallery=:idGallery ORDER BY position ASC LIMIT 1";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idGallery', $idGallery, PDO::PARAM_INT);
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
                $infoFile=$this->galleryInfo($id);
                $currentFile=$infoFile->$field;
                return $currentFile?:'';
            }

            public function updateFile(int $id, string $field, string $target, string $filename): void {

                $req = "UPDATE ".$this->opts['table_item']." SET ".$field."=:filename WHERE ".$target."=:id";
				$res = $this->pdo->prepare($req);
				$res->bindValue(':filename', $filename, PDO::PARAM_STR);
				$res->bindValue(':id', $id, PDO::PARAM_INT);
				$res->execute();
				$res->closeCursor();
				$res = NULL;
            }

            public function resetFile(int $id, string $field, string $target): void {
                $req = "UPDATE ".$this->opts['table_item']." SET ".$field."= NULL WHERE ".$target."=:id";
				$res = $this->pdo->prepare($req);
				$res->bindValue(':id', $id, PDO::PARAM_INT);
				$res->execute();
				$res->closeCursor();
				$res = NULL;
            }

            public function getFilePath(int $idCategory, string $folder): string {
                return '/tmp/gallery/'.$idCategory.'/'.$folder.'/files/';
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

                $req = "INSERT INTO ".$this->opts['table_img']." (idGallery, img, idCategory) VALUES (:id, :filename, :idCategory)";
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
				$res->bindValue(':id', $id, PDO::PARAM_INT);
				$res->execute();
				$res->closeCursor();
				$res = NULL;

            }

            public function getFolderPath(int $idCategory, string $folder): string {
                return '/img/gallery/'.$idCategory.'/'.$folder.'/';
            }     

            public function getImagePath(int $idCategory, string $folder): string {
                return '/img/gallery/'.$idCategory.'/'.$folder.'/images/';
            }

            public function getRecPath(int $idCategory, string $folder): string {
                return '/img/gallery/'.$idCategory.'/'.$folder.'/rec/';
            }

            public function getSquarePath(int $idCategory, string $folder): string {
                return '/img/gallery/'.$idCategory.'/'.$folder.'/square/';
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