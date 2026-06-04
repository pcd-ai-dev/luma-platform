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


   //---------------------------------------------------------  
   // CONNEXIONS
   //--------------------------------------------------------- 

		include($_SERVER['DOCUMENT_ROOT'].'/config.php');


   //---------------------------------------------------------
   // INIT CLASS
   //---------------------------------------------------------

        $secure = new SecureManager(requirePost: true, requireCsrf: false);


    //---------------------------------------------------------  
   	// VARIABLES
   	//---------------------------------------------------------

        $prompt          = trim($secure->v('string', 'prompt', false) ?? '');
        $provider = $secure->v('string', 'provider', false) ?? 'anthropic';
        $contenu_actuel  = trim($secure->v('string', 'contenu_actuel', false) ?? '');

        if (empty($prompt)) {
            echo json_encode(['error' => 'Prompt manquant.']);
            exit;
        }


    //---------------------------------------------------------  
   	// PROMPT CONSTRUCTION
   	//---------------------------------------------------------

        $contexte_actuel = $contenu_actuel
            ? "\nLe composant contient actuellement ce texte (tu peux t'en inspirer ou le reformuler) :\n« $contenu_actuel »"
            : '';

        $system = "Tu es un copywriter expert, spécialisé dans la rédaction de contenu web.
        Tu génères uniquement le texte demandé, sans explication, sans guillemets, sans balise HTML avec des paragraphe pour éaré si nécessaire.
        Sois concis et percutant. Maximum 3-4 phrases sauf si on te demande plus.";

        $user = "$prompt$contexte_actuel";


    //---------------------------------------------------------  
   	// CONFIG CALL BACK
   	//---------------------------------------------------------

        if($provider=='anthropic'){
            //ANTHROPIC
            $curlURL = "https://api.anthropic.com/v1/messages";
            $payload = json_encode([
                "model"      => "claude-sonnet-4-6",
                "max_tokens" => 600,
                "system"     => $system,
                "messages"   => [[
                    "role"    => "user",
                    "content" => $user
                ]]
            ], JSON_UNESCAPED_UNICODE);

            $curlHeader=[
                "Content-Type: application/json",
                "x-api-key: " . ANTHROPIC_KEY,
                "anthropic-version: 2023-06-01"
            ];

        }else{
            // CHAT GPT
            $curlURL = "https://api.openai.com/v1/chat/completions";
            $payload = json_encode([
                "model" => "gpt-4.1-nano",
                "messages" => [
                    [
                        "role" => "system",
                        "content" => $system
                    ],
                    [
                        "role" => "user",
                        "content" => $user
                    ]
                ],
                "temperature" => 0.7,
                "max_tokens"  => 600
            ], JSON_UNESCAPED_UNICODE);

            $curlHeader=[
                "Content-Type: application/json",
                "Authorization: Bearer " . OPENAI_KEY
            ];

        }

       
    //---------------------------------------------------------  
   	// CALLBACK CLAUDE ANTROPIC
   	//---------------------------------------------------------

        $ch = curl_init($curlURL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeader);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        $raw      = curl_exec($ch);
        $http     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err      = curl_error($ch);


	//---------------------------------------------------------
  	// RESPONSES
   	//---------------------------------------------------------

        if ($err) {
            echo json_encode(['error' => 'Erreur réseau : ' . $err]);
            exit;
        }

        if ($http !== 200) {
            $api = json_decode($raw, true);
            echo json_encode(['error' => $api['error']['message'] ?? 'Erreur API ' . $http]);
            exit;
        }

        $result = json_decode($raw, true);

       if($provider=='anthropic'){
            //ANTHROPIC
            $texte = $result['content'][0]['text'].' | de Anthropic' ?? '';
        }else{
            //OPEN AI
            $texte = $result['choices'][0]['message']['content'].' | de Open AI' ?? '';
        }
 
		$response = [
			'texte' => $texte
		];

		echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
		exit;