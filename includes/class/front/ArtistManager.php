<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */

    //---------------------------------------------------------  
	// CLASS FRONT ARTIST MANAGER
	//--------------------------------------------------------- 

    namespace Front;
    use PDO;
    use InvalidArgumentException;

    class ArtistManager {
        protected PDO $pdo;
        protected array $opts = [
            'table_user' => 'artist_user',
            'table_user_lang' => 'artist_user_lang',
            'table_gallery_img' => 'artist_gallery_img',
            'table_gallery_img_lang' => 'artist_gallery_img_lang',
            'table_video' => 'artist_video',
            'table_video_lang' => 'artist_video_lang'
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

                $this->opts['table_user'] = $this->validateTableName($this->opts['table_user']);
                $this->opts['table_user_lang'] = $this->validateTableName($this->opts['table_user_lang']);
                $this->opts['table_gallery_img'] = $this->validateTableName($this->opts['table_gallery_img']);
                $this->opts['table_gallery_img_lang'] = $this->validateTableName($this->opts['table_gallery_img_lang']);
                $this->opts['table_video'] = $this->validateTableName($this->opts['table_video']);
                $this->opts['table_video_lang'] = $this->validateTableName($this->opts['table_video_lang']);

                $this->translations = $translations;

            }

        //---------------------------------------------------------  
        // Info Artist
        //--------------------------------------------------------- 

            public function getArtistInfo(int $id, int $idLang=1): ?object{
                
                $req = "SELECT u.*, l.* FROM ".$this->opts['table_user']." u
                        INNER JOIN ".$this->opts['table_user_lang']." l
                        ON u.id=l.idArtist
                        AND l.idLang=:idLang
                        WHERE u.id=:id";
                $res = $this->pdo->prepare($req);
                $res->bindValue(':id', $id, PDO::PARAM_INT);
                $res->bindValue(':idLang', $idLang, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);

                return $r?:null;	
            }
            
            
        //---------------------------------------------------------  
        // Artist Lang
        //---------------------------------------------------------
            
            public function artistLang(int $idArtist, int $idLang): ?object{
                
                $req = "SELECT * FROM ".$this->opts['table_user_lang']." WHERE idArtist=:idArtist AND idLang=:idLang";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idArtist', $idArtist, PDO::PARAM_INT);
                $res->bindValue(':idLang', $idLang, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);
                            
                return $r?:null;
            }
            

        //---------------------------------------------------------  
        // Artist Lang photo
        //---------------------------------------------------------
            
            public function galleryArtistImgLang(int $idImg, int $idLang): ?object{
                
                $req = "SELECT * FROM ".$this->opts['table_gallery_img_lang']." WHERE idImg=:idImg AND idLang=:idLang";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idImg', $idImg, PDO::PARAM_INT);
                $res->bindValue(':idLang', $idLang, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);
                
                return $r?:null;
            }
            

        //---------------------------------------------------------  
        // Artiste Photo
        //---------------------------------------------------------
            
            public function getArtistImg(int $idArtist, string $artistList="", string $limit="", string $random="", string $group=""): ?array{
                
                if(!empty($idartist)){
                    if(empty($limit)){$limitSet=1;}else{$limitSet=$limit;}
                    $req = "SELECT * FROM ".$this->opts['table_gallery_img']." WHERE idArtist=:idArtist ORDER BY position LIMIT $limitSet";	
                    $res = $this->pdo->prepare($req);
                    $res->bindValue(':idArtist', $idArtist, PDO::PARAM_STR);
                    $res->execute();
                    if($limitSet==1){
                        $r = $res->fetch(PDO::FETCH_OBJ);
                        $res->closeCursor();
                        $res = NULL;
                        return $r->url;
                    }else{
                        $tab= $res->fetchAll();
                        return $tab?:[];
                    }
                }else{
                    if($group==1){$groupStatus="GROUP BY idArtist";}else{$groupStatus="";}
                    if($random==1){$orderStatus="ORDER BY RAND()";}else{$orderStatus="ORDER BY position";}
                    $req = "SELECT * FROM ".$this->opts['table_gallery_img']." WHERE idartist IN ($artistList) $groupStatus $orderStatus LIMIT $limit";	
                    $res = $this->pdo->prepare($req);
                    $res->execute();
                    $tab = $res->fetchAll();
                    return $tab?:[];
                }
            }

            
        //---------------------------------------------------------  
        // Artist Video
        //---------------------------------------------------------
            
            public function artistVideo(int $idArtist): ?object{

                $reqv = "SELECT v.*, l.*
                    FROM ".$this->opts['table_video']." v
                    INNER JOIN ".$this->opts['table_video_lang']." l
                    ON v.id=l.idVideo
                    AND l.idLang=1
                    WHERE idArtist=:idArtist
                    LIMIT 1";				
                $resv = $this->pdo->prepare($reqv);
                $resv->bindValue(':idArtist', $idArtist, PDO::PARAM_INT);
                $resv->execute();
                $r = $resv->fetch(PDO::FETCH_OBJ);
                            
                return $r?:null;
            }

    }