<?php declare(strict_types=1);

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */


    //---------------------------------------------------------
   	// INIT NAME SPACES
   	//--------------------------------------------------------- 
	
		use Tools\SecureManager;
		use Front\PageManager;
		use Front\langManager;

		
   //---------------------------------------------------------
   // CONNEXIONS
   //---------------------------------------------------------

		include($_SERVER['DOCUMENT_ROOT'].'/config.php');


   //---------------------------------------------------------  
   // INIT CLASS
   //---------------------------------------------------------
			
		$secure = new SecureManager(requirePost: true, requireCsrf: true);
		$lang= new LangManager($db);
		$page= new PageManager($db);
		
		
  	//---------------------------------------------------------  
	// LANGUAGE
	//---------------------------------------------------------
	
      	$tabLang=$lang->activeListLang();
		$nbLang=count($tabLang);
   
		
   //---------------------------------------------------------  
   // Variable
   //--------------------------------------------------------- 

		$varLink = $secure->session('siteData.varLink', 'string', false) ?? '';
		$idSite = $secure->session('siteData.id', 'int', false) ?? 0;
		$htmldata = $secure->v('string', 'html', false) ?? '';
		$cssdata = $secure->v('string', 'css', false) ?? '';
		$idCategory = $secure->v('int', 'idDuplicate', false) ?? 0;
		
		$idLang=1;
		$ri=$page->infoCat($idCategory,$idLang);
		$rc="";

			
   //---------------------------------------------------------  
   // DATA INSERT NEW CATEGORY
   //---------------------------------------------------------

		$reqAddCategory = "INSERT INTO ".$prefixRoot."category (idSite,parent,status,type,position,link,color,colorTxt) VALUES(:idSite, :parent, :status, :type, :position, :link,:color, :colorTxt)";
		$resAddCategory = $db->prepare($reqAddCategory);
		$resAddCategory->bindValue(':parent', $ri->parent, PDO::PARAM_INT);
		$resAddCategory->bindValue(':status', "0", PDO::PARAM_INT);
		$resAddCategory->bindValue(':type', (isset($ri->type))? $ri->type : 0, PDO::PARAM_INT);
		$resAddCategory->bindValue(':position', "0", PDO::PARAM_STR);
		$resAddCategory->bindValue(':link', (isset($ri->link))? $ri->link : '', PDO::PARAM_STR);
		$resAddCategory->bindValue(':idSite', $idSite, PDO::PARAM_STR);
		$resAddCategory->bindValue(':color', (isset($ri->color))? $ri->color : '', PDO::PARAM_STR);
		$resAddCategory->bindValue(':colorTxt', (isset($ri->colorTxt))? $ri->colorTxt : '', PDO::PARAM_STR);
		$resAddCategory->execute();
		$resAddCategory->closeCursor();
		$resAddCategory = NULL;
			
		$idCategoryinsert=$db->lastInsertId();

			
		for($i=0; $i<$nbLang;$i++){
			
			$idLang=$tabLang[$i]['id'];
			
			$rp=$page->infoPage($idCategory,$idLang);
			
			$rc=$page->infoCat($idCategory,$idLang);
			
			$builderHtml=(isset($rp->text)? $rp->text : '');
			$builderCss=(isset($rp->css)? $rp->css : '');
			$components=(isset($rp->components)? $rp->components : '[]');
			$assets=(isset($rp->assets)? $rp->assets : '[]');
			$styles=(isset($rp->styles)? $rp->styles : '[]');
			
			
   //---------------------------------------------------------  
   // DATA INSERT NEW CATEGORY LANG
   //---------------------------------------------------------

			$reqAddCategoryLang = "INSERT INTO ".$prefixRoot."category_lang (idSite,idCategory,name,idLang) VALUES(:idSite, :idCategory, :name, :idLang)";
			$resAddCategoryLang = $db->prepare($reqAddCategoryLang);
			$resAddCategoryLang->bindValue(':name',  (isset($rc->name))? $rc->name." | Clone" : ''." | Clone", PDO::PARAM_STR);
			$resAddCategoryLang->bindValue(':idCategory', $idCategoryinsert, PDO::PARAM_INT);
			$resAddCategoryLang->bindValue(':idLang', $idLang, PDO::PARAM_INT);
			$resAddCategoryLang->bindValue(':idSite', $idSite, PDO::PARAM_INT);
			$resAddCategoryLang->execute();
			$resAddCategoryLang->closeCursor();
			$resAddCategoryLang = NULL;


   //---------------------------------------------------------  
   // DATA INSERT NEW PAGE
   //---------------------------------------------------------

			$reqAddPage = "INSERT INTO ".$prefixRoot."pages (idSite,idCategory,item,name,title,text,css,components,assets,styles,idLang)
			VALUES(:idSite,:idCategory,:item,:name,:title,:text,:css,:components,:assets,:styles,:idLang)";
			$resAddPage = $db->prepare($reqAddPage);
			$resAddPage->bindValue(':idCategory', $idCategoryinsert, PDO::PARAM_STR);
			$resAddPage->bindValue(':item', (isset($rc->item))? $rc->item." | Clone" : ''." | Clone", PDO::PARAM_STR);
			$resAddPage->bindValue(':name', (isset($rc->name))? $rc->name." | Clone" : ''." | Clone", PDO::PARAM_STR);
			$resAddPage->bindValue(':title', (isset($rp->title))? $rp->title." | Clone" : ''." | Clone", PDO::PARAM_STR);
			$resAddPage->bindValue(':text', $builderHtml, PDO::PARAM_STR);
			$resAddPage->bindValue(':css', $builderCss, PDO::PARAM_STR);
			$resAddPage->bindValue(':components', $components, PDO::PARAM_STR);
			$resAddPage->bindValue(':assets', $assets, PDO::PARAM_STR);
			$resAddPage->bindValue(':styles', $styles, PDO::PARAM_STR);
			$resAddPage->bindValue(':idLang', $idLang, PDO::PARAM_INT);
			$resAddPage->bindValue(':idSite', $idSite, PDO::PARAM_INT);
			$resAddPage->execute();
			$resAddPage->closeCursor();
			$resAddPage = NULL;
			
		}
			
						
   //---------------------------------------------------------  
   // PROCESS
   //--------------------------------------------------------- 
			
			$urllinksc='/manager'.$varLink.'/admin_tree/param_content/'.$idCategoryinsert.'/1/0/0/';
			
			$newPage='';
			
			$newPage.='<li data-id="'.$idCategoryinsert.'" id="list_page_'.$idCategoryinsert.'">';

			$newPage.= '<div class="item" style="border-left:15px solid '.$ri->color.'; border-radius:5px;">';
			$newPage.= '<table style="border:0px;" width="100%" cellspacing="0" cellpadding="0">';
			$newPage.= '<tr>';
			$newPage.= '<td valign="bottom" width="20">';
			$newPage.= '<img src="/img/interface/icons/poubelleoff.png" width="16" height="16" style="border:0px;" class="delete"  id="'.$idCategoryinsert.'"/>';
			$newPage.= '</td>';
			$newPage.= '<td style="text-align:left;">&nbsp;|&nbsp;<a href="'.$urllinksc.'" target="_self" >'.$idCategoryinsert.' - '.$ri->name.' | Clone</a></td>';
			$newPage.= '<td width="5"><i class="gg-duplicate" id="'.$idCategoryinsert.'"></i></td>';
			$newPage.= '<td width="20">&nbsp;</td>';
			$newPage.= '<td width="5" style="text-align:right;">';
			$newPage.= '<button id="checkStatus'.$idCategoryinsert.'" data-id="'.$idCategoryinsert.'" data-table="root_category" data-field="status" data-target-id="id" data-output="green" style="border:0px;background:none;">';
            $newPage.= '<img src="/img/interface/icons/statusOff.png" width="13" height="13" style="border:0px;"/>';
            $newPage.= '</button>';
			$newPage.= '</td>';
			$newPage.= '</tr>';
			$newPage.= '</table>';
			$newPage.= '</div>';			 

			$newPage.='</li>';	
			
			$status=1;

			
	
	//---------------------------------------------------------
  	// RESPONSE
   	//---------------------------------------------------------

		$response = [
			'error' => false,
			'status' => $status,
			'idCategory' => $idCategory,
			'newPage' => $newPage
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;