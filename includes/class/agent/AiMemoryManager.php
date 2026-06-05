<?php declare(strict_types=1);

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */

    //---------------------------------------------------------  
	// CLASS AI MEMORY MANAGER
	//--------------------------------------------------------- 

        namespace Agent;

        use PDO;

        class AiMemoryManager{
            public function __construct(
                private PDO $pdo
            ) {}

            // ---------------------------------------------------------
            // SAVE MESSAGE
            // ---------------------------------------------------------   

                public function save(string $sessionId, string $role, string $message): void {
                    $stmt = $this->pdo->prepare("
                        INSERT INTO ai_chat_messages (session_id, role, message)
                        VALUES (:session_id, :role, :message)
                    ");

                    $stmt->execute([
                        'session_id' => $sessionId,
                        'role' => $role,
                        'message' => $message
                    ]);
                }


            // ---------------------------------------------------------
            // LOAD MESSAGES
            // ---------------------------------------------------------   

                public function load(string $sessionId, int $limit = 20): array {
                    $stmt = $this->pdo->prepare("
                        SELECT role, message
                        FROM ai_chat_messages
                        WHERE session_id = :session_id
                        ORDER BY id DESC
                        LIMIT $limit
                    ");

                    $stmt->execute(['session_id' => $sessionId]);

                    return array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));
                }
                
        }