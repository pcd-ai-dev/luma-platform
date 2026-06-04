<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */

    //---------------------------------------------------------  
	// CLASS FRONT PHOTO MANAGER
	//--------------------------------------------------------- 

    namespace Action;
    use PDO;

    class ActionManager {
        protected PDO $pdo;
        private string $table;
        private string $field;
        private string $target;
        private string $type;


        //---------------------------------------------------------
        // CONSTRUCT
        //---------------------------------------------------------

            public function __construct(PDO $pdo, string $table, string $field, string $target, string $type ){
                $this->pdo = $pdo;
                $this->table = $table;
                $this->field = $field;
                $this->target = $target;
                $this->type = $type;
            }


        //---------------------------------------------------------
        // SORTABLE FUNCTION
        //---------------------------------------------------------

            public function sortable(array $list): void {

                $positions = [];

                foreach ($list as $id => $parentId) {

                    $parentId = $parentId === null ? 0 : (int)$parentId;

                    if (!isset($positions[$parentId])) {
                        $positions[$parentId] = 1;
                    }

                    if($this->type=="listRoot"){

                        $req = "UPDATE ".$this->table." SET parent=:parent, position=:position WHERE id=:idCategory AND idSite=:idSite";
                        $res = $this->pdo->prepare($req);
                        $res->bindValue(':parent', $parentId, PDO::PARAM_STR);
                        $res->bindValue(':idCategory', $id, PDO::PARAM_STR);
                        $res->bindValue(':position', $positions[$parentId], PDO::PARAM_STR);
                        $res->bindValue(':idSite', $_SESSION['siteData']['id'], PDO::PARAM_STR);
                        $res->execute();
                        $res->closeCursor();
                        $res = NULL;

                    }else{

                        $req = "UPDATE ".$this->table." SET ".$this->field."=:position WHERE ".$this->target." = :id";
                        $res = $this->pdo->prepare($req);
                        $res->bindValue(':id', (int)$id, PDO::PARAM_INT);
                        $res->bindValue(':position', $positions[$parentId], PDO::PARAM_INT);
                        $res->execute();

                    }

                    $positions[$parentId]++;
                }
            }

        //---------------------------------------------------------
        // CHECK STATUS FUNCTION
        //---------------------------------------------------------

            public function getStatus(int $id): int {

                $req = "SELECT ".$this->field." FROM ".$this->table." WHERE ".$this->target."=:id";
                $res = $this->pdo->prepare($req);
                $res->bindValue(':id', $id, PDO::PARAM_INT);
                $res->execute();

                return (int)$res->fetchColumn() ?: 0;
            }

        //---------------------------------------------------------
        // UPDATE STATUS FUNCTION
        //---------------------------------------------------------

            public function toggleStatus(int $id): int{

                $current = $this->getStatus($id);
                $newStatus = $current === 0 ? 1 : 0;

                $req="UPDATE ".$this->table." SET ".$this->field."=:status WHERE ".$this->target."=:id";
                $res = $this->pdo->prepare($req);
                $res->bindValue(':status', $newStatus, PDO::PARAM_INT);
                $res->bindValue(':id', $id, PDO::PARAM_INT);
                $res->execute();

                return $newStatus?:0;
            }


        //---------------------------------------------------------
        // IMG GALLERY DISPLAY FUNCTION
        //---------------------------------------------------------

            public function getImgGalleryPhoto(int $id): array {

                $req = "SELECT * FROM ".$this->table." WHERE ".$this->field."=:id ORDER BY ".$this->target." ASC";				
                $res = $this->pdo->prepare($req);
                $res->bindValue(':id', $id, PDO::PARAM_INT);
                $res->execute();
                $nb=$res->rowCount();
                $tab = $res->fetchAll();
                $res->closeCursor();
                $res = NULL;

                return $tab?:[];

            }

        //---------------------------------------------------------
        // IMG LANG IMG FUNCTION
        //---------------------------------------------------------
		
            public function galleryImgLang(int $idImg, int $idLang, string $tableImg): ? object{
                
                $req = "SELECT * FROM ".$tableImg." WHERE idImg=:idImg AND idLang=:idLang";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idImg', $idImg, PDO::PARAM_INT);
                $res->bindValue(':idLang', $idLang, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);
                
                return $r?:null;
            }


    }