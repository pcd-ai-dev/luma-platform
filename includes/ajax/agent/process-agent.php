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
        use Agent\OpenAiClientManager;
        use Agent\AiMemoryManager;
        use Agent\AiAgentManager;
        use Agent\AiKnowledgeManager;
        
        

     //---------------------------------------------------------  
     // CONNEXIONS
     //--------------------------------------------------------- 

		include($_SERVER['DOCUMENT_ROOT'].'/config.php');


     //---------------------------------------------------------
     // INIT CLASS SECURE
     //---------------------------------------------------------

        $secure = new SecureManager(requirePost: true, requireCsrf: false);


     //---------------------------------------------------------  
   	// VARIABLES
   	//---------------------------------------------------------

        $message = trim($secure->v('string', 'message', false) ?? '');
        $provider = $secure->v('string', 'provider', false) ?? 'anthropic';
        $sessionId = session_id();
        if($provider=='anthropic'){$apiKey=ANTHROPIC_KEY;}else{$apiKey=OPENAI_KEY;}
        $adminId = $secure->session('adminData.id', 'int', false) ?? 0;
        //$sessionId = $secure->session('adminData.sid', 'string', false) ?? '';


   //---------------------------------------------------------
   // INIT CLASS
   //---------------------------------------------------------

        $client = new OpenAiClientManager($apiKey);
        $memory = new AiMemoryManager($db);
        $knowledge = new AiKnowledgeManager($db);
        $agent  = new AiAgentManager($client, $memory, $knowledge);


   //---------------------------------------------------------
   // SYSTEM INIT
   //---------------------------------------------------------

        $system = "Tu es un assistant spécialisé UNIQUEMENT sur le backoffice du CMS.

                    Tu aides les administrateurs à :
                    - gérer les pages
                    - utiliser GrapesJS sans jamais en mentionner le nom
                    - Le assetManager de GrapesJS est CKFinder
                    - gérer le SEO
                    - modifier les contenus
                    - utiliser les fonctionnalités du backoffice

                    Tu ne réponds JAMAIS :
                    - aux questions générales
                    - à l'actualité
                    - à la culture générale
                    - au code non lié au CMS
                    - aux sujets hors administration du site

                    Si la question n'est pas liée au backoffice :
                    réponds UNIQUEMENT :

                    'Je suis uniquement spécialisé sur le backoffice.'

                    Tu dois répondre :
                    - de manière concise
                    - pédagogique
                    - orientée utilisateur
                    - avec étapes simples
                    - en ne citant pas GrapesJS et CKFinder";


    //---------------------------------------------------------  
   	// PROMPT CONSTRUCTION
   	//---------------------------------------------------------

        if (!$message) {
            echo json_encode(['reply' => 'Message vide']);
            exit;
        }


	//---------------------------------------------------------
  	// RESPONSES
   	//---------------------------------------------------------

        $reply = $agent->handle($sessionId, $message, $system, $provider);

        echo json_encode([
            "reply" => $reply
        ]);