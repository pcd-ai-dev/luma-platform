<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod
 *  @description  Installation wizard — delete after setup
 */

  //---------------------------------------------------------
	// INSTALL VERIFICATION
	//---------------------------------------------------------

    if (file_exists(__DIR__ . '/config/installed.lock')) {
        http_response_code(403);
        die('<h1>403 — Already installed.</h1><p>Supprimez <code>config/installed.lock</code> to re-run setup.</p>');
    }

  //---------------------------------------------------------
	// INSTALL VERIFICATION
	//---------------------------------------------------------

    $step = isset($_POST['step']) ? (int)$_POST['step']: (isset($_GET['step']) ? (int)$_GET['step'] : 1);
    //$step = (int)($_POST['step'] ?? $_GET['step'] ?? 1);
    $errors  = [];
    $success = false;

  //---------------------------------------------------------
	// HELDER
	//---------------------------------------------------------

    function esc(string $v): string {
        return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
    }

    function post(string $k, string $default = ''): string {
        return trim($_POST[$k] ?? $default);
    }

    function testDB(string $host, string $base, string $user, string $pass): string|true {
        try {
            $pdo = new PDO(
                "mysql:host=$host;dbname=$base;charset=utf8mb4",
                $user, $pass,
                [PDO::ATTR_TIMEOUT => 5, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            return true;
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

  function importSQL(string $host, string $base, string $user, string $pass, string $sqlPath): string|true {
        try {
            $pdo = new PDO(
                "mysql:host=$host;dbname=$base;charset=utf8mb4",
                $user,
                $pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            $sql = file_get_contents($sqlPath);
            if ($sql === false) {
                return "Impossible de lire $sqlPath";
            }

            $queries = preg_split('/;\s*\n/', $sql);

            foreach ($queries as $i => $query) {
                $query = trim($query);

                if ($query === '') {
                    continue;
                }

                try {
                    $pdo->exec($query);
                } catch (PDOException $e) {
                    return "Erreur requête ";
                }
            }

            return true;

        } catch (PDOException $e) {
            return $e->getMessage();
        }
  }

	function createUser(string $hostDB, string $baseDB, string $userDB, string $passDB, string $email, string $pass): string|true {

    try {
      $pdo = new PDO(
        "mysql:host=$hostDB;dbname=$baseDB;charset=utf8mb4",
        $userDB,
        $passDB,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
      );

      $ressourceGender   = 'Mr';
      $ressourceFirstName = 'Peter';
      $ressourceLastName = 'DOE';

      $ressourceType     = 0;
      $ressourceLevel    = 99;
      $ressourceEmail    = $email ?? '';
      $ressourcePhone    = '';
      $googleAgenda      = '';
      $ressourcePass     = $pass ?? '';

      $ressourceSalt=sha1($ressourceFirstName.$ressourceLastName.$ressourceType.$ressourceEmail);
      $ressourcePassSQL = sha1($ressourceSalt.$ressourcePass);
        
      $datecdt=date("Y-m-d");
      $heurecdt=date("H:i:s");
      $dateitem=$datecdt.' '.$heurecdt;

      $menuItem='0,1,2';
      $menuStatus=1;

	   //---------------------------------------------------------  
	   // Insertion Owner DB
	   //--------------------------------------------------------- 
	
        $reqadduser = "INSERT INTO admin_user (idSite,date,gender,last_name,first_name,email,phone,googleAgenda,password,level,salt,menuItem,menuStatus) VALUES(:idSite, :dateuser, :genderuser, :lastnameuser, :firstnameuser, :emailuser, :phoneuser, :googleAgendaUser, :passuser, :leveluser, :salt, :menuItem, :menuStatus)";
        $resadduser = $pdo->prepare($reqadduser);
        $resadduser->bindValue(':idSite', 1, PDO::PARAM_INT);
        $resadduser->bindValue(':genderuser', $ressourceGender, PDO::PARAM_STR);
        $resadduser->bindValue(':dateuser', $dateitem, PDO::PARAM_STR);
        $resadduser->bindValue(':lastnameuser', $ressourceLastName, PDO::PARAM_STR);
        $resadduser->bindValue(':firstnameuser', $ressourceFirstName, PDO::PARAM_STR);
        $resadduser->bindValue(':emailuser', $ressourceEmail, PDO::PARAM_STR);
        $resadduser->bindValue(':phoneuser', $ressourcePhone, PDO::PARAM_STR);
        $resadduser->bindValue(':googleAgendaUser', $googleAgenda, PDO::PARAM_STR);
        $resadduser->bindValue(':passuser', $ressourcePassSQL, PDO::PARAM_STR);
        $resadduser->bindValue(':leveluser', $ressourceLevel, PDO::PARAM_STR);
        $resadduser->bindValue(':salt', $ressourceSalt, PDO::PARAM_STR);
        $resadduser->bindValue(':menuItem', $menuItem, PDO::PARAM_STR);
        $resadduser->bindValue(':menuStatus', $menuStatus, PDO::PARAM_INT);
        $resadduser->execute();
        $resadduser->closeCursor();
        $resadduser = NULL;

      return true;

    } catch (PDOException $e) {
      return $e->getMessage();
    }

	}

  //---------------------------------------------------------
	// CREATE CONFIG FILE
	//---------------------------------------------------------

  function writeConfig(array $d): bool {
      $tpl = <<<PHP
        <?php

        /*
        *  @author Luma Prod - Pierre Cosmao Dumanoir
        *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
        *  @version  Release: 4
        *  @generated {DATE}
        */


          //---------------------------------------------------------
          // INIT NAME SPACES
          //---------------------------------------------------------

            use Tools\PDOManager;
            use PHPMailer\PHPMailer\PHPMailer;
            use PHPMailer\PHPMailer\Exception;


          //---------------------------------------------------------
          // ERROR DISPLAY
          //---------------------------------------------------------

            ini_set('display_errors', 0);
            ini_set('display_startup_errors', 0);
            error_reporting(0);


          //---------------------------------------------------------  
          // CONFIG PATH
          //--------------------------------------------------------- 

            if(!defined('BASE_PATH')) define('BASE_PATH', realpath(__DIR__));
            if(!defined('PROCESSPATH')) define('PROCESSPATH', BASE_PATH.'/includes/');
            if(!defined('ADMINPATH')) define('ADMINPATH', BASE_PATH.'/includes/admin/');
            if(!defined('ADMINPROCESSPATH')) define('ADMINPROCESSPATH', ADMINPATH.'process/');
            if(!defined('ADMINSCHEDULERPATH')) define('ADMINSCHEDULERPATH', ADMINPATH.'scheduler/');
            if(!defined('CLASSPATH')) define('CLASSPATH', BASE_PATH.'/includes/class/');
            if(!defined('AJAXPATH')) define('AJAXPATH', '/includes/ajax/');
            if(!defined('XMLPATH')) define('XMLPATH', BASE_PATH.'/includes/xml/');
            if(!defined('VENDORPATH')) define('VENDORPATH', BASE_PATH.'/includes/vendor/');
            if(!defined('PLUGINSPATH')) define('PLUGINSPATH', BASE_PATH.'/plugins/');
            if(!defined('MANAGERPATH')) define('MANAGERPATH', BASE_PATH.'/manager/');
            if(!defined('GALERYPHOTOPATH')) define('GALERYPHOTOPATH', BASE_PATH.'/img/gallery/');
            
            date_default_timezone_set('UTC');
            date_default_timezone_set("Europe/Paris");


          //---------------------------------------------------------
          // CONNEXIONS
          //---------------------------------------------------------

            require BASE_PATH.'/includes/Composer/vendor/autoload.php';


          //---------------------------------------------------------  
          // CONFIG SESSION
          //--------------------------------------------------------- 

            if (session_status() === PHP_SESSION_NONE) {
              session_start();
            }
            if (empty(\$_SESSION['csrf'])) {
              \$_SESSION['csrf'] = bin2hex(random_bytes(32));
            }


          //---------------------------------------------------------
          // LOG DB
          //---------------------------------------------------------

            \$user = '{DB_USER}';
            \$password = '{DB_PASS}';
            \$base = '{DB_NAME}';
            \$host = '{DB_HOST}';
            \$dsn = "mysql:host=\$host;dbname=\$base;charset=utf8mb4";
            \$db = new PDOManager(\$dsn, \$user, \$password);


          //---------------------------------------------------------  
          // Prefix db
          //--------------------------------------------------------- 

            \$prefixRoot="root_";
            \$prefixParam="param_";
            \$prefixAdmin="admin_";
            \$prefixGallery="gallery_";
            \$prefixVideo="video_";
            \$prefixPost="post_";
            \$prefixLoc='loc_';
            \$prefixContact='contact_';


          //---------------------------------------------------------
          // VARS
          //---------------------------------------------------------
            
            \$httpStatus = (!empty(\$_SERVER['HTTPS']) && \$_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            \$hostStatus= \$_SERVER['HTTP_HOST'] ?? '';
            \$requestUri = \$_SERVER['REQUEST_URI'] ?? '';
            \$urlSSL = \$httpStatus.'://'.\$hostStatus.\$requestUri;

            \$siteHost='{SITE_HOST}';
            \$siteHostBase='{SITE_HOST_BASE}';
            \$siteName="{SITE_NAME}";
            \$siteAlt="{SITE_ALT}";
            \$bgColorDefault="background-image: linear-gradient(to left bottom, #020202, #0a0a0a, #111111, #161616, #1b1b1b, #1c1c1c, #1d1d1d, #1e1e1e, #1c1c1c, #1a1a1a, #181818, #161616);";


          //---------------------------------------------------------
          // IMG
          //---------------------------------------------------------

            \$imgDefaultSquare="/img/interface/default/defaultSquare.svg";
            \$imgDefaultRec="/img/interface/default/defaultRec.svg";
            \$imgDefaultThumb="/img/interface/default/defaultThumb.svg";


            //---------------------------------------------------------  
            // SMTP CONFIG
            //--------------------------------------------------------- 

            \$mailM = new PHPMailer;
            \$mailM->SMTPDebug = 0;
            \$mailM->isSMTP();
            \$mailM->Host = '{SMTP_HOST}';
            \$mailM->SMTPAuth = true;
            \$mailM->Username = '{SMTP_USER}';
            \$mailM->Password = '{SMTP_PASS}';
            \$mailM->SMTPSecure = 'tls';
            \$mailM->Port = 587;
            \$mailM->From = '{SMTP_FROM}';
            \$mailM->FromName = '{SMTP_FROM_NAME}';
            \$mailM->isHTML(true);


            //---------------------------------------------------------  
            // GMAP
            //---------------------------------------------------------

            define('GOOGLE_API_KEY', '{GMAP_KEY}');
            define('MAP_ID', '{GMAP_ID}');


          //---------------------------------------------------------  
            // AGENT CONFIG
            //---------------------------------------------------------
          
            define('ANTHROPIC_KEY', '{ANTHROPIC_KEY}');
            define('OPENAI_KEY', '{OPENAI_KEY}');
        PHP;

        $tpl = str_replace('{DATE}', date('Y-m-d H:i:s'), $tpl);
        $map = [
            '{DB_HOST}'       => $d['db_host'],
            '{DB_NAME}'       => $d['db_name'],
            '{DB_USER}'       => $d['db_user'],
            '{DB_PASS}'       => $d['db_pass'],
            '{SITE_HOST}'     => $d['site_host'],
            '{SITE_HOST_BASE}'=> $d['site_host_base'],
            '{SITE_NAME}'     => $d['site_name'],
            '{SITE_ALT}'      => $d['site_alt'],
            '{SMTP_HOST}'     => $d['smtp_host'],
            '{SMTP_USER}'     => $d['smtp_user'],
            '{SMTP_PASS}'     => $d['smtp_pass'],
            '{SMTP_FROM}'     => $d['smtp_from'],
            '{SMTP_FROM_NAME}'=> $d['smtp_from_name'],
            '{GMAP_KEY}'      => $d['gmap_key'],
            '{GMAP_ID}'       => $d['gmap_id'],
            '{ANTHROPIC_KEY}' => $d['anthropic_key'],
            '{OPENAI_KEY}'    => $d['openai_key'],
        ];

        $contentFile = __DIR__ . "/config.php";
        $content = str_replace(array_keys($map), array_values($map), $tpl);

        if (file_put_contents($contentFile, $content) === false) {
          return false;
        }else{
          return true;
        }


    }


  //---------------------------------------------------------
	// CREATE HTACCESS FILE
	//---------------------------------------------------------

    function writeHTAccess(): bool {

      $content = <<<HTACCESS
      Options +FollowSymlinks
      RewriteEngine on


      ##################################################################
      ## DEFAULT IMG
      ##################################################################

      RewriteCond %{DOCUMENT_ROOT}%{REQUEST_URI} !-f
      RewriteRule \.(gif|jpe?g|png|bmp|svg) /img/interface/default/defaultSquare.svg [NC,L]


      ##################################################################
      ## LANGUAGE
      ##################################################################

      RewriteRule ^language/([0-9]+)/$  /includes/lang/lang.php?idLang=$1  [QSA,L]


      ##################################################################
      ## ADMIN
      ##################################################################

      RewriteRule ^manager/logout/$  manager/config/logout.php [L]
      RewriteRule ^manager/$  manager/index.php [L]

      RewriteRule ^manager([^/]+)/logout/$  manager/config/logout.php [L]
      RewriteRule ^manager([^/]+)/$  manager/index.php?varLink=$1 [L]
      RewriteRule ^manager([^/]+)/(.+).html$  /manager/index.php?varLink=$1&item=$2 [L]
      RewriteRule ^manager([^/]+)/(.+)/(.+)/([0-9]+)/([0-9]+)/([0-9]+)/([0-9]+)/$ /manager/index.php?varLink=$1&item=$2&param=$3&category=$4&tab=$5&status=$6&mod=$7 [L]


      ##################################################################
      ## FRONT
      ##################################################################

      #Adressage
      RewriteRule ^([0-9]+)-(.+)\.html$  /index.php?idCategory=$1&item=$2  [L]
      RewriteRule ^([^/]+)/$  /index.php?varLink=$1  [L]
      RewriteRule ^([^/]+)/([0-9]+)-(.+)\.html$  /index.php?varLink=$1&idCategory=$2&item=$3  [L]

      #Revisions
      RewriteRule ^revision/([0-9]+)/([0-9]+)/([0-9]+)/$  /index.php?item=revision&idsite=$1&idLang=$2&idRevision=$3  [L]
      RewriteRule ^([^/]+)/revision/([0-9]+)/$ /index.php?varLink=$1&item=revision&idRevision=$1  [L]

      #Actualite
      RewriteRule ^news-([0-9]+)-([0-9]+)-(.+)\.html$ index.php?idsite=1&item=post&idCategory=$1&idNews=$2 [QSA,L]
      RewriteRule ^([^/]+)/news-([0-9]+)-([0-9]+)-(.+)\.html$ index.php?varLink=$1&item=post&idCategory=$2&idNews=$3 [QSA,L]


      ##################################################################
      ## ERRORS
      ##################################################################

      RewriteRule ^401/$  /index.php?item=error_401_authentification&idcat=8 [L]
      RewriteRule ^403/$  /index.php?item=error_403_access_denied&idcat=9 [L]
      RewriteRule ^404/$  /index.php?item=error_404_page_not_available&idcat=10 [L]

      ErrorDocument 401 /401/
      ErrorDocument 403 /403/
      ErrorDocument 404 /404/


      ##################################################################
      ## CONFIG / OPTION
      ##################################################################

      Options -Indexes
      HTACCESS;

      $htAccessFile = __DIR__ . "/.htaccess";

      if (file_put_contents($htAccessFile, $content) === false) {
        return false;
      }else{
        return true;
      }

    }

  

  //---------------------------------------------------------
	// WIZARD PROCESS
	//---------------------------------------------------------

    $saved = [];
    $allPost = $_POST;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if ($step === 1) {

        //---------------------------------------------------------
        // STEP 1 => TEST DB
        //---------------------------------------------------------

            $db_host = post('db_host', 'localhost');
            $db_name = post('db_name');
            $db_user = post('db_user');
            $db_pass = post('db_pass');

            if (!$db_name || !$db_user) $errors[] = 'Nom de base et utilisateur requis.';

            if (empty($errors)) {
                $res = testDB($db_host, $db_name, $db_user, $db_pass);
                if ($res !== true) $errors[] = "Connexion échouée : $res";
            }

            if (empty($errors)) $step = 2;

        } elseif ($step === 2) {

        //---------------------------------------------------------
        // STEP 2 => SITE INFO
        //---------------------------------------------------------

            if (!post('site_name')) $errors[] = 'Nom du site requis.';
            if (!post('site_host')) $errors[] = 'URL du site requise.';

            if (empty($errors)) $step = 3;

        } elseif ($step === 3) {

            //---------------------------------------------------------
            // STEP 3 => SMTP (OPTIONAL)
            //---------------------------------------------------------

              $step = 4;

        } elseif ($step === 4) {

            //---------------------------------------------------------
            // STEP 4 => API KEY (OPTIONAL)
            //---------------------------------------------------------

              $step = 5;

        } elseif ($step === 5) {

        //---------------------------------------------------------
        // STEP 5 => ADMIN LOGS
        //---------------------------------------------------------

            if (!post('admin_email')) $errors[] = 'Email de l\'administrateur requis.';
            if (!post('admin_pass')) $errors[] = 'Mot de passe de l\'administrateur requis.';

            // Collect everything
            $data = [
                'db_host'        => post('db_host', 'localhost'),
                'db_name'        => post('db_name'),
                'db_user'        => post('db_user'),
                'db_pass'        => post('db_pass'),
                'site_host'      => post('site_host'),
                'site_host_base' => post('site_host_base'),
                'site_name'      => post('site_name'),
                'site_alt'       => post('site_alt'),
                'smtp_host'      => post('smtp_host'),
                'smtp_user'      => post('smtp_user'),
                'smtp_pass'      => post('smtp_pass'),
                'smtp_from'      => post('smtp_from'),
                'smtp_from_name' => post('smtp_from_name'),
                'gmap_key'       => post('gmap_key'),
                'gmap_id'        => post('gmap_id'),
                'anthropic_key'  => post('anthropic_key'),
                'openai_key'     => post('openai_key'),
                'admin_email'    => post('admin_email'),
                'admin_pass'     => post('admin_pass'),
                'import_sql'     => post('import_sql', '0'),
            ];


            $sqlPath = __DIR__ . '/config/luma.sql';
            if (!file_exists($sqlPath)) {
                $errors[] = "Fichier SQL introuvable : $sqlPath";
            } else {
                $res = importSQL($data['db_host'], $data['db_name'], $data['db_user'], $data['db_pass'], $sqlPath);
                if ($res !== true) $errors[] = "Import SQL échoué : $res";

                createUser($data['db_host'], $data['db_name'], $data['db_user'], $data['db_pass'],$data['admin_email'], $data['admin_pass']);
            }

            if(empty($errors)) {
                if (!writeConfig($data)) {
                    $errors[] = "Impossible d'écrire config.php — vérifiez les permissions.";
                }

                if (!writeHTAccess()) {
                    $errors[] = "Impossible d'écrire .htAccess — vérifiez les permissions.";
                }
               
            }

            if (empty($errors)) {
                if (!is_dir(__DIR__ . '/config')) mkdir(__DIR__ . '/config', 0755, true);
                file_put_contents(__DIR__ . '/config/installed.lock', date('c'));
                $success = true;
                $step = 5;
            }
        }
    }

?>
<!DOCTYPE html>
  <html lang="fr">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Luma CRM — Installation</title>
      <link href="/css/fonts.css" rel="stylesheet" type="text/css" />
      <link href="/css/wizzard.css" rel="stylesheet" type="text/css" />
    </head>
    <body>

    <?php

        //---------------------------------------------------------
        // STEP CONFIG
        //---------------------------------------------------------

          $steps = [
              1 => ['label' => 'Base de données',  'tag' => 'DB'],
              2 => ['label' => 'Identité du site', 'tag' => 'SITE'],
              3 => ['label' => 'SMTP / E-mail',    'tag' => 'MAIL'],
              4 => ['label' => 'APIs & clés',      'tag' => 'API'],
              5 => ['label' => 'Accès Back-Office','tag' => 'ADMIM'],
              6 => ['label' => 'Terminé',          'tag' => 'DONE'],
          ];

          $curStep = $success ? 6 : $step;


        //---------------------------------------------------------
        // HIDDEN FIELD CONFIG
        //---------------------------------------------------------

          function hiddenFields(array $post, array $exclude = []): string {
              $out = '';
              foreach ($post as $k => $v) {
                  if (in_array($k, $exclude, true)) continue;
                  if ($k === 'step') continue;
                  $out .= '<input type="hidden" name="'.esc($k).'" value="'.esc($v).'">';
              }
              return $out;
          }
    ?>

      <div class="page">
        
        <!-- Header -->
        <div class="topbar">
          <div class="logo">LUMA<span>CRM WIZARD</span></div>
          <div class="topbar-version">v1 · <?= date('Y') ?></div>
        </div>

        <!-- Sidebar -->
        <aside class="sidebar">
          <?php foreach ($steps as $n => $info):
            if ($n < $curStep) $cls = 'done';
            elseif ($n === $curStep) $cls = 'active';
            else $cls = 'inactive';
          ?>
          <div class="step-item <?= $cls ?>">
            <div class="step-num">
              <?= ($n < $curStep) ? '✓' : $n ?>
            </div>
            <div class="step-label"><?= esc($info['label']) ?></div>
          </div>
          <?php endforeach; ?>

          <div class="sidebar-sep"></div>
          <div style="padding: 0 12px; color: var(--muted); font-size: 11px; line-height: 1.8;">
            Toutes les infos sont<br>écrites dans <code style="color:var(--accent)">config.php</code>.<br>
            Supprimez <code style="color:var(--accent)">launch.php</code><br>après installation.
          </div>
        </aside>

        <!-- Main -->
        <main class="main">

        <?php if (!empty($errors)): ?>
          <div class="errors">
            <?php foreach ($errors as $e): ?>
              <p>⚠ <?= esc($e) ?></p>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <?php
          //---------------------------------------------------------
          // STEP 1 => TEST DB
          //---------------------------------------------------------
        ?>
        <?php if ($curStep === 1): ?>

          <div class="section-tag">Étape 1 / 5</div>
          <div class="section-title">Base de données</div>
          <p class="section-desc">Connexion MySQL. Les identifiants seront testés avant de continuer.</p>

          <form method="POST">
            <input type="hidden" name="step" value="1">
            <?= hiddenFields($allPost, ['step','db_host','db_name','db_user','db_pass']) ?>

            <div class="form-grid">
              <div class="form-group">
                <label>Hôte <span class="req">*</span></label>
                <input type="text" name="db_host" value="<?= esc(post('db_host','localhost')) ?>" placeholder="localhost">
              </div>
              <div class="form-group">
                <label>Nom de la base <span class="req">*</span></label>
                <input type="text" name="db_name" value="<?= esc(post('db_name')) ?>" placeholder="luma_prod">
              </div>
              <div class="form-group">
                <label>Utilisateur <span class="req">*</span></label>
                <input type="text" name="db_user" value="<?= esc(post('db_user')) ?>" placeholder="root">
              </div>
              <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="db_pass" value="<?= esc(post('db_pass')) ?>" placeholder="••••••••">
              </div>
            </div>

            <div class="btn-row">
              <button type="submit" class="btn btn-primary">Tester la connexion →</button>
            </div>
          </form>

        <?php
          //---------------------------------------------------------
          // STEP 2 => SITE INFO
          //---------------------------------------------------------
        ?>
        <?php elseif ($curStep === 2): ?>

          <div class="section-tag">Étape 2 / 5</div>
          <div class="section-title">Identité du site</div>
          <p class="section-desc">Ces valeurs alimentent les variables globales du CRM.</p>

          <form method="POST">
            <input type="hidden" name="step" value="2">
            <?= hiddenFields($allPost, ['step','site_host','site_host_base','site_name','site_alt']) ?>

            <div class="form-grid">
              <div class="form-group full">
                <label>Nom du site <span class="req">*</span></label>
                <input type="text" name="site_name" value="<?= esc(post('site_name')) ?>" placeholder="Mon CRM">
              </div>
              <div class="form-group full">
                <label>Slogan / Alt <span class="opt">(optionnel)</span></label>
                <input type="text" name="site_alt" value="<?= esc(post('site_alt')) ?>" placeholder="Le meilleur CRM du monde">
              </div>
              <div class="form-group full">
                <label>URL complète <span class="req">*</span> <span class="opt">avec https://</span></label>
                <input type="url" name="site_host" value="<?= esc(post('site_host')) ?>" placeholder="https://monsite.fr">
              </div>
              <div class="form-group full">
                <label>URL base <span class="opt">domaine seul, sans slash final</span></label>
                <input type="text" name="site_host_base" value="<?= esc(post('site_host_base')) ?>" placeholder="monsite.fr">
              </div>
            </div>

            <div class="btn-row">
              <a href="?step=1" class="btn btn-ghost">← Retour</a>
              <button type="submit" class="btn btn-primary">Continuer →</button>
            </div>
          </form>

        <?php
          //---------------------------------------------------------
          // STEP 3 => STEP 3 => SMTP (OPTIONAL)
          //---------------------------------------------------------
        ?>
        <?php elseif ($curStep === 3): ?>

          <div class="section-tag">Étape 3 / 5</div>
          <div class="section-title">SMTP / E-mail</div>
          <p class="section-desc">Configuration PHPMailer. Peut être complétée plus tard en éditant <code>config.php</code>.</p>

          <div class="notice"><strong>Optionnel</strong> — laissez vide pour passer cette étape. Le CRM fonctionnera sans envoi d'e-mails.</div>

          <form method="POST">
            <input type="hidden" name="step" value="3">
            <?= hiddenFields($allPost, ['step','smtp_host','smtp_user','smtp_pass','smtp_from','smtp_from_name']) ?>

            <div class="form-grid">
              <div class="form-group full">
                <label>Hôte SMTP <span class="opt">(ex : smtp.ionos.fr)</span></label>
                <input type="text" name="smtp_host" value="<?= esc(post('smtp_host')) ?>" placeholder="smtp.monhébergeur.fr">
              </div>
              <div class="form-group">
                <label>Utilisateur SMTP</label>
                <input type="email" name="smtp_user" value="<?= esc(post('smtp_user')) ?>" placeholder="noreply@monsite.fr">
              </div>
              <div class="form-group">
                <label>Mot de passe SMTP</label>
                <input type="password" name="smtp_pass" value="<?= esc(post('smtp_pass')) ?>" placeholder="••••••••">
              </div>
              <div class="form-group">
                <label>Adresse d'expédition</label>
                <input type="email" name="smtp_from" value="<?= esc(post('smtp_from')) ?>" placeholder="contact@monsite.fr">
              </div>
              <div class="form-group">
                <label>Nom d'expéditeur</label>
                <input type="text" name="smtp_from_name" value="<?= esc(post('smtp_from_name')) ?>" placeholder="Mon CRM">
              </div>
            </div>

            <div class="btn-row">
              <a href="?step=2" class="btn btn-ghost">← Retour</a>
              <button type="submit" class="btn btn-primary">Continuer →</button>
            </div>
          </form>

        <?php
          //---------------------------------------------------------
          // STEP 4 => API KEY (OPTIONAL)
          //---------------------------------------------------------
        ?>
        <?php elseif ($curStep === 4): ?>

          <div class="section-tag">Étape 4 / 5</div>
          <div class="section-title">APIs & Clés</div>
          <p class="section-desc">Google Maps, Anthropic et OpenAI. Toutes optionnelles — laissez vide si non utilisé.</p>

          <form method="POST">
            <input type="hidden" name="step" value="4">
            <?= hiddenFields($allPost, ['step','gmap_key','gmap_id','anthropic_key','openai_key']) ?>

            <div class="divider">Google Maps</div>
            <div class="form-grid">
              <div class="form-group">
                <label>API Key <span class="opt">(optionnel)</span></label>
                <input type="text" name="gmap_key" value="<?= esc(post('gmap_key')) ?>" placeholder="AIzaSy...">
              </div>
              <div class="form-group">
                <label>Map ID <span class="opt">(optionnel)</span></label>
                <input type="text" name="gmap_id" value="<?= esc(post('gmap_id')) ?>" placeholder="map_id...">
              </div>
            </div>

            <div class="divider">Intelligence artificielle</div>
            <div class="form-grid">
              <div class="form-group">
                <label>Anthropic API Key <span class="opt">(optionnel)</span></label>
                <input type="password" name="anthropic_key" value="<?= esc(post('anthropic_key')) ?>" placeholder="sk-ant-...">
              </div>
              <div class="form-group">
                <label>OpenAI API Key <span class="opt">(optionnel)</span></label>
                <input type="password" name="openai_key" value="<?= esc(post('openai_key')) ?>" placeholder="sk-...">
              </div>
            </div>

            <div class="btn-row">
              <a href="?step=3" class="btn btn-ghost">← Retour</a>
              <button type="submit" class="btn btn-primary">Continuer →</button>
            </div>
          </form>

        <?php
          //---------------------------------------------------------
          // STEP 5 => ADMIN LOGS
          //---------------------------------------------------------
        ?>
        <?php elseif ($curStep === 5): ?>


          <div class="section-tag">Étape 5 / 5</div>
          <div class="section-title">Accès Back-Office</div>

          <form method="POST">
            <input type="hidden" name="step" value="5">
            <?= hiddenFields($allPost, ['step','admin_email','admin_pass']) ?>

            <div class="form-grid">
              <div class="form-group">
                <label>Email <span class="req">*</span></label>
                <input type="text" name="admin_email" value="<?= esc(post('admin_email')) ?>" placeholder="adresse@monsite.fr">
              </div>
              <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="admin_pass" value="<?= esc(post('admin_pass')) ?>" placeholder="••••••••">
              </div>
            </div>

            <div class="btn-row">
              <a href="?step=4" class="btn btn-ghost">← Retour</a>
              <button type="submit" class="btn btn-primary">Installer →</button>
            </div>
          </form>

        <?php
          //---------------------------------------------------------
          // STEP 5 => ADMIN LOGS
          //---------------------------------------------------------
        ?>
        <?php elseif ($curStep === 6): ?>

          <div class="success-wrap">
            <div class="success-icon">✓</div>
            <div class="section-tag">Installation terminée</div>
            <div class="section-title">Luma CRM est prêt.</div>
            <p class="section-desc">Votre <code>config.php</code> a été écrit avec succès.</p>

            <ul class="checklist">
              <li>config.php généré</li>
              <li>Base de données connectée</li>
              <?php if (post('import_sql') === '1'): ?>
              <li>Structure SQL importée (config/luma.sql)</li>
              <?php endif; ?>
              <?php if (post('smtp_host')): ?>
              <li>SMTP configuré</li>
              <?php endif; ?>
              <?php if (post('anthropic_key') || post('openai_key')): ?>
              <li>Clés API enregistrées</li>
              <?php endif; ?>
              <li>Fichier verrou créé (config/installed.lock)</li>
            </ul>

            <div class="warning-box">
              <strong>⚠ Sécurité — action requise</strong>
              Supprimez le fichier <code>launch.php</code> de votre serveur immédiatement.<br>
              Il contient vos identifiants en clair dans les champs cachés tant qu'il est accessible.
            </div>

            <div class="btn-row">
              <a href="/manager" class="btn btn-primary">Accéder au Back Office →</a>
              <a href="/" class="btn btn-primary">Accéder au CRM →</a>
            </div>
          </div>

        <?php endif; ?>

      </main>
    </div>
  </body>
</html>