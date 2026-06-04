<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */

    //---------------------------------------------------------  
	// CLASS NAV MANAGER
	//--------------------------------------------------------- 

    namespace Front;
    use PDO;
    use InvalidArgumentException;

    class NavManager {
        protected PDO $pdo;
        public int $idLang;
        public int $idCategory;
        public int $checkparent;
        protected array $opts = [
            'table_site' => 'root_site',
            'table_category' => 'root_category',
            'table_category_lang' => 'root_category_lang',
            'table_pages' => 'root_pages',
        ];
        private array $translations;

        private PageManager $PageManager;
        private StringManager $StringManager;


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

                $this->opts['table_site'] = $this->validateTableName($this->opts['table_site']);
                $this->opts['table_category'] = $this->validateTableName($this->opts['table_category']);
                $this->opts['table_category_lang'] = $this->validateTableName($this->opts['table_category_lang']);
                $this->opts['table_pages'] = $this->validateTableName($this->opts['table_pages']);

                $this->translations = $translations;

 
                $this->PageManager= new PageManager($pdo,[
                    'table_site' => 'root_site',
                    'table_category' => 'root_category',
                    'table_category_lang' => 'root_category_lang',
                    'table_category_img' => 'root_category_img',
                    'table_pages' => 'root_pages',
                    'table_pages_img' => 'root_pages_img',
                    'table_pages_img_lang' => 'root_pages_img_lang',
                    'table_revision' => 'root_revision',
                    'table_footer' => 'root_footer',
                    'table_lang' => 'root_lang'
                    ]
                );

                $this->StringManager= new StringManager($pdo,['table_tags' => 'param_tags']);

            }


        //---------------------------------------------------------  
        // Function
        //---------------------------------------------------------

            protected function hasChildren(array $rows, int $id): ?bool {
                foreach ($rows as $row) {
                    if ($row['parent'] == $id){return true;}
                }
                return false;
            }

		
        //---------------------------------------------------------  
        // LAPTOP BUILD
        //---------------------------------------------------------

            protected function buildMenuLaptop(int $idCategory, int $checkParent, int $idLang, string $varlink, array $rows, int $parent=1): ?string{ 
                    
                if($parent==1){$result='<ul id="nav">';}else{$result = '<ul>';}

                foreach ($rows as $row){

                    if ($row['parent'] == $parent){

                        $catLink=$row['id'];
                        $categoryParent=$row['parent'];
                        $categoryColor=$row['color'];
                        $categoryTxtColor=$row['colorTxt'];
                        $siteHomePage=$row['homePage'];
                        $categorylink=$row['link'];
                        $catIdPage=$row['idPage'];
                        
                        if($catIdPage){
                            $catLink=$catIdPage;
                            $infoCat=$this->PageManager->infoCat($catIdPage,$idLang);
                            $infoPage=$this->PageManager->infoPage($catIdPage,$idLang);
                            $checkitem = $this->StringManager->cleanUrl(mb_convert_encoding($infoCat->name, 'UTF-8'));
                            if(intval($infoCat->switch)==15){$menuItemIcon=" learnMenuIcon";} else{$menuItemIcon="";}
                        }else{
                            $catLink=$row['id'];
                            $infoPage=$this->PageManager->infoPage($row['id'],$idLang);
                            $checkitem = $this->StringManager->cleanUrl(mb_convert_encoding($row['name'], 'UTF-8'));
                            if(intval($row['switch'])==15){$menuItemIcon=" learnMenuIcon";} else{$menuItemIcon="";}
                        }

                        if(($catLink==$idCategory)||($catLink==$siteHomePage)&&($categorylink=='/')){ 
                            $style='class="current'.$menuItemIcon.'"';
                            if($categoryParent==1){
                                $styleBalise='';
                            }else{
                                $styleBalise='style="color:'.$categoryColor.';"';
                            }
                        }else{
                            if($idCategory==$siteHomePage){
                                if($categoryParent==1){
                                    $style='';
                                    $styleBalise='';
                                }else{
                                    $style='';  
                                    $styleBalise='';
                                }
                            }else{
                                if($catLink==$checkParent){
                                    $style=''; 
                                    $styleBalise='';
                                }else{
                                    $style='';  
                                    $styleBalise='';
                                }
                            }
                        }

                        $result.= '<li>';
                        if(empty($categorylink)){
                            $result.= '<a href="'.((!empty($varlink))? '/'. $varlink : '').'/'.$catLink.'-'.$checkitem.'.html" title="'.((isset($infoPage->description))? $infoPage->description: '').'"  '.$style.'  '.$styleBalise.'>'.$row['name'].'</a>';
                        }else{
                            $result.= '<a href="'.$categorylink.'" title="'.$row['id'].'"  '.$style.' '.$styleBalise.'>'.mb_strtoupper($row['name'], 'iso-8859-1').'</a>';
                        }

                        if ($this->hasChildren($rows, $row['id'])){ 
                            $result.= $this->buildMenuLaptop($idCategory, $checkParent, $idLang, $varlink, $rows, $row['id']);
                        }
                        
                        $result.= "</li>";
                    }
                }
                $result.= "</ul>";

                return $result?:'';
            }


        //---------------------------------------------------------  
        // LAPTOP MAIN NAV
        //---------------------------------------------------------

            public function navLaptop(int $idSite, int $idCategory, int $checkParent, int $idLang, string $varLink, int $base): ?string{

                if(!empty($base)){
                    $req="	SELECT c.id, c.parent, c.status, c.position, c.switch, c.type, c.link, c.idPage, c.color, c.colorTxt, l.idCategory, l.name, l.idLang, p.description, s.varLink, s.homePage
                            FROM ".$this->opts['table_category']." c
                            INNER JOIN ".$this->opts['table_category_lang']." l
                            ON c.id=l.idCategory AND l.idLang=:idLang_A AND c.status=1
                            INNER JOIN ".$this->opts['table_pages']." p
                            ON c.id=p.idCategory
                            AND p.idLang=:idLang_B
                            INNER JOIN ".$this->opts['table_site']." s
                            ON c.idSite=s.id
                            WHERE c.idsite=:idSite
                            AND (c.id=:base OR c.parent=:base)
                            ORDER BY c.position";
                    $res = $this->pdo->prepare($req);
                    $res->bindValue(':idLang_A', $idLang, PDO::PARAM_INT);
                    $res->bindValue(':idLang_B', $idLang, PDO::PARAM_INT);
                    $res->bindValue(':base', $base, PDO::PARAM_INT);
                    $res->bindValue(':idSite', $idSite, PDO::PARAM_INT);
                }else{
                    $req="	SELECT c.id, c.parent, c.status, c.position, c.switch, c.type, c.link, c.idPage, c.color, c.colorTxt, l.idCategory, l.name, l.idLang, p.description, s.varLink, s.homePage
                            FROM ".$this->opts['table_category']." c
                            INNER JOIN ".$this->opts['table_category_lang']." l
                            ON c.id=l.idCategory AND l.idLang=:idLang_A AND c.status=1
                            INNER JOIN ".$this->opts['table_pages']." p
                            ON c.id=p.idCategory AND p.idLang=:idLang_B
                            INNER JOIN ".$this->opts['table_site']." s
                            ON c.idSite=s.id
                            WHERE c.idSite=:idSite
                            ORDER BY c.position";
                    $res = $this->pdo->prepare($req);
                    $res->bindValue(':idLang_A', $idLang, PDO::PARAM_INT);
                    $res->bindValue(':idLang_B', $idLang, PDO::PARAM_INT);
                    $res->bindValue(':idSite', $idSite, PDO::PARAM_INT);
                }
                $res->execute();
                $tab=$res->fetchAll();

                return $this->buildMenuLaptop($idCategory, $checkParent, $idLang, $varLink, $tab, 1);

            }


        //---------------------------------------------------------  
        // MOBILE BUILD
        //---------------------------------------------------------

            protected function buildMenuMobile(int $idCategory, int $checkParent, int $idLang, string $varLink, array $rows, int $parent=1): ?string{ 

                    
                if($parent==1){$result='<ul id="navmain">';}else{$result = '<ul>';}

                foreach ($rows as $row){

                    if ($row['parent'] == $parent){

                        $catLink=$row['id'];
                        $categoryParent=$row['parent'];
                        $categoryColor=$row['color'];
                        $categoryTxtColor=$row['colorTxt'];
                        $siteHomePage=$row['homePage'];
                        $categorylink=$row['link'];
                        $catIdPage=$row['idPage'];
                        
                        if($catIdPage){
                            $catLink=$catIdPage;
                            $infoCat=$this->PageManager->infoCat($catIdPage,$idLang);
                            $infoPage=$this->PageManager->infoPage($catIdPage,$idLang);
                            $checkitem = $this->StringManager->cleanUrl(mb_convert_encoding($infoCat->name, 'UTF-8'));
                            if(intval($infoCat->switch)==15){$menuItemIcon=" learnMenuIcon";} else{$menuItemIcon="";}
                        }else{
                            $catLink=$row['id'];
                            $infoPage=$this->PageManager->infoPage($row['id'],$idLang);
                            $checkitem = $this->StringManager->cleanUrl(mb_convert_encoding($row['name'], 'UTF-8'));
                            if(intval($row['switch'])==15){$menuItemIcon=" learnMenuIcon";} else{$menuItemIcon="";}
                        }

                        if(($catLink==$idCategory)||($catLink==$siteHomePage)&&($categorylink=='/')){ 
                            $style='class="current'.$menuItemIcon.'"';
                            if($categoryParent==1){
                                //$styleBalise='style="color:'.$categoryColor.';"';
                                $styleBalise='';
                            }else{
                                //$styleBalise='style="color:'.$categoryColor.';"';
                                $styleBalise='';
                            }
                        }else{
                            if($idCategory==$siteHomePage){
                                if($categoryParent==1){
                                    $style='class="current'.$menuItemIcon.'"';
                                    $styleBalise='style="color:'.$categoryColor.';"';
                                }else{
                                    $style='';  
                                    $styleBalise='';
                                }
                            }else{
                                if($catLink==$checkParent){
                                    $style=''; 
                                    $styleBalise='style="color:'.$categoryColor.';"';
                                }else{
                                    $style='';  
                                    $styleBalise='';
                                }
                            }
                        }


                        $result.= '<li>';
                        if(empty($categorylink)){
                            $result.= '<a href="'.((!empty($varLink))? '/'. $varLink : '').'/'.$catLink.'-'.$checkitem.'.html" title="'.((isset($infoPage->description))? $infoPage->description: '').'"  '.$style.' >'.$row['name'].'</a>';
                        }else{
                            $result.= '<a href="/'.$categorylink.'" title="'.((isset($infoPage->description))? $infoPage->description: '').'"  '.$style.' '.$styleBalise.'>'.mb_strtoupper($row['name'], 'iso-8859-1').'</a>';
                        }

                        if ($this->hasChildren($rows,$row['id'])){ 
                            $result.= $this->buildMenuMobile($idCategory, $checkParent, $idLang, $varLink, $rows, $row['id'],1);
                        }
                        
                        $result.= "</li>";
                    }
                }
                $result.= "</ul>";

                return $result;
            }

        //---------------------------------------------------------  
        // MOBILE MAIN NAV
        //---------------------------------------------------------

            public function navMobile(int $idSite, int $idCategory, int $checkParent, int $idLang, string $varLink, int $base): ?string{

                if(!empty($base)){
                    $req="	SELECT c.id, c.parent, c.status, c.position, c.switch, c.type, c.link, c.idPage, c.color, c.colorTxt, l.idCategory, l.name, l.idLang, p.description, s.varLink, s.homePage
                            FROM ".$this->opts['table_category']." c
                            INNER join ".$this->opts['table_category_lang']." l
                            ON c.id=l.idCategory AND l.idLang=:idLang_A AND c.status=1
                            INNER join ".$this->opts['table_pages']." p
                            ON c.id=p.idCategory
                            AND p.idLang=:idLang_B
                            INNER join ".$this->opts['table_site']." s
                            ON c.idSite=s.id
                            WHERE c.idSite=:idSite
                            AND (c.id=:base OR c.parent=:base)
                            ORDER BY c.position";
                    $res = $this->pdo->prepare($req);
                    $res->bindValue(':idLang', $idLang, PDO::PARAM_INT);
                    $res->bindValue(':base', $base, PDO::PARAM_INT);
                    $res->bindValue(':idSite', $idSite, PDO::PARAM_INT);
                }else{
                    $req="	SELECT c.id, c.parent, c.status, c.position, c.switch, c.type, c.link, c.idPage, c.color, c.colorTxt, l.idCategory, l.name, l.idLang, p.description, s.varLink, s.homePage
                            FROM ".$this->opts['table_category']." c
                            INNER join ".$this->opts['table_category_lang']." l
                            ON c.id=l.idCategory AND l.idLang=:idLang_A AND c.status=1
                            INNER JOIN ".$this->opts['table_pages']." p
                            ON c.id=p.idCategory AND p.idLang=:idLang_B
                            INNER join ".$this->opts['table_site']." s
                            ON c.idSite=s.id
                            WHERE c.idsite=:idSite
                            ORDER BY c.position";
                    $res = $this->pdo->prepare($req);
                    $res->bindValue(':idLang_A', $idLang, PDO::PARAM_INT);
                    $res->bindValue(':idLang_B', $idLang, PDO::PARAM_INT);
                    $res->bindValue(':idSite', $idSite, PDO::PARAM_INT);
                }
                $res = $this->pdo->prepare($req);
                $res->bindValue(':idLang_A', $idLang, PDO::PARAM_INT);
                $res->bindValue(':idLang_B', $idLang, PDO::PARAM_INT);
                $res->bindValue(':idSite', $idSite, PDO::PARAM_INT);
                //$res->bindValue(':base', $base, PDO::PARAM_INT);
                $res->execute();
                $tab=$res->fetchAll();

                return $this->buildMenuMobile($idCategory, $checkParent, $idLang, $varLink, $tab, 1);

            }

    }