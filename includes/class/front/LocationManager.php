<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */

    //---------------------------------------------------------  
	// CLASS FRONT LOCATION MANAGER
	//--------------------------------------------------------- 

    namespace Front;
    use PDO;
    use InvalidArgumentException;
    use Tools\FileOwnerInterface;
    use Tools\ImgManager;

    class LocationManager implements FileOwnerInterface {
        protected PDO $pdo;
        protected array $opts = [
            'table_item' => 'loc_item',
            'table_lang' => 'loc_lang',
            'table_img' => 'loc_img',
            'table_img_lang' => 'loc_img_lang',
            'table_ville' => 'param_ville',
            'table_departement' => 'param_departement',
            'table_countries' => 'param_ip2nationCountries',
            'table_nation' => 'param_ip2nation'
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
                $this->opts['table_ville'] = $this->validateTableName($this->opts['table_ville']);
                $this->opts['table_departement'] = $this->validateTableName($this->opts['table_departement']);
                $this->opts['table_countries'] = $this->validateTableName($this->opts['table_countries']);
                $this->opts['table_nation'] = $this->validateTableName($this->opts['table_nation']);
                
                $this->translations = $translations;

            }

		
        //---------------------------------------------------------  
        // POSITION FUNCTION
        //---------------------------------------------------------
        
            public function checkPositionLocalisation(int $position): ?int{

                $req = "SELECT * FROM ".$this->opts['table_item']." WHERE position=:position";			 
                $res = $this->pdo->prepare($req);
                $res->execute(array(':position'=>$position));
                $tab = $res->fetchAll();
                $nb= count($tab);

                return $nb?:0;
            }


            public function maxPositionLocalisation(): ?int{

                $req = "SELECT Max(position) AS pos FROM ".$this->opts['table_item'];
                $res = $this->pdo->prepare($req);
                $res->execute();		
                $r = $res->fetch(PDO::FETCH_OBJ);
                $count=$r->pos;

                return $count?:0;
            }
	
        
            public function recalculPositionLocalisation(): void{

                $req = "SELECT * FROM ".$this->opts['table_item']." ORDER BY position";				
                $res = $this->pdo->prepare($req);
                $res->execute();
                $tab = $res->fetchAll();
                $nb = count($tab);

                for($i=0;$i<$nb;$i++){
                    $newposition=$i+1;
                    $reqmod = "UPDATE ".$this->opts['table_item']." SET position=:position WHERE id=:iditem";
                    $resmod = $this->pdo->prepare($reqmod);
                    $resmod->bindValue(':position', $newposition, PDO::PARAM_INT);
                    $resmod->bindValue(':iditem', $tab[$i]['id'], PDO::PARAM_INT);
                    $resmod->execute();
                    $resmod->closeCursor();
                    $resmod = NULL; 

                }
            }


        //---------------------------------------------------------  
        // INFO LOCALISATION
        //---------------------------------------------------------
            
            function locationInfo(int $idLocation): ?object{
                
                $req = "SELECT * FROM ".$this->opts['table_item']." WHERE id=:idLocation";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idLocation', $idLocation, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);

                
                return $r?:null;
            }

	
        //---------------------------------------------------------  
        // INFO LOCALISATION LANG
        //---------------------------------------------------------

            public function localisationLang(int $idLocation, int $idLang): ?object{
                
                $req = "SELECT * FROM ".$this->opts['table_lang']." WHERE idLocation=:idLocation AND idLang=:idLang";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idLocation', $idLocation, PDO::PARAM_INT);
                $res->bindValue(':idLang', $idLang, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);
                
                return $r?:null;
            }


        //---------------------------------------------------------  
        // POSITION LIST
        //---------------------------------------------------------
    
            public function locationList(int $idCategory, int $id, int $idLang): string{
            
                $req = "SELECT p.*, l.*
                    FROM ".$this->opts['table_item']." p
                    INNER JOIN ".$this->opts['table_lang']." l
                    ON p.id=l.idLocation AND l.idLang=:idLang
                    WHERE p.idCategory=:idCategory
                    AND p.status=1
                    ORDER BY l.idLocation DESC";
                $res = $this->pdo->prepare($req);
                $res->bindParam(':idCategory', $idCategory, PDO::PARAM_INT);
                $res->bindParam(':idLang', $idLang, PDO::PARAM_INT);
                $res->execute();
                $tab = $res->fetchAll();
                $nb= count($tab);
                
                $result="";
                
                for($i=0;$i<$nb;$i++){
                    if($tab[$i]['idLocation']==$id){$result.=$i;}else{}
                }
                
                return $result?:'';
            }


        //--------------------------------------------------------- 
        // LANG PHOTOS
        //---------------------------------------------------------
            
            public function locationImgLang(int $idImg, int $idLang): ?object{
                
                $req = "SELECT * FROM ".$this->opts['table_img_lang']." WHERE idImg=:idImg AND idLang=:idLang";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idImg', $idImg, PDO::PARAM_INT);
                $res->bindValue(':idLang', $idLang, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);
                $res->closeCursor();
                $res = NULL;
                
                return $r;
            }


        //-------------------------------------------------------
        // DISTANCE CALCUL
        //-------------------------------------------------------

            public function distance($lat1, $lon1, $lat2, $lon2): ?string{
                $pi80 = M_PI / 180; 
                $lat1 *= $pi80; 
                $lon1 *= $pi80; 
                $lat2 *= $pi80; 
                $lon2 *= $pi80; 
                $r = 6372.797; // mean radius of Earth in km 
                $dlat = $lat2 - $lat1; 
                $dlon = $lon2 - $lon1; 
                $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2); 
                $c = 2 * atan2(sqrt($a), sqrt(1 - $a)); 
                $km = $r * $c;
                
                if($km<=20){
                    $valueDist=1;
                }else if((21 <= $km) && ($km <= 50)){
                    $valueDist=2;
                }else{
                    $valueDist=3;
                }
                //return $km;
                
                if((empty($lat1))||(empty($lon1))||(empty($lat2))||(empty($lon2))){
                    return false;
                }else{
                    return $valueDist?:'';
                }
            }

        //-------------------------------------------------------
        // LOCALTION POINT DISPLAY
        //-------------------------------------------------------	
		
            public function showLocationCommon(int $idLocation, int $idCategory, int $idLang, string $lat, string $lng): ?string{
                
                $req = "SELECT p.id, p.date, p.img, p.type, p.video, p.address, p.city, p.cp, p.lat, p.lng, l.*
                    FROM ".$this->opts['table_item']." p
                    INNER JOIN ".$this->opts['table_lang']." l
                    ON p.id=l.idLocation
                    AND l.idLang=:idLang
                    WHERE p.id=:idLocation";			
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idLocation', $idLocation, PDO::PARAM_INT);
                $res->bindValue(':idLang', $idLang, PDO::PARAM_INT);
                $res->execute();
                $r=$res->fetch(PDO::FETCH_OBJ);
                                
                $type=$r->type;

                $depCode = substr($r->cp, 0, 2);
            
                if((file_exists($_SERVER['DOCUMENT_ROOT'].'/img/location/'.$idCategory.'/img/square/'.$r->img))&&(!empty($r->img))){
                    $photoDisplay='<img src="/img/location/'.$idCategory.'/img/square/'.$r->img.'" width="100%"  alt="'.$r->title.'" style="border:0px;"/>';
                }else{
                    $photoDisplay='<img src="/img/interface/default/defaultSquare.svg" width="100%" alt="'.$r->title.'" />';
                }

                $affLoc="";
                $affLoc.= '<div id="locItem" style="border:0px solid transparent;width:100%;">';

                if(empty($r->video)){
                    $affLoc.= '<div class="LocationImg">'.$photoDisplay.'</div>';
                }else{
                    $affLoc.='<div style="margin:-1px;"><iframe style="border:0px;" width="100%" height="180" src="https://www.youtube.com/embed/'.$r->video.'" framestyle="border:0px;" allow="autoplay; encrypted-media" allowfullscreen></iframe><div>';
                }

                $affLoc.='<div class="pointContent">';
                $affLoc.='<div class="pointTitle">'.$r->title.'</div>';
                $affLoc.='<div class="pointCity">'.$r->city.' ('.$depCode.')</div>';
                $affLoc.='<div class="pointDescription">'.$r->description.'</div>';
                $affLoc.='<div class="pointLink"><a href="https://maps.google.com/?q='.urlencode($r->address.' '.$r->cp.' '.$r->city).'" target="_blank">Voir l\'itin&eacute;raire</a></div>';
                $affLoc.='</div>';
                
                $affLoc.= '</div>';
                $affLoc.= '</div>';
                
                return $affLoc?:'';
            }
            
            
        //---------------------------------------------------------  
        // IP COUNTRY
        //--------------------------------------------------------- 

            function ipcountry(string $ip): ?object{
                    
                $req = "SELECT n.*, p.* FROM  ".$this->opts['table_nation']." n
                INNER JOIN ".$this->opts['table_countries']." p
                ON n.country=p.code
                WHERE n.ip < INET_ATON(:ip) ORDER BY n.ip DESC  LIMIT 0,1";
                $res = $this->pdo->prepare($req);
                $res->bindValue(':ip', $ip, PDO::PARAM_STR);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);
                    
                return $r?:null;
            }

		
        //---------------------------------------------------------  
        // LANGUAGE COUNTRIES
        //--------------------------------------------------------- 

            public function langCountry(string $country): ?object{
                    
                $req = "SELECT * FROM  ".$this->opts['table_countries']." WHERE code=:country";
                $res = $this->pdo->prepare($req);
                $res->bindValue(':country', $country, PDO::PARAM_STR);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);
                    
                return $r?:null;
            }
		
		
        //---------------------------------------------------------  
        // INFO CITY
        //---------------------------------------------------------
		
            public function getInfoCity(int $idCity): ?object{
                
                $req = "SELECT t.*, d.*
                FROM ".$this->opts['table_ville']." t
                INNER JOIN ".$this->opts['table_departement']." d
                ON t.id_departement=d.id_departement	
                WHERE t.id_ville=:idCity";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idChity', $idCity, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);
                
                return $r?:null;
            }


        //---------------------------------------------------------  
        // DEP INFO
        //---------------------------------------------------------

            public function getDep(string $code): ?object{
                
                $req = "SELECT * FROM ".$this->opts['table_departement']." WHERE code=:code";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':code', $code, PDO::PARAM_STR);
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
                $infoFile=$this->locationInfo($id);
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
                return '';
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

            public function updateImage(int $id, string $field, string $target, string $filename): void {
                $req = "UPDATE ".$this->opts['table_img']." SET ".$field."=:filename WHERE ".$target."=:id";
				$res = $this->pdo->prepare($req);
				$res->bindValue(':filename', $filename, PDO::PARAM_STR);
				$res->bindValue(':id', $id, PDO::PARAM_STR);
				$res->execute();
				$res->closeCursor();
				$res = NULL;
            }

            public function insertImage(int $idLocation, int $idCategory, string $filename): void {

                $req = "INSERT INTO ".$this->opts['table_img']." (idCategory, idLocation, img) VALUES (:idCategory, :idLocation, :filename)";
				$res = $this->pdo->prepare($req);
				$res->bindValue(':filename', $filename, PDO::PARAM_STR);
				$res->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
                $res->bindValue(':idLocation', $idLocation, PDO::PARAM_INT);
				$res->execute();
				$res->closeCursor();
				$res = NULL;

            }

            public function deleteImage(int $id): void {

				$reqs = [
					"DELETE FROM ".$this->opts['table_img']." WHERE id = :id",
					"DELETE FROM ".$this->opts['table_img_lang']." WHERE idImg = :id",
				];

				foreach ($reqs as $req) {
					$res = $this->pdo->prepare($req);
					$res->execute([':id' => $id]);
                    $res->closeCursor();
				    $res = NULL;
				}
            }

            public function getFolderPath(int $idCategory, string $folder): string {
                return '/img/location/'.$idCategory.'/'.$folder.'/';
            }     

            public function getImagePath(int $idCategory, string $folder): string {
                return '/img/location/'.$idCategory.'/'.$folder.'/images/';
            }

            public function getRecPath(int $idCategory, string $folder): string {
                return '/img/location/'.$idCategory.'/'.$folder.'/rec/';
            }

            public function getSquarePath(int $idCategory, string $folder): string {
                return '/img/location/'.$idCategory.'/'.$folder.'/square/';
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