<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */

    //---------------------------------------------------------  
	// NAMESPACE
	//---------------------------------------------------------

        
        use Front\RootManager;
        use Front\PageManager;
        use Front\VideoManager;
        use Front\PostManager;
        use Front\ProductManager;
        use Front\PhotoManager;
        use App\AdminManager;
        use App\PlaceManager;
        use App\LocationManager;


    //---------------------------------------------------------  
	// INIT CLASS
	//---------------------------------------------------------

        return [
            'admin' => [
                'manager' => App\AdminManager::class,
                'prefix' => 'admin-',
            ],
            'root' => [
                'manager' => Front\RootManager::class,
                'prefix' => 'root-',
            ],
            'place' => [
                'manager' => App\PlaceManager::class,
                'prefix' => 'place-',
            ],
            'page' => [
                'manager' => Front\PageManager::class,
                'prefix' => 'root-',
            ],
            'lang' => [
                'manager' => Front\LangManager::class,
                'prefix' => 'root-',
            ],
            'video' => [
                'manager' => Front\VideoManager::class,
                'prefix' => 'video-',
            ],
            'post' => [
                'manager' => Front\PostManager::class,
                'prefix' => 'post_',
            ],
            'product' => [
                'manager' => Front\ProductManager::class,
                'prefix' => 'prod_',
            ],
            'photo' => [
                'manager' => Front\PhotoManager::class,
                'prefix' => 'img_',
            ],
            'mailing' => [
                'manager' => App\MailingManager::class,
                'prefix' => 'mailing_',
            ],
            'location' => [
                'manager' => Front\LocationManager::class,
                'prefix' => 'loc_',
            ],
            'learn' => [
                'manager' => App\LearnManager::class,
                'prefix' => 'learn_',
            ]
        ];