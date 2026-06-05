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

            // ---------------------------------------------
            // AUTO SEARCH METHOD => FULLTEXT ? EMBEDDING
            // ---------------------------------------------

                public function search(string $query, int $limit = 5): string {
                    $query = trim($query);
                    if (empty($query)) return '';

                    $rows = ($this->embeddings !== null)
                        ? $this->searchByVector($query, $limit)
                        : $this->searchByFulltext($query, $limit);

                    if (empty($rows)) return '';

                    return $this->buildContext($rows);
                }

            // ---------------------------------------------------------
            // VECTOR SEARCHING
            // ---------------------------------------------------------

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

                    // CHECK VECTOR SIMILARITIES -> ASKVECTOR -> DOCVECTOR
                    foreach ($rows as &$row) {
                        $docVecteur   = EmbeddingsManager::decoder($row['embedding']);
                        $row['score'] = EmbeddingsManager::similarite($questionVecteur, $docVecteur);
                        unset($row['embedding']);
                    }
                    unset($row);

                    // SCORES SORT
                    usort($rows, fn($a, $b) => $b['score'] <=> $a['score']);

                    // DELETE SCORES UNDER 70%
                    $top = array_slice($rows, 0, $limit);
                    return array_values(
                        array_filter($top, fn($r) => $r['score'] >= 0.70)
                    );
                }

            // ---------------------------------------------------------
            // FULLTEXT SEARCHING
            // ---------------------------------------------------------

                private function searchByFulltext(string $query, int $limit): array {
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

            // ---------------------------------------------------------
            // CONTEXT CONSTRUCTION
            // ---------------------------------------------------------

                private function buildContext(array $rows): string {
                    $context = '';
                    foreach ($rows as $row) {
                        $context .= "
                            DOCUMENT : {$row['title']}
                            {$row['content']}
                            -----------------------------------
                        ";
                    }
                    return trim($context);
                }

            // ---------------------------------------------------------
            // QUERY FORMATING
            // ---------------------------------------------------------         

                private function sanitize(string $query): string {
                    $query = strip_tags($query);
                    $query = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $query);
                    $query = preg_replace('/\s+/', ' ', $query);
                    return trim($query);
                }

        }