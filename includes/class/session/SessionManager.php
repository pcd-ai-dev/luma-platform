<?php declare(strict_types=1);

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 4
 */

    namespace Session;
    use PDO;
    use InvalidArgumentException;

    class SessionManager {

        protected PDO $pdo;
        protected array $opts = [
            'cookie_name'          => 'ADMSESS',
            'cookie_lifetime'      => 0,
            'cookie_path'          => '/',
            'cookie_domain'        => '',
            'cookie_secure'        => false,
            'cookie_httponly'      => true,
            'cookie_samesite'      => 'Strict',

            'regenerate_interval'  => 300,
            'inactivity_timeout'   => 2500,     // FIX : 25000 → 2500 (~40 min)

            'table_user'           => 'admin_user',
            'table_session'        => 'admin_session',
            'table_site'           => 'root_site',

            'idSite'               => 1,
            'sitealt'              => '',
            'imgLogoTarget'        => '',

            'bind_ip'              => false,
            'ip_bytes_to_bind'     => 1,
            'bind_ua'              => false,
        ];

        protected ?array $adminData = null;

        //---------------------------------------------------------
        // CONSTRUCT
        //---------------------------------------------------------

        public function __construct(PDO $pdo, array $options = []) {

            $this->pdo  = $pdo;
            $this->opts = array_merge($this->opts, $options);

            $this->opts['table_user']    = $this->validateTableName($this->opts['table_user']);
            $this->opts['table_session'] = $this->validateTableName($this->opts['table_session']);
            $this->opts['table_site']    = $this->validateTableName($this->opts['table_site']);

            if (empty($this->opts['cookie_domain'])) {
                $host = $_SERVER['SERVER_NAME'] ?? ($_SERVER['HTTP_HOST'] ?? '');
                $host = preg_replace('/:\d+$/', '', $host);
                $host = preg_replace('/[^A-Za-z0-9\.\-]/', '', $host);
                $this->opts['cookie_domain'] = $host;
            }

            if ($this->opts['cookie_secure'] === null) {
                $this->opts['cookie_secure'] = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                    || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
            }

            if (session_status() === PHP_SESSION_NONE) {
                session_name($this->opts['cookie_name']);
                session_set_cookie_params([
                    'lifetime' => (int)$this->opts['cookie_lifetime'],
                    'path'     => $this->opts['cookie_path'],
                    'domain'   => $this->opts['cookie_domain'] ?: '',
                    'secure'   => (bool)$this->opts['cookie_secure'],
                    'httponly' => (bool)$this->opts['cookie_httponly'],
                    'samesite' => $this->opts['cookie_samesite'],
                ]);
            }
        }

        //---------------------------------------------------------
        // DEMARRAGE SESSION
        //---------------------------------------------------------

        public function start(): void {

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (empty($_SESSION['__meta'])) {
                $_SESSION['__meta'] = [
                    'created'       => time(),
                    'last_regen'    => time(),
                    'last_activity' => time(),
                ];
            }

            // Inactivity timeout
            if (!empty($this->opts['inactivity_timeout'])
                && (time() - ($_SESSION['__meta']['last_activity'] ?? 0)) > (int)$this->opts['inactivity_timeout']
            ) {
                $this->destroySession();
                session_start();
                return;
            }
            $_SESSION['__meta']['last_activity'] = time();

            if (!empty($this->opts['regenerate_interval'])
                && (time() - ($_SESSION['__meta']['last_regen'] ?? 0)) > (int)$this->opts['regenerate_interval']
            ) {
                session_regenerate_id(true);
                $_SESSION['__meta']['last_regen'] = time();
                $this->updateSessionSidInDb(session_id());
            }
        }

        //---------------------------------------------------------
        // GET USER ADMIN INFO
        //---------------------------------------------------------

        public function getUserInfoAdmin(): ?array {

            $this->start();

            $sid = session_id();
            if (empty($sid)) return null;

            $req = "SELECT u.idSite, u.id, u.password, u.date, u.last_modified, u.email, u.gender,
                           u.last_name, u.first_name, u.img, u.username, u.level, u.filter,
                           u.menuItem, u.menu, u.menuStatus,
                           s.id as session_user_id, s.sid
                    FROM {$this->opts['table_session']} s
                    INNER JOIN {$this->opts['table_user']} u ON s.id = u.id
                    WHERE s.sid = :sid
                    LIMIT 1";

            $res = $this->pdo->prepare($req);
            $res->execute([':sid' => $sid]);
            $adminData = $res->fetch(PDO::FETCH_ASSOC);

            return $adminData ?: null;
        }

        //---------------------------------------------------------
        // GET USER BY EMAIL
        //---------------------------------------------------------

        public function getUserByEmail(string $email): ?array {

            if (empty($email)) return null;

            $req = "SELECT * FROM {$this->opts['table_user']} WHERE email = :email LIMIT 1";
            $res = $this->pdo->prepare($req);
            $res->execute([':email' => $email]);
            $rmail = $res->fetch(PDO::FETCH_ASSOC);

            return $rmail ?: null;
        }

        //---------------------------------------------------------
        // ADMINDATA | VERIFY
        //---------------------------------------------------------

        public function getAdminData(): ?array {

            if ($this->adminData !== null) {
                return $this->adminData;
            }

            $this->adminData = $this->getUserInfoAdmin();
            return $this->adminData;
        }

        //---------------------------------------------------------
        // GET SITE ID
        //---------------------------------------------------------

        public function getIdSite(string $varLinkData): ?array {

            $this->start();

            if (empty($varLinkData)) return null;

            $sql  = "SELECT id, img, bgColor, adminColor, varLink, imgSquare, name
                     FROM {$this->opts['table_site']}
                     WHERE varLink = :varLinkData";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':varLinkData' => $varLinkData]);
            $r = $stmt->fetch(PDO::FETCH_ASSOC);

            return $r !== false ? $r : null;
        }

        //---------------------------------------------------------
        // GET SITE INFO
        //---------------------------------------------------------

        public function getSiteData(int $idSite): ?array {

            $this->start();

            if (empty($idSite)) return null;

            $sql  = "SELECT * FROM {$this->opts['table_site']} WHERE id = :idSite";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':idSite' => $idSite]);
            $siteData = $stmt->fetch(PDO::FETCH_ASSOC);

            return $siteData ?: null;
        }

        //---------------------------------------------------------
        // NETTOYAGE SESSION ADMIN
        //---------------------------------------------------------

        public function dbCleanAdmin(): void {   // FIX : ?array → void

            $this->start();
            if (empty($_SESSION['user_id'])) return;

            $limit    = date('Y-m-d H:i:s', strtotime('-2 days'));
            $cleanSQL = $this->pdo->prepare("DELETE FROM {$this->opts['table_session']} WHERE last_modified < :limit");
            $cleanSQL->execute([':limit' => $limit]);
        }

        //---------------------------------------------------------
        // GET IP
        //---------------------------------------------------------

        protected function getPartialIp(): string {

            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
            if (!$this->opts['bind_ip'] || empty($ip)) return '';

            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $parts = explode('.', $ip);
                $n     = max(1, min(4, (int)$this->opts['ip_bytes_to_bind']));
                return implode('.', array_slice($parts, 0, $n));
            }

            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                $hextets = explode(':', $ip);
                return implode(':', array_slice($hextets, 0, 4));
            }

            return $ip;
        }

        //---------------------------------------------------------
        // GET BROWSER
        //---------------------------------------------------------

        protected function getBrowser(): ?string {
            $browser = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);
            return $browser ?: null;
        }

        //---------------------------------------------------------
        // LOGIN PROCESS
        //---------------------------------------------------------

        public function getLoginCheckAdmin(string $email, string $password, int $idSite): ?string {

            $this->start();

            $email    = trim($email);
            $password = trim($password);
            if ($email === '' || $password === '') return null;

            $req  = "SELECT id, password, salt FROM {$this->opts['table_user']}
                     WHERE email = :email AND idSite = :idSite LIMIT 1";
            $stmt = $this->pdo->prepare($req);
            $stmt->execute([':email' => $email, ':idSite' => $idSite]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$user) return null;

            $stored       = $user['password'] ?? '';
            $salt         = $user['salt'] ?? '';
            $isModernHash = str_starts_with($stored, '$');

            if ($isModernHash) {
                if (!password_verify($password, $stored)) return null;

                if (password_needs_rehash($stored, PASSWORD_DEFAULT)) {
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $upd     = $this->pdo->prepare("UPDATE {$this->opts['table_user']} SET password = :ph WHERE id = :id");
                    $upd->execute([':ph' => $newHash, ':id' => $user['id']]);
                }

                return (string)$user['id'];

            } else {
                $legacy = sha1($salt . $password);
                if (!hash_equals($legacy, $stored)) return null;

                $newHash = password_hash($password, PASSWORD_DEFAULT);
                try {
                    $upd = $this->pdo->prepare("UPDATE {$this->opts['table_user']} SET password = :ph, salt = NULL WHERE id = :id");
                    $upd->execute([':ph' => $newHash, ':id' => $user['id']]);
                } catch (\Exception $e) {
                }

                return (string)$user['id'];
            }
        }

        //---------------------------------------------------------
        // OPEN SESSION ADMIN
        //---------------------------------------------------------

        public function openSessionAdmin(int $id): ?string {

            $this->start();

            if (session_status() !== PHP_SESSION_ACTIVE) return null;

            if (empty($id)) return null;

            session_regenerate_id(true);
            $sid = session_id();

            $reqDelete = "DELETE FROM {$this->opts['table_session']} WHERE id = :id";
            $resDelete = $this->pdo->prepare($reqDelete);
            $resDelete->execute([':id' => $id]);

            $redInsert = "INSERT INTO {$this->opts['table_session']} (sid, id, ip, browser, last_modified)
                          VALUES (:sid, :id, :ip, :browser, NOW())";
            $resInsert = $this->pdo->prepare($redInsert);
            $resInsert->execute([
                ':sid'     => $sid,
                ':id'      => $id,
                ':ip'      => $this->getPartialIp(),
                ':browser' => $this->getBrowser(),
            ]);

            $_SESSION['user_id'] = $id;
            $_SESSION['sid']     = $sid;

            return null;
        }

        //---------------------------------------------------------
        // SUPPRESSION SESSION ADMIN
        //---------------------------------------------------------

        public function closeSessionAdmin(string $varLink): ?string {

            $this->start();

            $sid = session_id();

            $reqDelete = "DELETE FROM {$this->opts['table_session']} WHERE sid = :sid";
            $resDelete = $this->pdo->prepare($reqDelete);
            $resDelete->execute([':sid' => $sid]);

            $v    = preg_replace('#[^A-Za-z0-9_\-/]#', '', $varLink);
            $host = $_SERVER['SERVER_NAME'] ?? ($_SERVER['HTTP_HOST'] ?? '');
            $host = preg_replace('/[^A-Za-z0-9\.\-]/', '', preg_replace('/:\d+$/', '', $host));

            // FIX : scheme dynamique au lieu de https:// forcé
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $link   = $scheme . '://' . $host . '/manager' . $v . '/';

            $this->destroySession();

            return $link ?: null;
        }

        //---------------------------------------------------------
        // HELPERS
        //---------------------------------------------------------

        protected function validateTableName(string $n): string {
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $n)) {
                throw new InvalidArgumentException("Invalid table name: $n");
            }
            return $n;
        }

        protected function updateSessionSidInDb(string $newSid): void {
            if (empty($_SESSION['user_id'])) return;

            try {
                $upd = $this->pdo->prepare("UPDATE {$this->opts['table_session']}
                                            SET sid = :sid, last_modified = NOW()
                                            WHERE id = :id");
                $upd->execute([':sid' => $newSid, ':id' => $_SESSION['user_id']]);
            } catch (\Exception $e) {
            }
        }

        protected function destroySession(): void {
            $_SESSION = [];
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(), '', time() - 62000,
                    $params['path']   ?? '/',
                    $params['domain'] ?? '',
                    $params['secure'] ?? false,
                    $params['httponly'] ?? true
                );
            }
            session_destroy();
        }

        //---------------------------------------------------------
        // PASSWORD GENERATION
        //---------------------------------------------------------

        public function createPass(int $length, bool $startWithVowel = true): string {
            $consonnes = "bcdfghjklmnpqrstvwxz";
            $voyelles  = "aeiouy";
            $useVowel  = $startWithVowel;
            $password  = '';

            for ($i = 0; $i < $length; $i++) {
                $chars    = $useVowel ? $voyelles : $consonnes;
                $index    = random_int(0, strlen($chars) - 1);
                $password .= $chars[$index];
                $useVowel = !$useVowel;
            }

            return $password;
        }

        //---------------------------------------------------------
        // FORMULAIRE LOGIN
        //---------------------------------------------------------

        public function getFormViewAdmin(int $idSite, string $imgLogoTarget, string $imgDefaultSquare, string $varLink, string $siteAlt): string {

            $imgLogo = empty($imgLogoTarget) ? $imgDefaultSquare : '/img/root/rec/' . $imgLogoTarget;
            $siteAlt = htmlspecialchars($siteAlt, ENT_QUOTES, 'UTF-8');
            $varLink = htmlspecialchars($varLink, ENT_QUOTES, 'UTF-8');

            $view  = '<div style="text-align:center;">';
            $view .= (!empty($imgLogoTarget)? '<img src="' . $imgLogo . '" width="150" alt="' . $siteAlt . '" />' : '');
            $view .= '<div>&nbsp;</div><div>&nbsp;</div>';
            $view .= '<input type="text" name="email" id="email" value="" class="formLogAdmin" placeholder="E-mail ...">';
            $view .= '<div>&nbsp;</div>';
            $view .= '<input type="password" name="password" id="password" value="" class="formLogAdmin" placeholder="Mot de passe ...">';
            $view .= '<div>&nbsp;</div>';
            $view .= '<input type="hidden" name="logadmin" id="logadmin" value="1">';
            $view .= '<input type="hidden" name="idSite" value="' . $idSite . '">';
            $view .= '<button class="cubutton">Go</button>';
            $view .= '<div>&nbsp;</div><div>&nbsp;</div>';
            if (!empty($varLink)) {
                //$view .= '<a href="/manager' . $varLink . '/forgot.html" target="_self" style="color:#FFF;">Mot de passe oubli&eacute; ?</a>';
            }
            $view .= '</div>';

            return $view;
        }

        //---------------------------------------------------------
        // FORMULAIRE MOT DE PASSE OUBLIE
        //---------------------------------------------------------

        public function getFormForgotAdmin(string $msg, int $status, int $idSite, string $imgLogoTarget, string $imgDefaultSquare, string $varLink, string $siteAlt): string {

            $imgLogo     = empty($imgLogoTarget) ? $imgDefaultSquare : '/img/root/square/' . $imgLogoTarget;
            $siteAlt     = htmlspecialchars($siteAlt, ENT_QUOTES, 'UTF-8');
            $varLink     = htmlspecialchars($varLink, ENT_QUOTES, 'UTF-8');
            $msg         = htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');

            // FIX : XSS sur courrielpost
            $courriel    = htmlspecialchars($_SESSION['courrielpost'] ?? '', ENT_QUOTES, 'UTF-8');

            $view  = '<div style="text-align:center;">';
            $view .= '<img src="' . $imgLogo . '" width="150" alt="' . $siteAlt . '" />';
            $view .= '<div>&nbsp;</div><div>&nbsp;</div>';

            if ($status === 0) {
                $view .= '<div>Veuillez ins&eacute;rer votre courriel dans le champ ci-dessous :</div>';
                $view .= '<div>&nbsp;</div>';
                $view .= '<input type="text" name="courriel" id="courriel" value="' . $courriel . '" class="formLogAdmin" placeholder="Votre courriel ..." />';
            }

            $view .= '<div>&nbsp;</div>';
            $view .= '<div>' . $msg . '</div>';
            $view .= '<div>&nbsp;</div>';

            if ($status === 0) {
                $view .= '<input type="hidden" name="logadmin" id="logadmin" value="1">';
                $view .= '<input type="hidden" name="idSite" value="' . $idSite . '">';
                $view .= '<button class="cubutton">Valider</button>';
                $view .= '<div>&nbsp;</div>';
            }

            $view .= '<div>&nbsp;</div>';
            $view .= '<a href="/manager' . $varLink . '/" target="_self" style="color:#FFF;">Vous connecter ?</a>';
            $view .= '</div>';

            return $view;
        }
    }