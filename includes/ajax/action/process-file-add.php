<?php declare(strict_types=1);

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */


    //---------------------------------------------------------
   	// INIT NAME SPACES
   	//---------------------------------------------------------

		use Tools\FileOwnerInterface;
        use Tools\SecureManager;


   //---------------------------------------------------------
   // CONNEXIONS
   //---------------------------------------------------------

      include($_SERVER['DOCUMENT_ROOT'].'/config.php');


   //---------------------------------------------------------
   // INIT CLASS SECURE
   //---------------------------------------------------------

		$secure = new SecureManager(requirePost: true, requireCsrf: true);


   //---------------------------------------------------------  
   // VARIABLES
   //---------------------------------------------------------

        $entity=$secure->v('string', 'entity', false) ?? '';
        $table=$secure->v('string', 'table', false) ?? '';
        $field=$secure->v('string', 'field', false) ?? '';
        $target=$secure->v('string', 'target', false) ?? '';
        $folder=$secure->v('string', 'folder', false) ?? '';
        $label=$secure->v('string', 'label', false) ?? '';
        $type=$secure->v('int', 'type', false) ?? 0;
        $id=$secure->v('int', 'id', false) ?? 0;
        $idParent=$secure->v('int', 'idParent', false) ?? 0;
        $idSite=$secure->v('int', 'idSite', false) ?? 0;
        $uid = uniqid('upl_');

   //---------------------------------------------------------  
   // CLASS ENTITIES
   //---------------------------------------------------------

        $entities = require PROCESSPATH.'ajax/action/process-entities.php';

        if (!isset($entities[$entity])) {
            die('<div class="error">Entity non autorisée</div>');
        } 

        $managerClass = $entities[$entity]['manager'];


   //---------------------------------------------------------  
   // INIT CLASS
   //---------------------------------------------------------

        $manager = new $managerClass($db);

        if (!$manager instanceof FileOwnerInterface) {
            die('<div class="error">Manager invalide</div>');
        }

   //---------------------------------------------------------  
   // GET CURRENT FILE/IMG
   //---------------------------------------------------------

        $currentFile = $manager->getFile($id, $field);

        $filePath = match ($field) {
            'url' => $manager->getFolderPath($idParent, $folder),
            'rec' => $manager->getRecPath($idParent, $folder),
            'imgMobile' => $manager->getMobilePath($idParent, $folder),
            default => $manager->getSquarePath($idParent, $folder),
        };


   //---------------------------------------------------------  
   // GET ICON FILE
   //---------------------------------------------------------

        if (empty($currentFile)) {
            $docPath = $iconDisplay = $imgDefaultSquare;
            $fullFilePath = "";
        } else {
            $fullFilePath = $filePath . $currentFile;

            $map = [
                'pdf' => 'pdf.svg',
                'doc' => 'doc.svg',
                'docx' => 'doc.svg',
                'xls' => 'xls.svg',
                'xlsx' => 'xls.svg',
                'jpg' => 'jpg.svg',
                'jpeg' => 'jpg.svg',
                'png' => 'png.svg',
                'zip' => 'zip.svg',
            ];

            $extension = strtolower(pathinfo($currentFile, PATHINFO_EXTENSION));
            $icon = $map[$extension] ?? 'other.svg';

            $docPath = $fullFilePath;
            $iconDisplay ="/img/interface/default/" . $icon;
        }

        if(in_array($type, [1, 2])){
            $imgDisplay=$fullFilePath;
            $acceptOptions='image/*';
            
        }else{
            $imgDisplay=$iconDisplay;
            $acceptOptions='image/*, application/pdf';
        }


   //---------------------------------------------------------  
   // FORM CONTENT
   //---------------------------------------------------------

   if(in_array($type, [1, 2, 3])){

        if ($currentFile){
            $currentFileContent='
                <div class="currentImage">
                    <img src="'.htmlspecialchars($imgDisplay).'" id="img_update_'.$uid.'" alt="" style="width:110px" />
                </div>
                <div style="text-align:right;"><button type="button" class="btn-danger resetImageBtn_'.$uid.'" style="margin-top:10px;visibility:visible;">Supprimer</button></div>
            ';
        }else{
            $currentFileContent='
                <div class="currentImage">
                    <img src="'.$imgDefaultSquare.'" id="img_update_'.$uid.'" alt="" style="width:110px" />
                </div>
                <div><button type="button" class="btn-danger resetImageBtn_'.$uid.'" style="margin-top:10px;visibility:hidden;">Supprimer</button></div>
            ';
        }

   }else{

        $currentFileContent='
            <div style="text-align:right;"><button type="button" class="btn-danger resetImageBtn_'.$uid.'" style="margin-top:10px;display:none;">Supprimer</button></div>
        ';

   } 


    $formContent='';
    $formContent.='
        <!-- HTML -->
        <div id="'.$uid.'" style="margin-bottom:3px;position:static;">
        <table width="100%" style="border:0px;" cellspacing="0" cellpadding="0">
            <tbody>
            '.(($label!='')? '<tr><td colspan="2"><div style="padding:10px;background-color:#FFFFFF;width:100%;box-sizing: border-box;"><b>'.$label.'</b></div><div>&nbsp;</div></td></tr><tr><td colspan="2">&nbsp;</td></tr>' : '').'
                <tr>
                    <td style="text-align:left;">
                         <input type="file" id="fileInput_'.$uid.'" accept="'.$acceptOptions.'" style="display:none;">
                            <button type="button" id="browse_'.$uid.'" class="btn btn-upload">'.((in_array($type, [1, 2]))? 'Choisir une image' : 'T&eacute;l&eacuteverser un fichier').'</button>

                       <div class="progress" style="display:none">
                            <span class="percent">0%</span>
                        </div>
                    </td>
                    <td style="text-align:right;">'.$currentFileContent.'</td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </tbody>
        </table>
        </div>
        <!-- /HTML -->
    ';


   //---------------------------------------------------------  
   // JS CONTENT
   //---------------------------------------------------------

        $formContent.='
        <script>
            window.__uploaders = window.__uploaders || [];
            window.__uploaders.push({
                uid: "'.$uid.'",
                entity: "'.$entity.'",
                id: '.$id.',
                field: "'.$field.'",
                target: "'.$target.'",
                idParent: '.$idParent.',
                folder: "'.$folder.'",
                defaultImg: "'.$imgDefaultSquare.'",
                type: "'.$type.'",
                idSite: "'.$idSite.'",
            });
        </script>
        ';


    //---------------------------------------------------------
    // RESPONSE
    //---------------------------------------------------------

		$formContent=mb_convert_encoding((isset($formContent))? $formContent : '', 'UTF-8');

		$response = [
			'formContent' => $formContent
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;