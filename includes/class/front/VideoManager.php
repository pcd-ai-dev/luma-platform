<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */

    //---------------------------------------------------------  
	// CLASS FRONT VIDEO MANAGER
	//--------------------------------------------------------- 

    namespace Front;
    use PDO;
    use InvalidArgumentException;
    use Exception;
    use Tools\FileOwnerInterface;
    use Tools\ImgManager;

    class VideoManager implements FileOwnerInterface {
        protected PDO $pdo;
        protected array $opts = [
            'table_item' => 'video_item',
            'table_lang' => 'video_lang',
            'table_gallery' => 'video_gallery',
            'table_gallery_lang' => 'video_gallery_lang'
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
                $this->opts['table_gallery'] = $this->validateTableName($this->opts['table_gallery']);
                $this->opts['table_gallery_lang'] = $this->validateTableName($this->opts['table_gallery_lang']);

                $this->translations = $translations;
            }


        //---------------------------------------------------------
        // FUNCTION POSITION
        //---------------------------------------------------------
	
            public function recalculPositionVideo(): void {

                $req = "SELECT * FROM ".$this->opts['table_item']." ORDER BY position";				
                $res = $this->pdo->prepare($req);
                $res->execute();
                $tab = $res->fetchAll();
                $nb=count($tab);
                
                for($i=0;$i<$nb;$i++){
                    $newposition=$i+1;
                    $reqmod = "UPDATE ".$this->opts['table_item']." SET position=:position WHERE id=:iditem";
                    $resmod = $this->pdo->prepare($reqmod);
                    $resmod->bindValue(':position', $newposition, PDO::PARAM_STR);
                    $resmod->bindValue(':iditem', $tab[$i]['id'], PDO::PARAM_STR);
                    $resmod->execute();
                    $resmod->closeCursor();
                    $resmod = NULL; 
                 }
            }

        //---------------------------------------------------------  
        // FUNCTIONS INFO VIDEO
        //---------------------------------------------------------   
        
            public function infoVideo(int $idVideo): ?object {
                
                $req = "SELECT * FROM ".$this->opts['table_item']." WHERE id=:idVideo";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idVideo', $idVideo, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);
                
                return $r?:null;
            }
	
		
            public function videoLang(int $idVideo, int $idLang): ?object {
                
                $req = "SELECT * FROM ".$this->opts['table_lang']." WHERE idVideo=:idVideo and idLang=:idLang";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idVideo', $idVideo, PDO::PARAM_INT);
                $res->bindValue(':idLang', $idLang, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);
                
                return $r?:null;
            }
		
        
        //---------------------------------------------------------  
        // GALLERRY FUNCTIONS
        //---------------------------------------------------------

            public function checkPositionGallery(int $position): ?int {
                
                $req = "SELECT * FROM ".$this->opts['table_gallery']." WHERE position=:position";			 
                $res = $this->pdo->prepare($req);
                $res->execute(array(':position'=>$position));
                $tab = $res->fetchAll();
                $nb=count($tab);
                
                return $nb?:0;
            }
	

            public function maxPositionGallery(): ?int {
                
                $req = "SELECT Max(position) AS pos FROM ".$this->opts['table_gallery'];
                $res = $this->pdo->prepare($req);
                $res->execute();		
                $r = $res->fetch(PDO::FETCH_OBJ);
                
                $count=$r->pos;
                    
                return $count?:0;
                    
            }
	
            public function recalculPositionGallery(): void {

                $req = "SELECT * FROM ".$this->opts['table_gallery']." ORDER BY position";				
                $res = $this->pdo->prepare($req);
                $res->execute();
                $tab = $res->fetchAll();
                $nb=count($tab);
                
                for($i=0;$i<$nb;$i++){
                    $newposition=$i+1;
                    $reqmod = "UPDATE ".$this->opts['table_gallery']." SET position=:position WHERE id=:iditem";
                    $resmod = $this->pdo->prepare($reqmod);
                    $resmod->bindValue(':position', $newposition, PDO::PARAM_INT);
                    $resmod->bindValue(':iditem', $tab[$i]['id'], PDO::PARAM_INT);
                    $resmod->execute();
                }
            }
	
        //---------------------------------------------------------  
        // FUNCTIONS INFO GALLERY
        //--------------------------------------------------------- 
        
            public function galleryVideoList(int $idCategory): ?array {

                $req = "SELECT * FROM ".$this->opts['table_gallery']." WHERE idCategory=:idCategory AND status=1 ORDER BY position ASC";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
                $res->execute();
                $tab = $res->fetchAll();

                return $tab?:[];

            }
    

            public function infoGalleryLang(int $idGallery, int $idLang): ?object {
                
                $req = "SELECT * FROM ".$this->opts['table_gallery_lang']." WHERE idGallery=:idGallery AND idLang=:idLang";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idGallery', $idGallery, PDO::PARAM_INT);
                $res->bindValue(':idLang', $idLang, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);
                
                return $r?:null;
            }


            public function countVideoGallery(int $idGallery): ?int {
                
                $req = "SELECT * FROM ".$this->opts['table_item']."item WHERE idGallery=:idGallery";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idGallery', $idGallery, PDO::PARAM_INT);
                $res->execute();
                $tab = $res->fetchAll();
                $nb=count($tab);
                
                return $nb?:0;
            }


        //---------------------------------------------------------  
        // FUNCTIONS DOCUMENT UPLOAD
        //--------------------------------------------------------- 

            public function insertDocument(int $id, int $idParent, string $filename): void{}
            public function deleteDocument(int $id, int $idSite): void {}


        //---------------------------------------------------------  
        // FUNCTIONS IMAGE/FILE
        //--------------------------------------------------------- 

            public function getFile(int $id, string $field): string {
                $infoFile=$this->infoVideo($id);
                $currentFile=$infoFile->$field;
                return $currentFile?:'';
            }

            public function updateFile(int $id, string $field, string $target, string $filename): void {
                $req= "UPDATE ".$this->opts['table_item']." SET ".$field."=:filename WHERE ".$target."=:id";
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
                return '/tmp/videos/'.$idCategory.'/'.$folder.'/files/';
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

                $req = "DELETE  FROM ".$this->opts['table_img']." WHERE id=:id";
				$res = $this->pdo->prepare($req);
				$res->bindValue(':id', $id, PDO::PARAM_STR);
				$res->execute();
				$res->closeCursor();
				$res = NULL;

            }

            public function getFolderPath(int $idCategory, string $folder): string {
                return '/img/video/';
            }  

            public function getImagePath(int $idCategory, string $folder): string {
                return '/img/videos/images/';
            }

            public function getRecPath(int $idCategory, string $folder): string {
                return '/img/videos/rec/';
            }

            public function getSquarePath(int $idCategory, string $folder): string {
                return '/img/videos/square/';
            }

            public function getMobilePath(int $idCategory, string $folder): string {
                return '';
            }

            public function getPdfPath(int $idCategory, string $folder): string {
                return '/img/videos/pdf/';
            }

            public function imgTreatment(string $fileName, int $idCategory, string $field, string $folder): void{

                $img= new ImgManager($this->pdo);

                $imagePath=$_SERVER['DOCUMENT_ROOT'].$this->getImagePath($idCategory, $folder);
                $squarePath=$_SERVER['DOCUMENT_ROOT'].$this->getSquarePath($idCategory, $folder);
                $recPath=$_SERVER['DOCUMENT_ROOT'].$this->getRecPath($idCategory, $folder);

                $img->imgProfile($imagePath.$fileName,$squarePath,$fileName,300, 300, 300, 300, "#151515", 100);
                $img->imgRectangle($imagePath.$fileName,$recPath,$fileName,1024, 576, 1024, 576, "#151515", 100);
                   
            }
           

    }