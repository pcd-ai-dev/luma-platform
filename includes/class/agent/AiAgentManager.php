<?php declare(strict_types=1);

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */

    //---------------------------------------------------------  
	// CLASS AI AGENT MANAGER
	//--------------------------------------------------------- 

        namespace Agent;

        class AiAgentManager{
            public function __construct(
                private OpenAiClientManager $client,
                private AiMemoryManager $memory,
                private AiKnowledgeManager $knowledge
            ) {}

            /**
             * Point d'entrée principal
             */
            public function handle(
                string $sessionId,
                string $message,
                string $system,
                string $provider
            ): string {

                // -----------------------------------------
                // SAVE USER MESSAGE
                // -----------------------------------------

                    $this->memory->save(
                        sessionId: $sessionId,
                        role: 'user',
                        message: $message
                    );

                // -----------------------------------------
                // LOAD CHAT HISTORY
                // -----------------------------------------

                    $history = $this->memory->load(
                        sessionId: $sessionId,
                        limit: 15
                    );

                // -----------------------------------------
                // LOAD KNOWLEDGE (RAG)
                // -----------------------------------------

                    $knowledge = $this->knowledge->search($message);

                // -----------------------------------------
                // BUILD PROMPT
                // -----------------------------------------

                    $messages = $this->buildPayload(
                        history: $history,
                        knowledge: $knowledge,
                        message: $message,
                        provider : $provider
                    );
                
                // -----------------------------------------
                // OPENAI CALL
                // -----------------------------------------

                    $response = $this->client->chat($messages, $provider);

                // -----------------------------------------
                // SAVE AI RESPONSE
                // -----------------------------------------

                $this->memory->save(
                    sessionId: $sessionId,
                    role: 'assistant',
                    message: $response
                );

                return $response;
            }

            /**
             * Construction des messages OpenAI
             */
           private function buildPayload(
                array $history,
                string $knowledge,
                string $message,
                string $provider = 'anthropic'
            ): array {
                $messages = [];

                if ($provider === 'openai') {
                    $messages[] = ['role' => 'system', 'content' => $this->systemPrompt($knowledge)];
                }

                foreach ($history as $row) {
                    $messages[] = ['role' => $row['role'], 'content' => $row['message']];
                }

                $messages[] = ['role' => 'user', 'content' => $message];

                $payload = [
                    "model"       => $provider === 'anthropic' ? "claude-sonnet-4-6" : "gpt-4.1-mini",
                    "max_tokens"  => $provider === 'anthropic' ? 400 : 600,
                    "temperature" => $provider === 'anthropic' ? 0.4 : 0.7,
                    "messages"    => $messages
                ];

                if ($provider === 'anthropic') {
                    $payload['system'] = $this->systemPrompt($knowledge);
                }

                return $payload;  // payload COMPLET
            }


            /**
             * Prompt système principal
             */
            private function systemPrompt(string $knowledge): string {
                return "
                    Documentation CMS :
                    $knowledge
                ";
            }
        }