<?php declare(strict_types=1);

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */

    //---------------------------------------------------------  
	// CLASS SECURE MANAGER
	//--------------------------------------------------------- 

        namespace tools;
        use finfo;
    
        class SecureManager {
            
            public array $filter = [];
    
            private bool $jsonResponse;
            private bool $requirePost;
            private bool $requireCsrf;
            private int $maxSize;
    
            public function __construct(
                bool $requirePost = false,
                bool $requireCsrf = false,
                int $maxSize = 10000000
            ) {
                $this->requirePost = $requirePost;
                $this->requireCsrf = $requireCsrf;
                $this->maxSize = $maxSize;
    
                $this->jsonResponse = $this->detectJsonMode();
    
                if (session_status() !== PHP_SESSION_ACTIVE) {
                    throw new \RuntimeException('La session doit être démarrée avant SecureManager.');
                }
    
                $this->checkMethod();
                $this->filter = $this->getInput();
    
                if ($this->requireCsrf && $_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->checkCsrf();
                }
            }
    
            /* =========================
            * INPUT
            * ========================= */
    
            private function detectJsonMode(): bool {
                $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
                $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    
                return str_contains($contentType, 'application/json')
                    || str_contains($accept, 'application/json');
            }
    
            private function checkMethod(): void {
                if ($this->requirePost && ($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
                    $this->error(405, 'Méthode non autorisée');
                }
            }
    
            private function getInput(): array {
                $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
                $isJson = str_contains($contentType, 'application/json');
    
                if ($isJson) {
                    $input = file_get_contents('php://input');
    
                    if (is_string($input) && strlen($input) > $this->maxSize) {
                        $this->error(413, 'Payload trop volumineux');
                    }
    
                    $data = json_decode($input, true);
    
                    if (!is_array($data)) {
                        $this->error(400, 'JSON invalide');
                    }
    
                    return $data;
                }
    
                return array_merge($_GET, $_POST);
            }
    
        //---------------------------------------------------------
        // VALIDATION
        //---------------------------------------------------------

    
            public function v(string $type, string $path, bool $required = true): mixed {
                $value = $this->getPath($this->filter, $path);
    
                return $this->validate($type, $value, $required);
            }
    
            private function validate(string $type, mixed $value, bool $required): mixed {
                if ($required && ($value === null || $value === '')) {
                    $this->error(422, 'Champ requis');
                }
    
                if (!$required && ($value === null || $value === '')) {
                    return null;
                }
    
                return match ($type) {
    
                    'int' => filter_var($value, FILTER_VALIDATE_INT)
                        ?? $this->error(422, 'Entier invalide'),
    
                    'email' => filter_var($value, FILTER_VALIDATE_EMAIL)
                        ?: $this->error(422, 'Email invalide'),
    
                    'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
                        ?? $this->error(422, 'Booléen invalide'),
    
                    'float' => filter_var($value, FILTER_VALIDATE_FLOAT)
                        ?: $this->error(422, 'Nombre invalide'),
    
                    'array' => is_array($value)
                        ? $value
                        : $this->error(422, 'Tableau invalide'),
    
                    'string' => is_string($value)
                        ? trim($value)
                        : $this->error(422, 'Chaîne invalide'),
    
                    'search' => $this->validateSearch($value),
    
                    'slug'   => $this->validateSlug($value),
    
                    default => $this->error(500, 'Type inconnu'),
                };
            }
    
            private function validateSearch(mixed $value): string {
                if (!is_string($value)) {
                    $this->error(422, 'Recherche invalide');
                }
    
                $value = trim($value);
    
                if (strlen($value) > 100) {
                    $this->error(422, 'Trop long');
                }
    
                return $value;
            }
    
            private function validateSlug(mixed $value): string {
                if (!is_string($value)) {
                    $this->error(422, 'Slug invalide');
                }
    
                $value = trim($value);
    
                if (!preg_match('/^[a-zA-Z0-9_-]+$/', $value)) {
                    $this->error(422, 'Format invalide');
                }
    
                return $value;
            }
    
        //---------------------------------------------------------
        // SESSION
        //---------------------------------------------------------
    
            public function session(string $path, string $type = 'string', bool $required = true): mixed {
                $value = $this->getPath($_SESSION, $path);
    
                return $this->validate($type, $value, $required);
            }
    
            public function setSession(string $path, mixed $value): void {
                $keys = explode('.', $path);
                $ref =& $_SESSION;
    
                foreach ($keys as $key) {
                    if (!isset($ref[$key]) || !is_array($ref[$key])) {
                        $ref[$key] = [];
                    }
                    $ref =& $ref[$key];
                }
    
                $ref = $value;
            }
    
            public function removeSession(string $path): void {
                $keys = explode('.', $path);
                $ref =& $_SESSION;
                $last = array_pop($keys);
    
                foreach ($keys as $key) {
                    if (!isset($ref[$key]) || !is_array($ref[$key])) {
                        return;
                    }
                    $ref =& $ref[$key];
                }
    
                unset($ref[$last]);
            }
    
        //---------------------------------------------------------
        // PATH ACCESS
        //---------------------------------------------------------
    
            private function getPath(array $data, string $path): mixed {
                $keys = explode('.', $path);
    
                foreach ($keys as $key) {
                    if (!is_array($data) || !array_key_exists($key, $data)) {
                        return null;
                    }
                    $data = $data[$key];
                }
    
                return $data;
            }


        //---------------------------------------------------------
        // CSRF
        //---------------------------------------------------------
    
            private function checkCsrf(): void {
                $csrfSession = $_SESSION['csrf'] ?? null;

                // Accepte le token depuis le header OU depuis le body
                $csrfClient = $_SERVER['HTTP_X_CSRF_TOKEN'] 
                    ?? $this->filter['csrf'] 
                    ?? null;

                if (!is_string($csrfSession) || !is_string($csrfClient) || !hash_equals($csrfSession, $csrfClient)) {
                    $this->error(403, 'CSRF invalide');
                }
            }


        //---------------------------------------------------------
        // SECURITY HELPERS
        //---------------------------------------------------------
    
            public static function e(string $value): string {
                return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }

            public static function raw(mixed $value): string {
                if (!is_string($value)) return '';
                $value = trim($value);
                if (!mb_check_encoding($value, 'UTF-8')) {
                    $value = mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');
                }
                return $value;
            }

    
        //---------------------------------------------------------
        // UPLOAD
        //---------------------------------------------------------
    
            public function upload(array $file, string $dir, array $allowedExt): string {
                if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
                    $this->error(400, 'Upload invalide');
                }
    
                if ($file['size'] > 5_000_000) {
                    $this->error(413, 'Fichier trop volumineux');
                }
    
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
                if (!in_array($ext, $allowedExt, true)) {
                    $this->error(415, 'Extension non autorisée');
                }
    
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mime  = $finfo->file($file['tmp_name']);
    
                $map = [
                    'jpg' => 'image/jpeg',
                    'png' => 'image/png',
                    'pdf' => 'application/pdf',
                ];
    
                if (!isset($map[$ext]) || $map[$ext] !== $mime) {
                    $this->error(415, 'Type MIME invalide');
                }
    
                if (!is_dir($dir) || !is_writable($dir)) {
                    $this->error(500, 'Répertoire invalide');
                }
    
                $name = bin2hex(random_bytes(16)) . '.' . $ext;
                $path = rtrim($dir, '/') . '/' . $name;
    
                if (!move_uploaded_file($file['tmp_name'], $path)) {
                    $this->error(500, 'Erreur upload');
                }
    
                return $name;
            }

        //---------------------------------------------------------
        // ERROR HANDLER
        //---------------------------------------------------------
    
            private function error(int $code, string $message): never {
                http_response_code($code);
    
                if ($this->jsonResponse) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(['error' => $message]);
                } else {
                    echo "<h1>{$code}</h1><p>{$message}</p>";
                }
    
                exit;
            }
        }