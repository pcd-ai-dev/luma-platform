<?php

/*
*  @author Lumaprod - Pierre Cosmao Dumanoir
*  @copyright  2003-2026 Lumaprod - Pierre Cosmao Dumanoir
*  @version  Release: 3
*/

    //---------------------------------------------------------
   	// INIT NAME SPACES
   	//---------------------------------------------------------

		use Xml\XmlManager;

	//---------------------------------------------------------  
	// INIT CLASS
	//--------------------------------------------------------- 

		$xml= new XmlManager($db,$idSite,$siteName, $siteHost, $imgDefaultSquare);

	//---------------------------------------------------------  
	// FILE SECURE
	//---------------------------------------------------------  

		if (!$session->getAdminData()) {
			header('HTTP/1.1 403 Forbidden');
			exit('Accès interdit');
		}


    //--------------------------------------------------------------------------------\\
	//--------------------------------------------------------------------------------\\
	// Ajout Page
	//--------------------------------------------------------------------------------\\
	//--------------------------------------------------------------------------------\\


	//--------------------------------------------------------
	// Vars
	//--------------------------------------------------------

		$idSite=(isset($adminData['idSite']))? intval($adminData['idSite']) : 0;	

	//--------------------------------------------------------
	// process
	//--------------------------------------------------------

   
		if(isset($_POST['addCategoryStart'])){ 

		// Récupération variable

			$idSite   = isset($adminData['idSite']) ? intval($adminData['idSite']) : 0;
			$parent   = $secure->v('int', 'parent', false) ?? 0;
			$status   = $secure->v('int', 'status', false) ?? 0;
			$position = $secure->v('int', 'position', false) ?? 0;
			$link     = $secure->v('string', 'link', false) ?? '';
			$type     = $secure->v('int', 'type', false) ?? 0;


			$checkPosition=$page->checkPosition($position,$parent);
			$countPosition=$page->maxPosition($parent);

			if($checkPosition==0){}else{		
				for($i=$countPosition;$i>=$position;$i--){		
					$newPosition=$i+1;
					$page->updatePosition($parent,$newPosition,$i);			
				}	
			}
	
	
	
   //---------------------------------------------------------  
   // BUILDER HTML
   //---------------------------------------------------------
	
		$builderHtml="";
		$builderHtml.='
		<body>
		<section id="i7h4" class="hero">
			<div class="container">
			<h1 id="i79e">Nom de la page
			</h1>
			<p>
				Base line de la page 
			</p>
			</div>
		</section>
		<section class="news-section" id="ij3q8">
			<div class="container" id="iaeok">
			<div class="news-grid" id="ie9rp">
				Contenu de la page
			</div>
			</div>
		</section>
		</body>
		';
			

   //---------------------------------------------------------  
   // BUILDER CSS
   //---------------------------------------------------------
	
		$builderCss="";
		$builderCss.='
		*{
		margin-top:0px;
		margin-right:0px;
		margin-bottom:0px;
		margin-left:0px;
		padding-top:0px;
		padding-right:0px;
		padding-bottom:0px;
		padding-left:0px;
		box-sizing:border-box;
		}
		body{
			font-family:Arial, sans-serif;
			background-image:none;
			background-position-x:0%;
			background-position-y:0%;
			background-size:auto;
			background-repeat:repeat;
			background-attachment:scroll;
			background-origin:padding-box;
			background-clip:border-box;
			background-color:rgb(248, 250, 252);
			color:rgb(31, 41, 55);
			line-height:1.6;
		}
		.container{
			max-width:1200px;
			margin-top:auto;
			margin-right:auto;
			margin-bottom:auto;
			margin-left:auto;
			padding-top:0px;
			padding-right:20px;
			padding-bottom:0px;
			padding-left:20px;
		}
		.hero{
			background-image:linear-gradient(135deg, rgb(37, 99, 235), rgb(29, 78, 216));
			background-position-x:0%;
			background-position-y:0%;
			background-size:auto;
			background-repeat:repeat;
			background-attachment:scroll;
			background-origin:padding-box;
			background-clip:border-box;
			background-color:transparent;
			color:white;
			text-align:center;
			padding-top:80px;
			padding-right:20px;
			padding-bottom:80px;
			padding-left:20px;
		}
		.hero h1{
			font-size:3rem;
			margin-bottom:15px;
		}
		.hero p{
			max-width:700px;
			margin-top:auto;
			margin-right:auto;
			margin-bottom:auto;
			margin-left:auto;
		}
		.news-section{
			padding-top:80px;
			padding-right:0px;
			padding-bottom:80px;
			padding-left:0px;
		}
		.news-grid{
			display:grid;
			grid-template-columns:repeat(auto-fill, minmax(340px, 1fr));
			row-gap:30px;
			column-gap:30px;
		}
		.news-card img{
			width:100%;
			height:220px;
			object-fit:cover;
			display:block;
		}
		.pagination a{
			text-decoration-line:none;
			padding-top:10px;
			padding-right:15px;
			padding-bottom:10px;
			padding-left:15px;
			background-image:none;
			background-position-x:0%;
			background-position-y:0%;
			background-size:auto;
			background-repeat:repeat;
			background-attachment:scroll;
			background-origin:padding-box;
			background-clip:border-box;
			background-color:white;
			border-top-width:1px;
			border-right-width:1px;
			border-bottom-width:1px;
			border-left-width:1px;
			border-top-style:solid;
			border-right-style:solid;
			border-bottom-style:solid;
			border-left-style:solid;
			border-top-color:rgb(229, 231, 235);
			border-right-color:rgb(229, 231, 235);
			border-bottom-color:rgb(229, 231, 235);
			border-left-color:rgb(229, 231, 235);
			border-image-source:none;
			border-image-slice:100%;
			border-image-width:1;
			border-image-outset:0;
			border-image-repeat:stretch;
			border-top-left-radius:8px;
			border-top-right-radius:8px;
			border-bottom-right-radius:8px;
			border-bottom-left-radius:8px;
			color:rgb(55, 65, 81);
		}
		.pagination a.active{
			background-image:none;
			background-position-x:0%;
			background-position-y:0%;
			background-size:auto;
			background-repeat:repeat;
			background-attachment:scroll;
			background-origin:padding-box;
			background-clip:border-box;
			background-color:rgb(37, 99, 235);
			color:white;
			border-top-color:rgb(37, 99, 235);
			border-right-color:rgb(37, 99, 235);
			border-bottom-color:rgb(37, 99, 235);
			border-left-color:rgb(37, 99, 235);
		}
		#i79e{
			color:#00cd9d;
		}
		#i7h4{
			background-image:unset;
			background-repeat:unset;
			background-position:unset;
			background-attachment:unset;
			background-size:unset;
			background-image-color:unset;
			background-image-gradient:unset;
			background-image-gradient-dir:unset;
			background-image-gradient-type:unset;
			background-color:#132436;
		}
		#ie9rp{
			display:block;
			text-align:center;
		}
		@media (max-width: 768px){
			.hero h1{
			font-size:2rem;
			}
			.news-grid{
			grid-template-columns:1fr;
			}
			#i7h4{
			padding:250px 20px 80px 20px;
			}
		}
		@media (max-width: 480px){
			#i7h4{
			padding:180px 20px 80px 20px;
			}
		}
		';

	
	
	//---------------------------------------------------------  
	// COMPONENTS CSS
   	//---------------------------------------------------------

		$builderComponents="";
		$builderComponents.='[{"tagName":"section","classes":["hero"],"attributes":{"id":"i7h4"},"components":[{"classes":["container"],"components":[{"type":"header","attributes":{"id":"i79e"},"components":[{"type":"textnode","content":"Nom de la page\n      "}]},{"tagName":"p","type":"text","components":[{"type":"textnode","content":"\n        Base line de la page\n      "}]}]}]},{"tagName":"section","classes":["news-section"],"attributes":{"id":"ij3q8"},"components":[{"classes":["container"],"attributes":{"id":"iaeok"},"components":[{"type":"text","classes":["news-grid"],"attributes":{"id":"ie9rp"},"components":[{"type":"textnode","content":"\n       Contenu de la page\n      "}]}]}]}]';


	//---------------------------------------------------------  
	// STYLES CSS
   	//---------------------------------------------------------

		$builderStyles="";
		$builderStyles.='[{"selectors":[],"selectorsAdd":"*","style":{"margin-top":"0px","margin-right":"0px","margin-bottom":"0px","margin-left":"0px","padding-top":"0px","padding-right":"0px","padding-bottom":"0px","padding-left":"0px","box-sizing":"border-box"}},{"selectors":[],"selectorsAdd":"body","style":{"font-family":"Arial, sans-serif","background-image":"none","background-position-x":"0%","background-position-y":"0%","background-size":"auto","background-repeat":"repeat","background-attachment":"scroll","background-origin":"padding-box","background-clip":"border-box","background-color":"rgb(248, 250, 252)","color":"rgb(31, 41, 55)","line-height":"1.6"}},{"selectors":["container"],"style":{"max-width":"1200px","margin-top":"auto","margin-right":"auto","margin-bottom":"auto","margin-left":"auto","padding-top":"0px","padding-right":"20px","padding-bottom":"0px","padding-left":"20px"}},{"selectors":["hero"],"style":{"background-image":"linear-gradient(135deg, rgb(37, 99, 235), rgb(29, 78, 216))","background-position-x":"0%","background-position-y":"0%","background-size":"auto","background-repeat":"repeat","background-attachment":"scroll","background-origin":"padding-box","background-clip":"border-box","background-color":"transparent","color":"white","text-align":"center","padding-top":"80px","padding-right":"20px","padding-bottom":"80px","padding-left":"20px"}},{"selectors":[],"selectorsAdd":".hero h1","style":{"font-size":"3rem","margin-bottom":"15px"}},{"selectors":[],"selectorsAdd":".hero p","style":{"max-width":"700px","margin-top":"auto","margin-right":"auto","margin-bottom":"auto","margin-left":"auto"}},{"selectors":["news-section"],"style":{"padding-top":"80px","padding-right":"0px","padding-bottom":"80px","padding-left":"0px"}},{"selectors":["news-grid"],"style":{"display":"grid","grid-template-columns":"repeat(auto-fill, minmax(340px, 1fr))","row-gap":"30px","column-gap":"30px"}},{"selectors":[],"selectorsAdd":".news-card img","style":{"width":"100%","height":"220px","object-fit":"cover","display":"block"}},{"selectors":[],"selectorsAdd":".pagination a","style":{"text-decoration-line":"none","padding-top":"10px","padding-right":"15px","padding-bottom":"10px","padding-left":"15px","background-image":"none","background-position-x":"0%","background-position-y":"0%","background-size":"auto","background-repeat":"repeat","background-attachment":"scroll","background-origin":"padding-box","background-clip":"border-box","background-color":"white","border-top-width":"1px","border-right-width":"1px","border-bottom-width":"1px","border-left-width":"1px","border-top-style":"solid","border-right-style":"solid","border-bottom-style":"solid","border-left-style":"solid","border-top-color":"rgb(229, 231, 235)","border-right-color":"rgb(229, 231, 235)","border-bottom-color":"rgb(229, 231, 235)","border-left-color":"rgb(229, 231, 235)","border-image-source":"none","border-image-slice":"100%","border-image-width":"1","border-image-outset":"0","border-image-repeat":"stretch","border-top-left-radius":"8px","border-top-right-radius":"8px","border-bottom-right-radius":"8px","border-bottom-left-radius":"8px","color":"rgb(55, 65, 81)"}},{"selectors":[],"selectorsAdd":".pagination a.active","style":{"background-image":"none","background-position-x":"0%","background-position-y":"0%","background-size":"auto","background-repeat":"repeat","background-attachment":"scroll","background-origin":"padding-box","background-clip":"border-box","background-color":"rgb(37, 99, 235)","color":"white","border-top-color":"rgb(37, 99, 235)","border-right-color":"rgb(37, 99, 235)","border-bottom-color":"rgb(37, 99, 235)","border-left-color":"rgb(37, 99, 235)"}},{"selectors":[],"selectorsAdd":".hero h1","style":{"font-size":"2rem"},"mediaText":"(max-width: 768px)","atRuleType":"media"},{"selectors":["news-grid"],"style":{"grid-template-columns":"1fr"},"mediaText":"(max-width: 768px)","atRuleType":"media"},{"selectors":["#i7h4"],"style":{"padding":"250px 20px 80px 20px"},"mediaText":"(max-width: 768px)","atRuleType":"media"},{"selectors":["#i7h4"],"style":{"padding":"180px 20px 80px 20px"},"mediaText":"(max-width: 480px)","atRuleType":"media"},{"selectors":["#i79e"],"style":{"color":"#00cd9d"}},{"selectors":["#i7h4"],"style":{"__background-type":"unset","background-image":"unset","background-repeat":"unset","background-position":"unset","background-attachment":"unset","background-size":"unset","background-image-color":"unset","background-image-gradient":"unset","background-image-gradient-dir":"unset","background-image-gradient-type":"unset","background-color":"#132436"}},{"selectors":["#ie9rp"],"style":{"display":"block","text-align":"center"}}]';


   //---------------------------------------------------------  
   // Insertion db nouvelle  category
   //---------------------------------------------------------

		$reqcadd = "INSERT INTO ".$prefixRoot."category (idSite,parent,status,type,position,link) VALUES(:idSite, :parent, :status, :type, :position, :link)";
		$rescadd = $db->prepare($reqcadd);
		$rescadd->bindValue(':parent', $parent, PDO::PARAM_STR);
		$rescadd->bindValue(':status', $status, PDO::PARAM_STR);
		$rescadd->bindValue(':type', $type, PDO::PARAM_STR);
		$rescadd->bindValue(':position', $position, PDO::PARAM_STR);
		$rescadd->bindValue(':link', $link, PDO::PARAM_STR);
		$rescadd->bindValue(':idSite', $idSite, PDO::PARAM_STR);
		$rescadd->execute();
		$rescadd->closeCursor();
		$rescadd = NULL;
			
		$idCategoryinsert=$db->lastInsertId();
		
	// Insertion langue
	
		for($i=0; $i<$nbLang;$i++){

		$idLang=$tabLang[$i]['id'];
		$name='name_'.$idLang;
		${$name} = ucfirst($secure->v('string', $name, false) ?? '');
		
		
   //---------------------------------------------------------  
   // Insertion db langue category
   //---------------------------------------------------------
			
		$reqcaddl = "INSERT INTO ".$prefixRoot."category_lang (idSite,idCategory,name,idLang) VALUES(:idSite, :idCategory, :name, :idLang)";
		$rescaddl = $db->prepare($reqcaddl);
		$rescaddl->bindValue(':name', ${$name}, PDO::PARAM_STR);
		$rescaddl->bindValue(':idCategory', $idCategoryinsert, PDO::PARAM_STR);
		$rescaddl->bindValue(':idLang', $idLang, PDO::PARAM_STR);
		$rescaddl->bindValue(':idSite', $idSite, PDO::PARAM_STR);
		$rescaddl->execute();
		$rescaddl->closeCursor();
		$rescaddl = NULL;
		
		
   //---------------------------------------------------------  
   // Insertion db nouvelle  page
   //---------------------------------------------------------
   
		$checkitem = $string->cleanUrl(mb_convert_encoding(${$name}, 'UTF-8'));
			
		if($type==1){
			$reqpadd = "INSERT INTO ".$prefixRoot."pages (idSite,idCategory,item,name,title,text,css,components,styles,idLang) VALUES(:idSite,:idCategory,:item,:name,:title,:text,:css,:components,:styles,:idLang)";
			$respadd = $db->prepare($reqpadd);
			$respadd->bindValue(':idSite', $idSite, PDO::PARAM_STR);
			$respadd->bindValue(':idCategory', $idCategoryinsert, PDO::PARAM_STR);
			$respadd->bindValue(':item', $checkitem, PDO::PARAM_STR);
			$respadd->bindValue(':name', ${$name}, PDO::PARAM_STR);
			$respadd->bindValue(':title', ${$name}, PDO::PARAM_STR);
			$respadd->bindValue(':text', $builderHtml, PDO::PARAM_STR);
			$respadd->bindValue(':css', $builderCss, PDO::PARAM_STR);
			$respadd->bindValue(':components', $builderComponents, PDO::PARAM_STR);
			$respadd->bindValue(':styles', $builderStyles, PDO::PARAM_STR);
			$respadd->bindValue(':idLang', $idLang, PDO::PARAM_STR);
			$respadd->execute();
			$respadd->closeCursor();
			$respadd = NULL;
		}else{
			$reqpadd = "INSERT INTO ".$prefixRoot."pages (idSite,idCategory,item,name,title,idLang) VALUES(:idSite,:idCategory,:item,:name,:title,:idLang)";
			$respadd = $db->prepare($reqpadd);
			$respadd->bindValue(':idSite', $idSite, PDO::PARAM_STR);
			$respadd->bindValue(':idCategory', $idCategoryinsert, PDO::PARAM_STR);
			$respadd->bindValue(':item', $checkitem, PDO::PARAM_STR);
			$respadd->bindValue(':name', ${$name}, PDO::PARAM_STR);
			$respadd->bindValue(':title', ${$name}, PDO::PARAM_STR);
			$respadd->bindValue(':idLang', $idLang, PDO::PARAM_STR);
			$respadd->execute();
			$respadd->closeCursor();
			$respadd = NULL;
		}


		}
		
		
		$xml->updateSitemap();

}

	//---------------------------------------------------------  
	// CATEGORY TREE
	//--------------------------------------------------------- 

		$rt = $page->getCatTree($idSite);
		$catTree = $rt['catTree'];
		$tabCat = $rt['tab'];