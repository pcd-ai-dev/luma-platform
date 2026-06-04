<?php declare(strict_types=1);

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */

    //---------------------------------------------------------  
	// CLASS AI OPEN CLIENT MANAGER
	//--------------------------------------------------------- 

    namespace Agent;

        class OpenAiClientManager {
            public function __construct(
                private string $apiKey
            ) {}

            public function chat(array $payload, string $provider): string{
                if ($provider === 'anthropic') {
                    $curlURL    = "https://api.anthropic.com/v1/messages";
                    $curlHeader = [
                        "Content-Type: application/json",
                        "x-api-key: " . $this->apiKey,
                        "anthropic-version: 2023-06-01"
                    ];
                } else {
                    $curlURL    = "https://api.openai.com/v1/chat/completions";
                    $curlHeader = [
                        "Content-Type: application/json",
                        "Authorization: Bearer " . $this->apiKey
                    ];
                }

                $ch = curl_init($curlURL);
                curl_setopt($ch, CURLOPT_POST,           true);
                curl_setopt($ch, CURLOPT_POSTFIELDS,     json_encode($payload, JSON_UNESCAPED_UNICODE));
                curl_setopt($ch, CURLOPT_HTTPHEADER,     $curlHeader);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT,        20);
                $res = curl_exec($ch);

                $data = json_decode($res, true);

                if ($provider === 'anthropic') {
                    return ($data['content'][0]['text'] ?? '') . ' | de Anthropic';
                } else {
                    return ($data['choices'][0]['message']['content'] ?? '') . ' | de Open AI';
                }
            }
        }