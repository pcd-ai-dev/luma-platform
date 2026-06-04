<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */

	//---------------------------------------------------------  
	// NEWS PROCESS
	//--------------------------------------------------------- 

	//---------------------------------------------------------  
	// INIT
	//--------------------------------------------------------- 

		unset($_SESSION['offsetPost']);
		$_SESSION['offsetPost']=10;

		unset($_SESSION['idCatPost']);
		$_SESSION['idCatPost']=$idCategory;

	//---------------------------------------------------------  
	// CONSTRUCTION
	//--------------------------------------------------------- 

	//---------------------------------------------------------  
	// GALLERY PHOTOS
	//--------------------------------------------------------- 

		$postPhotos="";
		$postPhotos.='<div id="photoContainer">';

		$postPhotos.= '<div class="grid-sizer"></div>';

		for ( $i = 0; $i < $nbi; $i++ ) {

			$videoshow=(isset($tabi[$i]['video']))?$tabi[$i]['video']: '';

			if(strlen($videoshow)>20){

                $videoPost="";
            
                if(empty($videoshow)){}else{
					if(!empty($tabi[$i]['img'])){
						$urlPoster='/img/post/'.$tabi[$i]['idCategory'].'/gallery/images/'.$tabi[$i]['img'];
					}else{
						$urlPoster="";
					}
                    $videoPost.= '<video controls width="100%" poster="'.$urlPoster.'">';
                    $videoPost.= '<source src="'.$videoshow.'" type="video/mp4" />';
                    $videoPost.= '</video>';
                }
    
            }else{
				if(is_numeric($videoshow)){
					$videoLinkcolorBox = "https://player.vimeo.com/video/" . $videoshow;
				}else{
					$videoLinkcolorBox = "https://www.youtube.com/embed/" . $videoshow;
				}
			}

			if(empty($videoshow)){
				$postPhotos.= '<div class="item itemVideo">';
				$postPhotos.='<a href="/img/post/' . $tabi[ $i ][ 'idCategory' ] . '/gallery/images/' . $tabi[ $i ][ 'img' ] . '" data-fancybox="gallery" class="fancybox">';
				$postPhotos.='<img src="/img/post/' . $tabi[ $i ][ 'idCategory' ] . '/gallery/images/' . $tabi[ $i ][ 'img' ] . '" width="100%" alt="'.$siteAlt.' '.$rpost->title.'" />';
				$postPhotos.='</a>';
				$postPhotos.= '</div>';
			}else{
				$postPhotos.= '<div class="item itemVideo">';
				
				if(strlen($videoshow)>20){
                    $postPhotos.= $videoPost;
                }else{
                    $postPhotos.= '<iframe border="0" class="videoEmbeded" src="'.$videoLinkcolorBox.'" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>';
                }

				$postPhotos.= '</div>';
			}
		 }

		$postPhotos.='</div>';




	//---------------------------------------------------------  
	// SCHEMAS PROCESS
	//--------------------------------------------------------- 

	echo '<script type="application/ld+json">'; 
	
	echo '
		{
			"@context": "https://schema.org",
			"@graph": [
				{
					"@type": [
						"NewsMediaOrganization",
						"Organization"
					],
					"@id": "'.$siteHost.'/#organization",
					"name": "'.$siteName.'",
					"url": "'.$siteHost.'",
					"logo": {
						"@type": "ImageObject",
						"@id": "'.$siteHost.'/#logo",
						"url": "'.$defaultLogoSquare.'",
						"contentUrl": "'.$defaultLogoSquare.'",
						"caption": "'.$siteName.'",
						"inLanguage": "fr-FR",
						"width": "600",
						"height": "600"
					}
				},
				{
					"@type": "WebSite",
					"@id": "'.$siteHost.'/#website",
					"url": "'.$siteHost.'",
					"name": "'.$siteName.'",
					"alternateName": "'.$siteName.'",
					"publisher": {
						"@id": "'.$siteHost.'/#organization"
					},
					"inLanguage": "fr-FR"
				},
				{
					"@type": "ImageObject",
					"@id": "'.$siteHost.$imgLink.'",
					"url": "'.$siteHost.$imgLink.'",
					"width": "'.$width.'",
					"height": "'.$height.'",
					"inLanguage": "fr-FR"
				},
				{
					"@type": "BreadcrumbList",
					"@id": "'.$urlSSL.'#breadcrumb",
					"itemListElement": [
						{
							"@type": "ListItem",
							"position": "1",
							"item": {
								"@id": "'.$siteHost.'",
								"name": "Accueil"
							}
						},
						{
							"@type": "ListItem",
							"position": "2",
							"item": {
								"@id": "'.$siteHost.'/'.$is->newsPage.'-what-s-up.html",
								"name": "What\'s up !"
							}
						},
						{
							"@type": "ListItem",
							"position": "3",
							"item": {
								"@id": "'.$urlSSL.'",
								"name": "'.$rpost->title ?? ''.'"
							}
						}
					]
				},	
				{
					"@type": "WebPage",
					"@id": "'.$urlSSL.'#webpage",
					"url": "'.$urlSSL.'",
					"name": "'.$rpost->title.'",
					"datePublished": "'.$datePost->format('Y-m-d').'T'.$datePost->format('H:i:s').'+02:00'.'",
					"dateModified": "'.$datePostModified->format('Y-m-d').'T'.$datePostModified->format('H:i:s').'+02:00'.'",
					"isPartOf": {
						"@id": "'.$siteHost.'/#website"
					},
					"primaryImageOfPage": {
						"@id": "'.$siteHost.''.$imgLink.'"
					},
					"inLanguage": "fr-FR",
					"breadcrumb": {
						"@id": "'.$urlSSL.'#breadcrumb"
					}
				},
				{
					"@type": "Person",
					"@id": "'.$siteHost.'/3-groupe.html",
					"name": "'.$siteName.'",
					"description": "Restez informés des dernières actualités de The Maze. Ne manquez aucune annonce, sortie d\'album, ou informations sur nos prochains concerts.",
					"url": "'.$siteHost.'/3-groupe.html",
					"image": {
						"@type": "ImageObject",
						"@id": "'.$defaultLogoSquare.'",
						"url": "'.$defaultLogoSquare.'",
						"caption": "'.$siteName.'",
						"inLanguage": "fr-FR"
					},
					"worksFor": {
						"@id": "'.$siteHost.'/#organization"
					}
				},
				{
					"headline": "'.$rpost->title.'",
					"description": "'.strip_tags($rpost->shortText ?? '').'",
					"datePublished": "'.$datePost->format('Y-m-d').'T'.$datePost->format('H:i:s').'+02:00'.'",
					"dateModified": "'.$datePostModified->format('Y-m-d').'T'.$datePostModified->format('H:i:s').'+02:00'.'",
					"keywords": "A definir",
					"image": {
						"@id": "'.$siteHost.''.$imgLink.'"
					},
					"author": {
						"@id": "'.$siteHost.'/3-groupe.html",
						"name": "'.$siteName.'"
					},
					"@type": "BlogPosting",
					"name": "'.$rpost->title ?? ''.'",
					"articleSection": "News, Rock, punk",
					"@id": "'.$urlSSL.'/#schema-'.$idNews.'",
					"isPartOf": {
						"@id": "'.$urlSSL.'/#webpage"
					},
					"publisher": {
						"@id": "'.$siteHost.'/#organization"
					},
					"inLanguage": "fr-FR",
					"mainEntityOfPage": {
						"@id": "'.$urlSSL.'#webpage"
					}
				}';
			
			if(!empty($rp->video)){

				if(strlen($rp->video)>20){
					$embededUrl=''.$siteHost.''.$rp->video;
				}else{
					$embededUrl='https://www.youtube.com/embed/'.$rp->video;
				}

				echo '
					,{
						"@type": "VideoObject",
						"name": "'.$rpost->title ?? ''.'",
						"description": "'.strip_tags($rpost->shortText ?? '').'",
						"uploadDate": "'.$datePostModified->format('Y-m-d').'T'.$datePostModified->format('H:i:s').'+02:00'.'",
						"thumbnailUrl": [
							"'.$siteHost.''.$imgLink.'"
						],
						"embedUrl": "'.$urlSSL.'",
						"contentUrl": "'.$embededUrl.'",
						"duration": "PT4M19S",
						"width": 1280,
						"height": 720,
						"isFamilyFriendly": true,
						"publisher": {
							"@id": "'.$siteHost.'/#organization"
						},
						"inLanguage": "fr-FR",
						"potentialAction": {
							"@type": "WatchAction",
							"target": "'.$urlSSL.'"
						}
						}

					';

			}


			if(!empty($rp->audio)){
				echo '
					,{
						"@type": "AudioObject",
						"contentUrl": "'.$siteHost.'/tmp/data/adminFiles/THEMAZE/mp3/'.$rp->audio.'",
						"description": "'.$rpost->title ?? ''.'",
						"duration": "T0M15S",
						"encodingFormat": "mp3",
						"name": "'.$rp->audio.'"
					}
				';

			}


			if(!empty($rp->place)){
				echo '
				,{
					"@context": "https://schema.org",
					"@type": "Event",
					"name": "'.$rpost->title ?? ''.'",
	
					"startDate": "'.$dateStart->format('Y-m-d').'T'.$dateStart->format('H:i:s').'+02:00'.'",
					"endDate": "'.$dateEnd->format('Y-m-d').'T'.$dateEnd->format('H:i:s').'+02:00'.'",
					"eventAttendanceMode": "https://schema.org/OfflineEventAttendanceMode",
					"eventStatus": "https://schema.org/EventScheduled",
					"location": {
						"@type": "Place",
						"name": "'.mb_convert_encoding($rp->place, 'UTF-8', 'ISO-8859-1').'",
						"address": {
						"@type": "PostalAddress",
						"streetAddress": "'.mb_convert_encoding($rp->address, 'UTF-8', 'ISO-8859-1').'",
						"addressLocality": "'.mb_convert_encoding($rp->city, 'UTF-8', 'ISO-8859-1').'",
						"postalCode": "'.$rp->cp.'",
						"addressRegion": "IDF",
						"addressCountry": "FR"
						}
					},
					"image": [
						"'.$defaultLogoSquare.'",
						"'.$siteHost.''.$imgLink.'"
					],
					"description": "'.strip_tags($rpost->shortText ?? '').'",
					"offers": {
						"@type": "Offer",
						"url": "'.$urlSSL.'",
						"price": "'.$rp->price.'",
						"priceCurrency": "EUR",
						"availability": "https://schema.org/InStock",
						"validFrom": "'.$dateEnd->format('Y-m-d').'T'.$dateEnd->format('H:i:s').'+02:00'.'"
					},
					"performer": {
						"@type": "PerformingGroup",
						"name": "'.$siteName.'"
					},
					"organizer": {
						"@type": "Organization",
						"name": "'.$siteName.'",
						"url": "'.$siteHost.'"
					}
					}';

			}

	echo '		
			]
		}
	';

 	echo '</script>'; 

	$textBuilder= $htmlText;

	$backLink=(isset($is->newsPage))? '/'.$varLinkData.'/'.$is->newsPage.'-blog.html' : '/'.$varLinkData.'/'.$is->homePage.'-blog.html';

	$textBuilder= str_replace("#newsTitle#", $rpost->title ?? '', $textBuilder);
	$textBuilder= str_replace("#dateshow#", $dateshow, $textBuilder);
	$textBuilder= str_replace("#newsContent#", $rpost->text, $textBuilder);
	$textBuilder= str_replace("#imgLink#", $imgLink, $textBuilder);
	$textBuilder= str_replace("#backLink#", $backLink, $textBuilder);
	$textBuilder= str_replace("#postPhotos#", $postPhotos, $textBuilder);