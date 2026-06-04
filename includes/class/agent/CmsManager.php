<?php declare(strict_types=1);

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */

    //---------------------------------------------------------  
	// CLASS AI CMS MANAGER
	//--------------------------------------------------------- 

        namespace Agent;

        use PDO;

        class CmsManager {
            public function __construct(
                private PDO $pdo
            ) {}

            public function updatePageGrapes(array $data): string
            {
                $stmt = $this->pdo->prepare("
                    UPDATE root_pages
                    SET
                        title = :title,
                        components = :components,
                        styles = :styles,
                        text = :text,
                        keywords = :keywords,
                        description = :description
                    WHERE id = :id
                ");

                $stmt->execute([
                    'id' => $data['id'],
                    'title' => $data['title'] ?? '',
                    'components' => json_encode($data['components'] ?? []),
                    'styles' => json_encode($data['styles'] ?? []),
                    'text' => $data['text'] ?? '',
                    'keywords' => $data['keywords'] ?? '',
                    'description' => $data['description'] ?? ''
                ]);

                return "Page mise à jour ID: " . $data['id'];
            }

            public function getPage(int $id): array
            {
                $stmt = $this->pdo->prepare("
                    SELECT * FROM root_pages WHERE id = :id
                ");

                $stmt->execute(['id' => $id]);

                return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
            }
        }