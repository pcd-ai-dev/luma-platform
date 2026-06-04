<?php session_start();


/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2024 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */


   //---------------------------------------------------------  
	// CONNEXION
	//--------------------------------------------------------- 

      include("config.inc.php");    
      
      

   //---------------------------------------------------------  
	// PROCESS
	//--------------------------------------------------------- 


      $imgtmp = imagecreatetruecolor($cryptwidth,$cryptheight);
      $blank  = imagecolorallocate($imgtmp,255,255,255);
      $black   = imagecolorallocate($imgtmp,0,0,0);
      imagefill($imgtmp,0,0,$blank);

      $word ='';
      $x = 10; 
      $pair = 0;
      $charnb = rand($charnbmin,$charnbmax);

      for ($i=1;$i<= $charnb;$i++) {              

         $tword[$i]['font'] =  $tfont[array_rand($tfont,1)];
         
         $tword[$i]['angle'] = (rand(1,2)==1)?rand(0,$charanglemax):rand(360-$charanglemax,360);
         
         if ($crypteasy) $tword[$i]['element'] =(!$pair)?$charelc[rand(0,strlen($charelc)-1)]:$charelv[rand(0,strlen($charelv)-1)];
            else $tword[$i]['element'] = $charel[rand(0,strlen($charel)-1)];

         $pair=!$pair;
         $tword[$i]['size'] = rand($charsizemin,$charsizemax);
         $tword[$i]['y'] = ($charup?($cryptheight/2)+rand(0,($cryptheight/5)):($cryptheight/1.5));
         $word .=$tword[$i]['element'];
         imagettftext($imgtmp,$tword[$i]['size'],$tword[$i]['angle'],$x,$tword[$i]['y'],$black,$tword[$i]['font'],$tword[$i]['element']);
         $x +=$charspace;
     } 


      $xbegin=0;
      for ($x=0;$x<$cryptwidth;$x++){
         for ($y=0;$y<$cryptheight;$y++) {
            if ((imagecolorat($imgtmp,$x,$y) != $blank) and ($xbegin==0)) $xbegin = $x;
        }
      }
    
      $xend=0;
      for ($x=$cryptwidth-1;$x>0;$x--){
         for ($y=0;$y<$cryptheight;$y++) {
            if ((imagecolorat($imgtmp,$x,$y) != $blank) and ($xend==0)) $xend = $x;
         }
      }

      $xvariation = round(($cryptwidth/2)-(($xend-$xbegin)/2));
      imagedestroy ($imgtmp);


      $img = imagecreatetruecolor($cryptwidth,$cryptheight); 

      if($bgimg){
         list($getwidth, $getheight, $gettype, $getattr) = getimagesize($bgimg);
         switch($gettype){
            case "1": $imgread = imagecreatefromgif($bgimg); break;
            case "2": $imgread = imagecreatefromjpeg($bgimg); break;
            case "3": $imgread = imagecreatefrompng($bgimg); break;
         }
         imagecopyresized ($img, $imgread, 0,0,0,0,$cryptwidth,$cryptheight,$getwidth,$getheight);

         imagedestroy ($imgread);

      }else{
         $bg = imagecolorallocate($img,$bgR,$bgG,$bgB);
         imagefill($img,0,0,$bg);
         if ($bgclear){imagecolortransparent($img,$bg);}
      }


      $ink = imagecolorallocatealpha($img,$charR,$charG,$charB,$charclear);
      $x = $xvariation;

      for($i=1;$i<= $charnb;$i++){       
         if($charcolorrnd){
         $ok = false;
            do{
               $rndR = rand(0,255); $rndG = rand(0,255); $rndB = rand(0,255);
               $rndcolor = $rndR+$rndG+$rndB;
               switch ($charcolorrndlevel) {
                  case 1  : if ($rndcolor<200) $ok=true; break;
                  case 2  : if ($rndcolor<400) $ok=true; break;
                  case 3  : if ($rndcolor>500) $ok=true; break;
                  case 4  : if ($rndcolor>650) $ok=true; break;
                  default : $ok=true;               
               }
            } while (!$ok);

            $rndink = imagecolorallocatealpha ($img,$rndR,$rndG,$rndB,$charclear);
         }  
      
         imagettftext($img,$tword[$i]['size'],$tword[$i]['angle'],$x,$tword[$i]['y'],$charcolorrnd?$rndink:$ink,$tword[$i]['font'],$tword[$i]['element']);
         $x +=$charspace;
      } 


      $noisecol = $noisecolorchar?$ink:$bg; 
      $nbpx = rand($noisepxmin,$noisepxmax);
      $nbline = rand($noiselinemin,$noiselinemax);

      for($i=1;$i<$nbpx;$i++){imagesetpixel ($img,rand(0,$cryptwidth-1),rand(0,$cryptheight-1),$noisecol);}
      for($i=1;$i<=$nbline;$i++){imageline($img,rand(0,$cryptwidth-1),rand(0,$cryptheight-1),rand(0,$cryptwidth-1),rand(0,$cryptheight-1),$noisecol);}


      if($bgframe){
         $framecol = imagecolorallocate($img,($bgR*3+$charR)/4,($bgG*3+$charG)/4,($bgB*3+$charB)/4);
         imagerectangle($img,0,0,$cryptwidth-1,$cryptheight-1,$framecol);
      }
            

      if ($cryptgrayscal){imagefilter ( $img,IMG_FILTER_GRAYSCALE);}
      if ($cryptgaussianblur){imagefilter ( $img,IMG_FILTER_GAUSSIAN_BLUR);}


      switch (strtoupper($cryptsecure)){    
         case "MD5"  : $_SESSION['cryptcode'] = md5($word); break;
         case "SHA1" : $_SESSION['cryptcode'] = sha1($word); break;
         default     : $_SESSION['cryptcode'] = $word; break;
      }

      $_SESSION['crypttime'] = time();
      $_SESSION['cryptcptuse']++;       
  

      switch (strtoupper($cryptformat)) {  
         case "JPG"  :
	      case "JPEG" : if(imagetypes() & IMG_JPG){
                           header("Content-type: image/jpeg");
                           imagejpeg($img, "", 80);
                        }
                        break;
	      case "GIF"  : if (imagetypes() & IMG_GIF){
                           header("Content-type: image/gif");
                           imagegif($img);
                        }
                        break;
	      case "PNG"  : 
	      default     : if (imagetypes() & IMG_PNG)  {
                           header("Content-type: image/png");
                           imagepng($img);
                        }
      }

      imagedestroy ($img);
      unset ($word,$tword);
      unset ($_SESSION['cryptreload']);