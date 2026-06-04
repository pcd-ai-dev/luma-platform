<?php declare(strict_types=1);

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */

    //---------------------------------------------------------  
	// CLASS AI KNOWLEDGE MANAGER
	//--------------------------------------------------------- 

        namespace Agent;

        use PDO;
        use Exception;

        class AiKnowledgeManager
        {
            public function __construct(
                private PDO        $pdo,
                private ?EmbeddingsManager $embeddings = null
            ) {}

            // ─────────────────────────────────────────────────────────
            // MÉTHODE PUBLIQUE — inchangée côté signature
            // Elle choisit automatiquement vecteurs ou FULLTEXT
            // ─────────────────────────────────────────────────────────
            public function search(string $query, int $limit = 5): string {
                $query = trim($query);
                if (empty($query)) return '';

                $rows = ($this->embeddings !== null)
                    ? $this->searchByVector($query, $limit)
                    : $this->searchByFulltext($query, $limit);

                if (empty($rows)) return '';

                return $this->buildContext($rows);
            }

            // ─────────────────────────────────────────────────────────
            // RECHERCHE VECTORIELLE
            // ─────────────────────────────────────────────────────────

            private function searchByVector(string $query, int $limit): array {
                $questionVecteur = $this->embeddings->generer($query);

                $stmt = $this->pdo->query(
                    "SELECT title, content, embedding
                    FROM ai_knowledge
                    WHERE embedding IS NOT NULL"
                );
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (empty($rows)) {
                    return $this->searchByFulltext($query, $limit);
                }

                // 3. Calcul de similarité cosinus pour chaque doc
                foreach ($rows as &$row) {
                    $docVecteur   = EmbeddingsManager::decoder($row['embedding']);
                    $row['score'] = EmbeddingsManager::similarite($questionVecteur, $docVecteur);
                    unset($row['embedding']); // plus utile après calcul
                }
                unset($row);

                // 4. Tri par score décroissant
                usort($rows, fn($a, $b) => $b['score'] <=> $a['score']);

                // 5. Top $limit résultats au-dessus du seuil de pertinence
                $top = array_slice($rows, 0, $limit);
                return array_values(
                    array_filter($top, fn($r) => $r['score'] >= 0.70)
                );
            }

            // ─────────────────────────────────────────────────────────
            // RECHERCHE FULLTEXT — ton code existant, inchangé
            // ─────────────────────────────────────────────────────────
            private function searchByFulltext(string $query, int $limit): array
            {
                $search = $this->sanitize($query);
                if (empty($search)) return [];

                $stmt = $this->pdo->prepare("
                    SELECT
                        title,
                        content,
                        MATCH(title, content)
                        AGAINST(:search_A IN NATURAL LANGUAGE MODE) AS score
                    FROM ai_knowledge
                    WHERE MATCH(title, content)
                    AGAINST(:search_B IN NATURAL LANGUAGE MODE)
                    ORDER BY score DESC
                    LIMIT {$limit}
                ");
                $stmt->execute(['search_A' => $search, 'search_B' => $search]);
                return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            }

            // ─────────────────────────────────────────────────────────
            // CONSTRUCTION DU CONTEXTE — ton format existant, inchangé
            // ─────────────────────────────────────────────────────────
            private function buildContext(array $rows): string
            {
                $context = '';
                foreach ($rows as $row) {
                    $context .= "
                        DOCUMENT: {$row['title']}
                        {$row['content']}
                        -----------------------------------
                    ";
                }
                return trim($context);
            }

            private function sanitize(string $query): string {
                $query = strip_tags($query);
                $query = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $query);
                $query = preg_replace('/\s+/', ' ', $query);
                return trim($query);
            }

        }