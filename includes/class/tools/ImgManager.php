<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */

    //---------------------------------------------------------  
	// CLASS FRONT PHOTO MANAGER
	//--------------------------------------------------------- 

    namespace Tools;
    use PDO;
    use InvalidArgumentException;
    use Tools\ImageTools;

    class ImgManager {
        protected PDO $pdo;
        protected array $opts = [
            'table_user' => 'usr_user'
        ];
        private ImageTools $imgTools;

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

            public function __construct(PDO $pdo, array $options = []){

                $this->pdo = $pdo;
                $this->opts = array_merge($this->opts, $options);

                $this->opts['table_user'] = $this->validateTableName($this->opts['table_user']);

            }


        //---------------------------------------------------------
        // IMAGES FONCTIONS
        //---------------------------------------------------------

            public function imgProfileSquare($imgfile,$rep,$filename,$thumbwidth,$thumbheight,$cropwidth,$cropheight,$color,$quality): void{
		
                $imginfo=getimagesize($imgfile);
                $width=$imginfo[0];
                $height=$imginfo[1];
                $type=$imginfo[2];
                
                // GIF : 1
                // JPG : 2
                // PNG : 3
                
                $img = new ImageTools($imgfile);
                if($width>$height){
                    $img->resizeWidth($thumbwidth);
                    $img->resizeNewByHeight($thumbwidth,$thumbheight,$cropheight,$color);
                } else if($height>$width){
                    $img->resizeHeight($thumbheight);
                    $img->resizeNewByHeight($thumbwidth,$thumbheight,$cropheight,$color);
                } else if($height==$width){
                    $img->resizeHeight($thumbheight);
                    $img->resizeNewByHeight($thumbwidth,$thumbheight,$cropheight,$color);
                }
                if($type==1){
                    $ext=".gif";
                    $img->setOutputType(ImageTools::IMAGE_TYPE_GIF);
                    $img->save($rep, $filename, $quality, true);
                } else if($type==2){
                    $ext=".jpg";
                    $img->setOutputType(ImageTools::IMAGE_TYPE_JPG);
                    $img->save($rep, $filename, $quality, true);
                } if($type==3){
                    $ext=".png";
                    $img->setTransparentBg(); 
                    $img->setOutputType(ImageTools::IMAGE_TYPE_PNG);
                    $img->save($rep, $filename, $quality, true);
                }
                //$img->showImage();
                $img->destroy(); 
            
            }


        //---------------------------------------------------------
        // Creation thumb ptofil
        //---------------------------------------------------------
		
            public function imgProfile($imgfile,$rep,$filename,$thumbwidth,$thumbheight,$cropwidth,$cropheight,$color,$quality): void{
                
                $imginfo=getimagesize($imgfile);
                $width=$imginfo[0];
                $height=$imginfo[1];
                $type=$imginfo[2];
                
                // GIF : 1
                // JPG : 2
                // PNG : 3
                
                $img = new ImageTools($imgfile);
                if($width>$height){
                    $img->resizeWidth($thumbwidth);
                    $img->resizeNewByHeight($thumbwidth,$thumbheight,$cropheight,$color);
                } else if($height>$width){
                    $img->resizeHeight($thumbheight);
                    $img->resizeNewByWidth($thumbwidth,$thumbheight,$cropwidth,$color);
                } else if($height==$width){
                    $img->resizeHeight($thumbheight);
                    $img->resizeNewByHeight($thumbwidth,$thumbheight,$cropheight,$color);
                }
                if($type==1){
                    $ext=".gif";
                    $img->setOutputType(ImageTools::IMAGE_TYPE_GIF);
                    $img->save($rep, $filename, $quality, true);
                } else if($type==2){
                    $ext=".jpg";
                    $img->setOutputType(ImageTools::IMAGE_TYPE_JPG);
                    $img->save($rep, $filename, $quality, true);
                } if($type==3){
                    $ext=".png";
                    $img->setTransparentBg();
                    $img->setOutputType(ImageTools::IMAGE_TYPE_PNG);
                    $img->save($rep, $filename, $quality, true);
                }
                //$img->showImage();
                $img->destroy(); 
            
            }
	
	
            public function imgRectangle( $imgfile, $rep, $filename, $thumbwidth,$thumbheight,$quality ): void {

                $imginfo = getimagesize($imgfile);
                $type    = $imginfo[2];

                $img = new \Tools\ImageTools($imgfile);

                $origW = $img->getX();
                $origH = $img->getY();

                $ratioOrig  = $origW / $origH;
                $ratioThumb = $thumbwidth / $thumbheight;

                // 1️⃣ Resize pour couvrir
                if ($ratioOrig > $ratioThumb) {
                    // Image plus large → on ajuste à la hauteur
                    $img->resizeHeight($thumbheight);
                } else {
                    // Image plus haute (portrait) → on ajuste à la largeur
                    $img->resizeWidth($thumbwidth);
                }

                // 2️⃣ Calcul du crop centré
                $newW = $img->getX();
                $newH = $img->getY();

                $x = max(0, (int)(($newW - $thumbwidth) / 2));
                $y = max(0, (int)(($newH - $thumbheight) / 2));

                $img->cropImage($x, $y, $thumbwidth, $thumbheight);

                // 3️⃣ Type de sortie
                switch ($type) {
                    case IMAGETYPE_GIF:
                        $img->setOutputType(\Tools\ImageTools::IMAGE_TYPE_GIF);
                        break;

                    case IMAGETYPE_PNG:
                        $img->setOutputType(\Tools\ImageTools::IMAGE_TYPE_PNG);
                        break;

                    default:
                        $img->setOutputType(\Tools\ImageTools::IMAGE_TYPE_JPG);
                        break;
                }

                $img->save($rep, $filename, $quality, true);
                $img->destroy();
            }

	
	
            public function imgResizeWidth($rep,$filename,$widthsize): void{
                
                $imginfo=getimagesize($rep.$filename);
                $type=$imginfo[2];
                
                // GIF : 1
                // JPG : 2
                // PNG : 3
                
                $img = new ImageTools($rep.$filename);
                
                // Resize image by specified width
                $img->resizeWidth($widthsize); // new width
                
                if($type==1){
                    $ext=".gif";
                    $img->setOutputType(ImageTools::IMAGE_TYPE_GIF);
                    $img->save($rep, $filename, "80", true);
                } else if($type==2){
                    $ext=".jpg";
                    $img->setOutputType(ImageTools::IMAGE_TYPE_JPG);
                    $img->save($rep, $filename, "80", true);
                } if($type==3){
                    $ext=".png";
                    $img->setOutputType(ImageTools::IMAGE_TYPE_PNG);
                    $img->save($rep, $filename, "80", true);
                }
                    
            }
	
	
	
            public function rotateRight($imgfile,$rep): ?bool{
                
                $imginfo=getimagesize($rep.$imgfile);
                $type=$imginfo[2];
                
                $img = new ImageTools($rep.$imgfile);

                $img->rotateLeft(); // new width
                
                if($type==1){
                    $ext=".gif";
                    $img->setOutputType(ImageTools::IMAGE_TYPE_GIF);
                    $img->save($rep, $imgfile, "100", true);
                } else if($type==2){
                    $ext=".jpg";
                    $img->setOutputType(ImageTools::IMAGE_TYPE_JPG);
                    $img->save($rep, $imgfile, "100", true);
                } if($type==3){
                    $ext=".png";
                    $img->setOutputType(ImageTools::IMAGE_TYPE_PNG);
                    $img->save($rep, $imgfile, "100", true);
                }
                //$img->showImage();
                $img->destroy(); 
                
                return true;
            
            }
	
            public function rotateLeft($imgfile,$rep){
                
                $imginfo=getimagesize($rep.$imgfile);
                $type=$imginfo[2];
                
                $img = new ImageTools($rep.$imgfile);

                $img->rotateRight(); // new width
                
                if($type==1){
                    $ext=".gif";
                    $img->setOutputType(ImageTools::IMAGE_TYPE_GIF);
                    $img->save($rep, $imgfile, "100", true);
                } else if($type==2){
                    $ext=".jpg";
                    $img->setOutputType(ImageTools::IMAGE_TYPE_JPG);
                    $img->save($rep, $imgfile, "100", true);
                } if($type==3){
                    $ext=".png";
                    $img->setOutputType(ImageTools::IMAGE_TYPE_PNG);
                    $img->save($rep, $imgfile, "100", true);
                }
                //$img->showImage();
                $img->destroy(); 
                
                return true;
            
            }
	
	
            public function image_fix_orientation($path): void{
                $image = imagecreatefromjpeg($path);
                $exif = exif_read_data($path);
            
                if (!empty($exif['Orientation'])) {
                    switch ($exif['Orientation']) {
                        case 3:
                            $image = imagerotate($image, 180, 0);
                            break;
                        case 6:
                            $image = imagerotate($image, -90, 0);
                            break;
                        case 8:
                            $image = imagerotate($image, 90, 0);
                            break;
                    }
                    imagejpeg($image, $path);
                }
            }
	


        //---------------------------------------------------------  
        // IMG Base profil
        //--------------------------------------------------------- 
	
            public function createImageUser(string $initiales, int $userId, string $localhost): ?string{
                
                $fontname = $localhost.'/fonts/hurmegeometricsans1_semibold-webfont.ttf';
                $file = $localhost."/img/user/avatar/USER_".$userId.".jpg";
                $fileThumb = $localhost."/img/user/avatar_thumb/USER_".$userId.".jpg";
                $path_thumb=$localhost."/img/user/avatar_thumb/";
                $fileName="USER_".$userId.".jpg";
                
                $quality = 100;	
                $lenghtTxt=strlen($initiales);
                if($lenghtTxt==1){
                    $fontSize=250;
                    $i=0;
                }else if($lenghtTxt==2){
                    $fontSize=250;
                    $i=0;
                }else if($lenghtTxt==3){
                    $fontSize=160;
                    $i=-35;
                }else if($lenghtTxt==4){
                    $fontSize=160;
                    $i=-35;
                }else if($lenghtTxt==5){
                    $fontSize=160;
                    $i=-35;
                }else{
                    $fontSize=80;
                    $i=-60;
                }

                $im = imagecreatefromjpeg($localhost."/img/user/baseUser.jpg");
                    
                $color['white'] = imagecolorallocate($im, 255, 255, 255);
                    
                $y = imagesy($im) - 200;
                            
                $x = $this->center_text($initiales, $fontSize, $localhost);
                imagettftext($im, $fontSize, 0, $x, $y+$i, $color['white'], $fontname, $initiales);

                imagejpeg($im, $file, $quality);
                
                copy($file,$fileThumb);
                        
                $this->imgProfile($fileThumb, $path_thumb, $fileName, 45, 45, 45, 45, "#FFFFFF", 100);
                
                $reqimgh = "UPDATE ".$this->opts['table_user']." SET photo=:photo WHERE id=:userId";
                $resimgh = $this->pdo->prepare($reqimgh);
                $resimgh->bindValue(':userId', $userId, PDO::PARAM_STR);
                $resimgh->bindValue(':photo', $fileName, PDO::PARAM_STR);
                $resimgh->execute();
                                            
                return $file?:null;	
            }


            function center_text(string $initiales, int $fontSize, string $localhost): ?int{
                $fontName = $localhost.'/fonts/hurmegeometricsans1_semibold-webfont.ttf';
                $imageWidth = 600;
                $dimensions = imagettfbbox($fontSize, 0, $fontName, $initiales);
                return ceil(($imageWidth - $dimensions[4]) / 2)?:0;				
            }



    }