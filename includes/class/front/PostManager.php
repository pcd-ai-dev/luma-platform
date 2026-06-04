<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */

    //---------------------------------------------------------  
	// CLASS FRONT POST MANAGER
	//--------------------------------------------------------- 

    namespace Front;
    use PDO;
    use DateTime;
    use DateTimeImmutable;
    use IntlDateFormatter;
    use InvalidArgumentException;
    use Tools\FileOwnerInterface;
    use Tools\ImgManager;

    class PostManager implements FileOwnerInterface {
        protected PDO $pdo;
        protected array $opts = [
            'table_item' => 'post_item',
            'table_lang' => 'post_lang',
            'table_img' => 'post_img',
            'table_img_lang' => 'post_img_lang'
        ];

        private StringManager $StringManager;
        private array $translations;
        private string $siteName;
        private string $siteHost;
        private string $imgDefaultSquare;

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

            public function __construct(PDO $pdo, string $siteName='', string $siteHost='', string $imgDefaultSquare='', array $translations = [], array $options = []){

                $this->pdo = $pdo;
                $this->opts = array_merge($this->opts, $options);

                $this->opts['table_item'] = $this->validateTableName($this->opts['table_item']);
                $this->opts['table_lang'] = $this->validateTableName($this->opts['table_lang']);
                $this->opts['table_img'] = $this->validateTableName($this->opts['table_img']);
                $this->opts['table_img_lang'] = $this->validateTableName($this->opts['table_img_lang']);

                $this->StringManager= new StringManager($pdo,['table_tags' => 'param_tags']);

                $this->translations = $translations;

                $this->siteName = $siteName;
                $this->siteHost = $siteHost;
                $this->imgDefaultSquare = $imgDefaultSquare;
            }

        //---------------------------------------------------------
        // FUNCTION POSITION
        //---------------------------------------------------------

            public function checkPositionBlog(int $position): ?int{

                $req = "SELECT * FROM ".$this->opts['table_item']." WHERE position=:position";			 
                $res = $this->pdo->prepare($req);
                $res->bindValue(':position', $position, PDO::PARAM_INT);
                $res->execute();
                $tab = $res->fetchAll();
                $nb=count($tab);

                return $nb?:0;
            }


            public function maxPositionBlog(): ?int{

                $req = "SELECT Max(position) AS pos FROM ".$this->opts['table_item'];	 
                $res = $this->pdo->prepare($req);
                $res->execute();		
                $r = $res->fetch(PDO::FETCH_OBJ);

                $count=$r->pos;
                    
                return $count?:0;
            }

            public function recalculPositionBlog(): void {

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
                    
                }
            }

        //---------------------------------------------------------  
        // FUNCTIONS INFO POST
        //--------------------------------------------------------- 
        
            public function infoPost(int $idPost): ?object {

                $req = "SELECT * FROM ".$this->opts['table_item']." WHERE id=:idPost";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idPost', $idPost, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);
                
                return $r?:null;
            }

            
            public function postLang(int $idPost, int $idLang): ?object {
                
                $req = "SELECT * from ".$this->opts['table_lang']." WHERE idPost=:idPost AND idLang=:idLang";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idPost', $idPost, PDO::PARAM_INT);
                $res->bindValue(':idLang', $idLang, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);
                
                return $r?:null;
            }


        //-------------------------------------------------------
        // showPostCommon
        //-------------------------------------------------------	
		
            public function postListDisplay(int $idPost, int $idLang, int $idCategory, string $varlink): ?string{
                
                $req = "SELECT p.id, p.idCategory as idCategoryRoot, p.date, p.img, p.type, p.source, p.video, l.*
                    FROM ".$this->opts['table_item']." p
                    INNER JOIN ".$this->opts['table_lang']." l
                    ON p.id=l.idPost
                    WHERE p.id=:idPost
                    AND l.idLang=:idLang
                    ORDER BY p.date";			
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idPost', $idPost, PDO::PARAM_INT);
                $res->bindValue(':idLang', $idLang, PDO::PARAM_INT);
                $res->execute();
                $r=$res->fetch(PDO::FETCH_OBJ);
                
                $linkPost=((!empty($varlink))? '/'. $varlink : '').'/news-'.$idCategory.'-'.$idPost.'-'.$this->StringManager->cleanUrl($r->title).'.html';

                if((file_exists($_SERVER['DOCUMENT_ROOT'].'/img/post/'.$r->idCategoryRoot.'/img/square/'.$r->img))&&(!empty($r->img))){
                    $imgDisplay='/img/post/'.$r->idCategoryRoot.'/img/square/'.$r->img;
                }else{
                    $imgDisplay=$this->imgDefaultSquare;
                }

                if (strlen((isset($r->video))?$r->video : '')>20){
                    if(empty($r->video)){
                        $videoLinkcolorBox = "";
                    }else{
                        $videoLinkcolorBox = $this->siteHost.$r->video;
                    }
                }else{
                    if ( is_numeric( $r->video ) ) {
                        $videoLinkcolorBox = "https://player.vimeo.com/video/".$r->video;
        
                    } else {
                        $videoLinkcolorBox = "https://www.youtube.com/embed/".$r->video;
                    }
                }
                
                $shareLink='<div id="fb-root"></div><script async defer crossorigin="anonymous" src="https://connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v10.0" nonce="UxH0LHPf"></script>';
                $shareLink.='<div>&nbsp;</div>';
                $shareLink.='<div class="fb-share-button" data-href="'.$linkPost.'" data-layout="button" data-size="large"><a target="_blank" href="'.$linkPost.'" class="fb-xfbml-parse-ignore">Partager</a></div>';
                $shareLink.='<div>&nbsp;</div>';
                $shareLink.='<div><a href="https://twitter.com/intent/tweet?button_hashtag='.$this->siteName.'" class="twitter-hashtag-button" data-show-count="false" data-size="large">Tweet</a><script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script></div>';
                
                $dateStart = new DateTimeImmutable($r->date);
                $formatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
                $formatter ->setPattern("dd MMMM YYYY");

                $dateDisplay=$formatter->format($dateStart);

                $affpost="";
                
                $affpost.= '<div class="postRow">';
                
                $affpost.= '<div class="postContent">';
                            
                $affpost.= '<div><h2>'.$r->title.'</h2></div>';
                
                $affpost.= '<div class="datePost" style="color:'.(isset($is->colorNews)? $is->colorNews: '#FFFFFF;').'">'.$dateDisplay.'</div>';
                
                $affpost.= '<div>&nbsp;</div>';
                
                $affpost.= '<div class="clampFourTxt">'.strip_tags((isset($r->shortText)? $r->shortText : ''), '<a><br/><br><iframe><strong>').'</div>';
                
                
                $affpost.= '<div>&nbsp;</div><div id="LinkPost"><a href="'.$linkPost.'" title="'.$r->title.'">'.$this->translations['READTXT'].'</a></div>';
                
                //$affpost.=$shareLink;

                $affpost.= '</div>';
                
                $affpost.= '<div class="postImg" style="background-image:url('.$imgDisplay.');">';
                
                if((empty($r->video)||(strlen($r->video)>20))){}else{
                    $affpost.= '<a href="'.$videoLinkcolorBox.'" data-fancybox="" data-width="80%" data-height="80%" >';
                    $affpost.= '<div class="overlay-content-header">';
                    $affpost.= '<div class="play-button">&nbsp;</div>';
                    $affpost.= '</div>';
                    $affpost.= '</a>';
                }
                
                $affpost.= '</div>';

                $affpost.= '</div>';
                
                
                return $affpost;
            }


        //-------------------------------------------------------
        // showPostCommon
        //-------------------------------------------------------
		
            public function postColumnDisplay(int $idPost, int $idLang, string $varlink): ?string{
                
                $req = "SELECT p.id, p.idCategory as idCategoryRoot, p.date, p.img, p.type, p.source, p.video, l.*
                    FROM ".$this->opts['table_item']." p
                    INNER JOIN ".$this->opts['table_lang']." l
                    ON p.id=l.idPost
                    WHERE p.id=:idPost
                    AND l.idLang=:idLang
                    ORDER BY p.date";			
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idPost', $idPost, PDO::PARAM_INT);
                $res->bindValue(':idLang', $idLang, PDO::PARAM_INT);
                $res->execute();
                $r=$res->fetch(PDO::FETCH_OBJ);
                
                $linkPost=((!empty($varlink))? '/'. $varlink : '').'/news-'.$idPost.'-'.$this->StringManager->cleanUrl($r->title).'.html';

                if((file_exists($_SERVER['DOCUMENT_ROOT'].'/img/post/'.$r->idCategoryRoot.'/img/square/'.$r->img))&&(!empty($r->img))){
                    $imgDisplay='/img/post/'.$r->idCategoryRoot.'/img/square/'.$r->img;
                }else{
                    $imgDisplay=$this->imgDefaultSquare;
                }

                if (strlen((isset($r->video))?$r->video : '')>20){
                    if(empty($r->video)){
                         $videoLinkcolorBox = "";
                    }else{
                        $videoLinkcolorBox = $this->siteHost.$r->video;
                    }
                }else{
                    if ( is_numeric( $r->video ) ) {
                        $videoLinkcolorBox = "https://player.vimeo.com/video/".$r->video;
        
                    } else {
                        $videoLinkcolorBox = "https://www.youtube.com/watch?v=".$r->video;
                    }
                }

                $dateStart = new DateTimeImmutable($r->date);
                $formatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
                $formatter ->setPattern("dd MMMM YYYY");

                $dateDisplay=$formatter->format($dateStart);

                $affpost="";
                $affpost.= '<div class="postColumn">';
                $affpost.= '<div class="postImg" style="background-image:url('.$imgDisplay.');">';
                if((empty($r->video)||(strlen($r->video)>20))){}else{
                    $affpost.= '<div class="overlay-content-header">';
                    $affpost.= '<a href="'.$videoLinkcolorBox.'" data-fancybox="" data-width="80%" data-height="80%" >';
                    $affpost.= '<div class="play-button">&nbsp;</div>';
                    $affpost.= '</a>';
                    $affpost.= '</div>';
                }
                $affpost.= '</div>';
                $affpost.= '<div class="postContent">';
                $affpost.= '<div><h2>'.$r->title.'</h2></div>';
                $affpost.= '<div class="datePost">'.$dateDisplay.'</div>';
                $affpost.= '<div>&nbsp;</div>';
                $affpost.= '<div class="clampFourTxt">'.strip_tags((isset($r->shortText)? $r->shortText : ''), '<a><br/><br><iframe><strong>').'</div>';	
                $affpost.= '<div>&nbsp;</div><div id="LinkPost"><a href="'.$linkPost.'" title="'.$r->title.'">'.$this->translations['READTXT'].'/a></div>';
                $affpost.= '</div>';
                $affpost.= '</div>';
                
                return $affpost;
            }


        //-------------------------------------------------------
        // showJobCommon
        //-------------------------------------------------------	
		
            public function jobListDisplay(int $idPost, string $email, int $idLang, string $varlink): string{
                
                $req = "SELECT p.id, p.idCategory, p.date, p.img, p.type, p.source, p.video, l.*
                    FROM ".$this->opts['table_item']." p
                    INNER JOIN ".$this->opts['table_lang']." l
                    ON p.id=l.idPost
                    WHERE p.id=:idPost
                    AND l.idLang=:idLang
                    ORDER BY p.date";			
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idLang', $idLang, PDO::PARAM_INT);
                $res->bindValue(':idPost', $idPost, PDO::PARAM_INT);
                $res->execute();
                $r=$res->fetch(PDO::FETCH_OBJ);
                $res->closeCursor();
                $res = NULL;
                    
                $photo=$r->img;
                
                $linkPost=((!empty($varlink))? '/'. $varlink : '').'/job-'.$idPost.'-'.$this->StringManager->cleanUrl($r->title).'.html';
                
                $dateStart = new DateTimeImmutable($r->date);
                $formatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
                $formatter ->setPattern("dd MMMM YYYY");

                $dateDisplay=$formatter->format($dateStart);

                $affpost="";
                $affpost.= '<div class="jobRow">';
                $affpost.= '<div class="jobContent">';
                $affpost.= '<div class="dateJob">'.$dateDisplay.'</div>';
                $affpost.= '<div><h3>'.$r->title.'</h3></div>';
                $affpost.= '<div>'.$r->text.'</div>';
                if(empty($email)){$emailJob="cpcv@cpcvidf.fr";}else{$emailJob=$email;}
                $affpost.= '<div class="linkJob"><a href="mailto:'.$emailJob.'?subject=CPCV Ile-de-France : Candidature poste '.$r->title.'&amp;body=
                    Insérez une lettre de motivation et votre curriculum vitae. %0D%0A %0D%0A
                    Nous vous remercions de l\'intérêt que vous nous portez. %0D%0A %0D%0ACPCV ïle-de-France">Postuler</a></div>';
                $affpost.= '</div>';
                $affpost.= '</div>';
                
                return $affpost?:'';
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
                $infoFile=$this->infoPost($id);
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
                return '/tmp/post/'.$idCategory.'/'.$folder.'/';
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

                $req = "INSERT INTO ".$this->opts['table_img']." (idPost, img, idCategory) VALUES (:id, :filename, :idCategory)";
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
                return '/img/post/'.$idCategory.'/'.$folder.'/';
            }     

            public function getImagePath(int $idCategory, string $folder): string {
                return '/img/post/'.$idCategory.'/'.$folder.'/images/';
            }

            public function getRecPath(int $idCategory, string $folder): string {
                return '/img/post/'.$idCategory.'/'.$folder.'/rec/';
            }

            public function getSquarePath(int $idCategory, string $folder): string {
                return '/img/post/'.$idCategory.'/'.$folder.'/square/';
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
                    $img->imgProfile($imagePath.$fileName,$squarePath,$fileName,600, 600, 600, 600, "#151515", 100);
                    $img->imgRectangle($imagePath.$fileName,$recPath,$fileName,1024, 576, 1024, 576, "#151515", 100);
                }
                   
            }

    }