<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */

    //---------------------------------------------------------  
	// CLASS FRONT CONTACT MANAGER
	//--------------------------------------------------------- 

    namespace Front;
    use PDO;
    use InvalidArgumentException;

    class ContactManager {
        protected PDO $pdo;
        protected array $opts = [
            'table_item' => 'contact_item',
            'table_lang' => 'contact_lang'
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

                $this->translations = $translations;

            }


        //---------------------------------------------------------  
        // Check email Adresse
        //--------------------------------------------------------- 

            public function VerifEmail(string $adresse): ?bool{  
                $Syntaxe='#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#';  
                if(preg_match($Syntaxe,$adresse)){return true;}else{return false;}
            }

		
        //---------------------------------------------------------
        // Function contact
        //---------------------------------------------------------
	
            public function checkPositionContact(int $position): ?int{
                
                $req = "SELECT * FROM ".$this->opts['table_item']." WHERE position=:position";			 
                $res = $this->pdo->prepare($req);
                $res->execute(array(':position'=>$position));
                $nb=$res->rowCount();
                
                return $nb?:0;
            }
	

            public function maxPositionContact(): ?int{
                
                $req = "SELECT Max(position) AS pos FROM ".$this->opts['table_item'];
                $res = $this->pdo->prepare($req);
                $res->execute();		
                $r = $res->fetch(PDO::FETCH_OBJ);
                
                $count=$r->pos;
                    
                return $count?:0;
                    
            }
	

        //---------------------------------------------------------
        // Suppresion recalcul position input
        //---------------------------------------------------------
	
            public function recalculPositionContact(): void {

                $req = "SELECT * FROM ".$this->opts['table_item']." ORDER BY position";				
                $res = $this->pdo->prepare($req);
                $res->execute();
                $tab = $res->fetchAll();
                $nb = count($tab);
                
                for($i=0;$i<$nb;$i++){
                    $newposition=$i+1;
                    $reqmod = "UPDATE ".$this->opts['table_item']." SET position=:position WHERE id=:idItem";
                    $resmod = $this->pdo->prepare($reqmod);
                    $resmod->bindValue(':position', $newposition, PDO::PARAM_INT);
                    $resmod->bindValue(':idItem', $tab[$i]['id'], PDO::PARAM_INT);
                    $resmod->execute();
                }
            }


        //---------------------------------------------------------
        // Récupération info page par id
        //---------------------------------------------------------

            public function infoContactType(int $idContact, int $idLang): ?object{
                
                $req = "SELECT * FROM ".$this->opts['table_lang']." WHERE idContact=:idContact AND idLang=:idLang";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idContact', $idContact, PDO::PARAM_INT);
                $res->bindValue(':idLang', $idLang, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);
                
                return $r?:null;
            }

		
        //---------------------------------------------------------  
        // Récupération texte localisation
        //---------------------------------------------------------
            
            public function contactLang(int $idContact, int $idLang): ?object{
                
                $req = "SELECT * FROM ".$this->opts['table_lang']." WHERE idContact=:idContact AND idLang=:idLang";	
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idContact', $idContact, PDO::PARAM_INT);
                $res->bindValue(':idLang', $idLang, PDO::PARAM_INT);
                $res->execute();
                $r = $res->fetch(PDO::FETCH_OBJ);
                
                return $r?:null;
            }

    }