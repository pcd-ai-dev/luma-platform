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
    

    class PageManager implements FileOwnerInterface {
        protected PDO $pdo;
        protected array $opts = [
            'table_category' => 'root_category',
            'table_category_lang' => 'root_category_lang',
            'table_category_img' => 'root_category_img',
            'table_pages' => 'root_pages',
            'table_pages_img' => 'root_pages_img',
            'table_pages_img_lang' => 'root_pages_img_lang',
            'table_revision' => 'root_revision',
            'table_footer' => 'root_footer',
            'table_header' => 'root_header'
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

                $this->opts['table_category'] = $this->validateTableName($this->opts['table_category']);
                $this->opts['table_category_lang'] = $this->validateTableName($this->opts['table_category_lang']);
                $this->opts['table_pages'] = $this->validateTableName($this->opts['table_pages']);
                $this->opts['table_pages_img'] = $this->validateTableName($this->opts['table_pages_img']);
                $this->opts['table_pages_img_lang'] = $this->validateTableName($this->opts['table_pages_img_lang']);
                $this->opts['table_revision'] = $this->validateTableName($this->opts['table_revision']);
                $this->opts['table_footer'] = $this->validateTableName($this->opts['table_footer']);
                $this->opts['table_header'] = $this->validateTableName($this->opts['table_header']);

                $this->translations = $translations;
            }

        //---------------------------------------------------------  
        //  INFO CATEGORY
        //---------------------------------------------------------

            public function infoCat(int $idCategory, int $idLang): ? object{
                
                $req = "SELECT c.*, l.idCategory, l.name, l.idLang
                        FROM ".$this->opts['table_category']." c
                        INNER JOIN ".$this->opts['table_category_lang']." l
                        ON c.id=l.idCategory
                        AND l.idLang=:idLang
                        WHERE c.id=:idCategory";
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
                $res->bindValue(':idLang', $idLang, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);
                
                return $r?:null;
            }


        //---------------------------------------------------------  
        // CATEGORY CHIDINFO
        //--------------------------------------------------------- 

            public function infoChildCat(int $idCategory): ?array{

                $req = "SELECT c.id as ckid, c.parent, c.status, c.type, c.position, l.id, l.name, l.idCategory, l.idLang
                    FROM ".$this->opts['table_category']." c
                    INNER JOIN ".$this->opts['table_category_lang']." l
                    ON c.id=l.idCategory
                    AND c.parent=:idCategory
                    AND l.idLang=1 
                    ORDER BY c.position";			 
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
                $res->execute();
                $tab=$res->fetchAll();
                $nb=count($tab);

                return $tab?:[];

            }


        //---------------------------------------------------------  
        // CATEGORY NAME
        //--------------------------------------------------------- 
	
            public function catNameCheck(int $idCheck): ? string{
                
                $req = "SELECT idCategory, name, idLang FROM ".$this->opts['table_category_lang']." WHERE idCategory=:idCheck AND idLang=1";			 
                $res = $this->pdo->prepare($req);
                $res->execute(array(':idCheck'=>$idCheck));
                $r = $res->fetch(PDO::FETCH_OBJ);
                $res->closeCursor();
                $res = NULL;

                return $r->name ?? '';
            }


        //---------------------------------------------------------  
        // GET PHOTO CAT
        //---------------------------------------------------------

            public function getPhotoCat(int $idCategory, int $idLang): ?string{
                
                $req = "SELECT img FROM ".$this->opts['table_category_img']." WHERE idCategory=:idCategory AND idLang=:idLang";			 
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
                $res->bindValue(':idLang', $idLang, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);
                
                return $r?->img ?? '';
            }            


        //---------------------------------------------------------  
        // HIERARCHY TREE
        //--------------------------------------------------------- 

		
            public function getCatTree(int $idSite): ?array{
            
                $req = "SELECT c.idSite, c.id as idCheck, c.parent, l.idCategory, l.name, l.id
                    FROM ".$this->opts['table_category']." c
                    INNER JOIN ".$this->opts['table_category_lang']." l
                    ON c.id=l.idCategory AND l.idLang=1
                    WHERE c.idSite IN (0, $idSite)
                    ORDER BY c.parent";		 
                $res = $this->pdo->prepare($req);
                $res->execute();
                $tab=$res->fetchAll();
                $nb=count($tab);
                
                $catTree=array();
                
                for($i=0;$i<$nb;$i++){
                    $cat=$tab[$i]['parent']; 
                    $catTree[$cat][]=$tab[$i]['name'];
                }

                return ['catTree' => $catTree,'tab' => $tab];

            }

	
        //---------------------------------------------------------  
        // POSITION CATEGORY
        //---------------------------------------------------------
   
            public function checkPosition(int $position, int $parent): ?int{

                $req = "SELECT * FROM ".$this->opts['table_category']." WHERE position=:position and parent=:parent";			 
                $res = $this->pdo->prepare($req);
                $res->bindValue(':position', $position, PDO::PARAM_INT);
                $res->bindValue(':parent', $parent, PDO::PARAM_INT);
                $res->execute();
                $nb=$res->rowCount();
                $res->closeCursor();
                $res = NULL;

                return $nb?:0;
            }

            public function maxPosition(int $parent): ?int{

                $req = "SELECT Max(position) as pos FROM ".$this->opts['table_category']." WHERE parent=:parent";
                $res = $this->pdo->prepare($req);
                $res->execute(array(':parent'=>$parent));		
                $r = $res->fetch(PDO::FETCH_OBJ);
                $res->closeCursor();
                $res = NULL;

                $position=$r->pos;

                return $position?:0;
            }

            public function updatePosition(int $parent, int $newposition, int $i): void{

                $req = "UPDATE ".$this->opts['table_category']." SET position=:position WHERE parent=:parent AND position=:positionstatus";
                $res = $this->pdo->prepare($req);
                $res->bindValue(':position', $newposition, PDO::PARAM_INT);
                $res->bindValue(':parent', $parent, PDO::PARAM_INT);
                $res->bindValue(':positionstatus', $i, PDO::PARAM_INT);
                $res->execute();
            }

            public function recalculPosition(): void {

                $reqm = "SELECT * FROM ".$this->opts['table_category']." ORDER BY parent";				
                $resm = $this->pdo->prepare($reqm);
                $resm->execute();
                $tabm = $resm->fetchAll();
                $nbm=count($tabm);

                for($m=0;$m<$nbm;$m++){
                    $req = "SELECT * FROM ".$this->opts['table_category']." WHERE parent=:parent ORDER BY position";				
                    $res = $this->pdo->prepare($req);
                    $res->bindValue(':parent', $tabm[$m]['id'], PDO::PARAM_INT);
                    $res->execute();
                    $tab = $res->fetchAll();
                    $nb= count($tab);

                    for($i=0;$i<$nb;$i++){
                        $newposition=$i+1;
                        $reqmod = "UPDATE ".$this->opts['table_category']." SET position=:position WHERE id=:iditem";
                        $resmod = $this->pdo->prepare($reqmod);
                        $resmod->bindValue(':position', $newposition, PDO::PARAM_INT);
                        $resmod->bindValue(':iditem', $tab[$i]['id'], PDO::PARAM_INT);
                        $resmod->execute();
                    }
                }
            }

		
        //---------------------------------------------------------  
        // INFO PAGE
        //---------------------------------------------------------

            public function infoPage(int $idCategory, int $idLang): ?object{
                
                $req = "SELECT * FROM ".$this->opts['table_pages']." WHERE idCategory=:idCategory AND idLang=:idLang";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
                $res->bindValue(':idLang', $idLang, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);
                
                return $r?:null;
            }


        //---------------------------------------------------------  
        // PAGE IMG LANG
        //---------------------------------------------------------
		
            public function pagesImgLang(int $idImg, int $idLang): ?object{
                
                $req = "SELECT * FROM ".$this->opts['table_pages_img_lang']." WHERE idImg=:idImg AND idLang=:idLang";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idImg', $idImg, PDO::PARAM_INT);
                $res->bindValue(':idLang', $idLang, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);
                
                return $r?:null;
            }


        //---------------------------------------------------------  
        // INFO REVISION
        //---------------------------------------------------------

            public function infoRevision(int $idRevision, int $idLang): ?object{
                
                $req = "SELECT * FROM ".$this->opts['table_revision']." WHERE id=:idRevision AND idLang=:idLang";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idRevision', $idRevision, PDO::PARAM_INT);
                $res->bindValue(':idLang', $idLang, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);
                
                return $r?:null;
            }


        //---------------------------------------------------------  
        // INFO REVISION CATEGOTY
        //---------------------------------------------------------

		    public function infoRevisionByCategory(int $idCategory, int $idLang): ?array{

				$req = "SELECT * FROM ".$this->opts['table_revision']." WHERE idCategory=:idCategory AND idLang=:idLang ORDER BY date DESC LIMIT 10";			 
				$res = $this->pdo->prepare($req);
				$res->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
				$res->bindValue(':idLang', (int)$idLang, PDO::PARAM_INT);
				$res->execute();
				$tab=$res->fetchAll();

				return $tab?:null;
		    }

        //---------------------------------------------------------  
        // GET LAST UPDATE
        //---------------------------------------------------------
            
            public function getLastUpdate(int $idCategory): ?string{

                $req = "SELECT date as dateUpdate FROM ".$this->opts['table_revision']." WHERE idCategory=:idCategory ORDER BY date DESC LIMIT 1";			 
                $res = $this->pdo->prepare($req);
                $res->execute(array(':idCategory'=>$idCategory));
                $r = $res->fetch(PDO::FETCH_OBJ);

                return $r->dateUpdate?:'';
            }


        //---------------------------------------------------------  
        // INFO FOOTER
        //---------------------------------------------------------

            public function infoFooter(int $idSite, int $idLang): ?object{
                
                $req = "SELECT * FROM ".$this->opts['table_footer']." WHERE idSite=:idSite AND idLang=:idLang";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idLang', $idLang, PDO::PARAM_INT);
                $res->bindValue(':idSite', $idSite, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);
                
                return $r?:null;
            }


        //---------------------------------------------------------  
        // INFO HEADER
        //---------------------------------------------------------

            public function infoHeader(int $idSite, int $idLang): ?object{
                
                $req = "SELECT * FROM ".$this->opts['table_header']." WHERE idSite=:idSite AND idLang=:idLang";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idLang', $idLang, PDO::PARAM_INT);
                $res->bindValue(':idSite', $idSite, PDO::PARAM_INT);
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
                $infoFile=$this->infoCat($id,1);
                $currentFile=$infoFile->$field;
                return $currentFile?:'';
            }

            public function updateFile(int $id, string $field, string $target, string $filename): void {
                $req = "UPDATE ".$this->opts['table_category']." SET ".$field."=:filename WHERE ".$target."=:id";
				$res = $this->pdo->prepare($req);
				$res->bindValue(':filename', $filename, PDO::PARAM_STR);
				$res->bindValue(':id', $id, PDO::PARAM_STR);
				$res->execute();
				$res->closeCursor();
				$res = NULL;
            }

            public function resetFile(int $id, string $field, string $target): void {
                $req = "UPDATE ".$this->opts['table_category']." SET ".$field."= NULL WHERE ".$target."=:id";
				$res = $this->pdo->prepare($req);
				$res->bindValue(':id', $id, PDO::PARAM_STR);
				$res->execute();
				$res->closeCursor();
				$res = NULL;
            }

            public function getFilePath(int $idCategory, string $folder): string {
                return '/tmp/pages/'.$idCategory.'/'.$folder.'/files/';
            }


        //---------------------------------------------------------  
        // FUNCTIONS IMAGE TREATMENT
        //--------------------------------------------------------- 

            public function getGalleryImage(int $id): ?string {
                
                $req = "SELECT * FROM ".$this->opts['table_pages_img']." WHERE id=:id";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':id', $id, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);
                
                return $r->img?:'';
            }

            public function insertImage(int $id, int $idCategory, string $filename): void {

                $req = "INSERT INTO ".$this->opts['table_pages_img']." (idCategory, img) VALUES (:idCategory, :filename)";
				$res = $this->pdo->prepare($req);
				$res->bindValue(':filename', $filename, PDO::PARAM_STR);
				$res->bindValue(':idCategory', $id, PDO::PARAM_INT);
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

				$reqs = [
					"DELETE FROM ".$this->opts['table_pages_img']." WHERE id = :id",
					"DELETE FROM ".$this->opts['table_pages_img_lang']." WHERE idImg = :id",
				];

				foreach ($reqs as $req) {
					$res = $this->pdo->prepare($req);
					$res->execute([':id' => $id]);
                    $res->closeCursor();
				    $res = NULL;
				}
            }


            public function getFolderPath(int $idCategory, string $folder): string {
                return '/img/pages/'.$idCategory.'/'.$folder.'/';
            }     

            public function getImagePath(int $idCategory, string $folder): string {
                return '/img/pages/'.$idCategory.'/'.$folder.'/images/';
            }

            public function getRecPath(int $idCategory, string $folder): string {
                return '/img/pages/'.$idCategory.'/'.$folder.'/rec/';
            }

            public function getSquarePath(int $idCategory, string $folder): string {
                return '/img/pages/'.$idCategory.'/'.$folder.'/square/';
            }

            public function getMobilePath(int $idCategory, string $folder): string {
                return '';
            }

            public function imgTreatment(string $fileName, int $idCategory, string $field, string $folder): void{

                $img= new ImgManager($this->pdo);

                $imagePath=$_SERVER['DOCUMENT_ROOT'].$this->getImagePath($idCategory, $folder);
                $squarePath=$_SERVER['DOCUMENT_ROOT'].$this->getSquarePath($idCategory, $folder);
                $recPath=$_SERVER['DOCUMENT_ROOT'].$this->getRecPath($idCategory, $folder);


                $img->imgResizeWidth($imagePath,$fileName,1200);

                 if($field=='rec'){
                    $img->imgProfile($imagePath.$fileName, $recPath, $fileName, 900, 300, 900, 300, "#FFF", 80);
                } else if($field=='square'){
                    $img->imgProfile($imagePath.$fileName, $squarePath, $fileName, 1600, 1600, 1600, 1600, "#FFF", 80);
                }else{
                    $img->imgProfile($imagePath.$fileName,$squarePath,$fileName,600, 600, 600, 600, "#151515", 80);
                    $img->imgRectangle($imagePath.$fileName,$recPath,$fileName,1024, 576, 1024, 576, "#151515", 80);
                }

                
                   
            }  


    }