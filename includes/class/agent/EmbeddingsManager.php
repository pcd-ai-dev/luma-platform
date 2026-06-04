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
    use Exception;

    class EmbeddingsManager{
        
        private string $apiKey;
        private string $model = 'text-embedding-3-small'; // 1536 dimensions, le plus économique

        public function __construct(string $apiKey)
        {
            $this->apiKey = $apiKey;
        }

        // ─────────────────────────────────────────
        // Génère le vecteur d'un texte
        // Retourne un tableau de 1536 floats
        // ─────────────────────────────────────────
        public function generer(string $texte): array {

            // Nettoyage : retire les sauts de ligne multiples (dégradent les embeddings)
            $texte = preg_replace('/\s+/', ' ', trim($texte));

            // Tronque si trop long (max ~8000 tokens pour ce modèle)
            if (strlen($texte) > 25000) {
                $texte = substr($texte, 0, 25000);
            }

            $payload = json_encode([
                'model' => $this->model,
                'input' => $texte
            ]);

            $ch = curl_init('https://api.openai.com/v1/embeddings');
            curl_setopt($ch, CURLOPT_POST,           true);
            curl_setopt($ch, CURLOPT_POSTFIELDS,     $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER,     [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT,        15);

            $raw  = curl_exec($ch);
            $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $err  = curl_error($ch);
            curl_close($ch);

            if ($err) throw new Exception('Erreur réseau embeddings : ' . $err);
            if ($http !== 200) {
                $api = json_decode($raw, true);
                throw new Exception('Erreur API embeddings : ' . ($api['error']['message'] ?? 'HTTP ' . $http));
            }

            $result = json_decode($raw, true);
            return $result['data'][0]['embedding'];  // tableau de 1536 floats
        }

        // ─────────────────────────────────────────
        // Similarité cosinus entre deux vecteurs
        // Retourne un score entre 0 (rien en commun) et 1 (identiques)
        // ─────────────────────────────────────────
        public static function similarite(array $a, array $b): float {
            $dot   = 0.0;
            $normA = 0.0;
            $normB = 0.0;

            $n = min(count($a), count($b));
            for ($i = 0; $i < $n; $i++) {
                $dot   += $a[$i] * $b[$i];
                $normA += $a[$i] * $a[$i];
                $normB += $b[$i] * $b[$i];
            }

            if ($normA == 0 || $normB == 0) return 0.0;

            return $dot / (sqrt($normA) * sqrt($normB));
        }

        // ─────────────────────────────────────────
        // Encode/décode pour le stockage MySQL
        // ─────────────────────────────────────────

        public static function encoder(array $vecteur): string
        {
            return json_encode($vecteur);
        }

        public static function decoder(string $json): array
        {
            return json_decode($json, true) ?? [];
        }
    }