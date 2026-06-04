<?php

declare(strict_types=1);

session_start();

require __DIR__ . '/config.inc.php';

/*
|--------------------------------------------------------------------------
| Vérification extension GD
|--------------------------------------------------------------------------
*/

if (!extension_loaded('gd')) {
    http_response_code(500);
    exit('Extension GD non disponible.');
}

/*
|--------------------------------------------------------------------------
| Chargement des polices TTF (IMPORTANT FIX PHP 8.x)
|--------------------------------------------------------------------------
*/

$fontDir = __DIR__ . '/fonts';
$tfont = glob($fontDir . '/*.ttf') ?: [];

if (empty($tfont)) {
    // fallback système Linux (DejaVu)
    $fallback = '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf';

    if (is_file($fallback)) {
        $tfont = [$fallback];
    } else {
        http_response_code(500);
        exit('Aucune police TTF disponible.');
    }
}

/*
|--------------------------------------------------------------------------
| Anti brute force
|--------------------------------------------------------------------------
*/

$maxAttempts = 8;
$resetDelay  = 900;

$_SESSION['captcha_attempts'] ??= 0;
$_SESSION['captcha_attempts_time'] ??= time();

if ((time() - (int)$_SESSION['captcha_attempts_time']) > $resetDelay) {
    $_SESSION['captcha_attempts'] = 0;
    $_SESSION['captcha_attempts_time'] = time();
}

if ((int)$_SESSION['captcha_attempts'] >= $maxAttempts) {

    $errorImage = $_SERVER['DOCUMENT_ROOT'] . '/includes/verification/images/erreur1.png';

    if (is_file($errorImage)) {
        header('Content-Type: image/png');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        readfile($errorImage);
        exit;
    }

    http_response_code(429);
    exit('CAPTCHA bloqué temporairement.');
}

$_SESSION['captcha_attempts']++;

/*
|--------------------------------------------------------------------------
| Création image
|--------------------------------------------------------------------------
*/

$imgTmp = imagecreatetruecolor($cryptwidth, $cryptheight);

if (!$imgTmp) {
    http_response_code(500);
    exit('Impossible de créer l’image.');
}

$white = imagecolorallocate($imgTmp, 255, 255, 255);
$black = imagecolorallocate($imgTmp, 0, 0, 0);

imagefill($imgTmp, 0, 0, $white);

/*
|--------------------------------------------------------------------------
| Génération du mot CAPTCHA
|--------------------------------------------------------------------------
*/

$word = '';
$x = 10;
$pair = false;

$charNb = random_int($charnbmin, $charnbmax);
$letters = [];

for ($i = 0; $i < $charNb; $i++) {

    // sélection police SAFE
    $font = $tfont[array_rand($tfont)];

    if (!is_file($font) || !is_readable($font)) {
        continue;
    }

    $angle = random_int(-$charanglemax, $charanglemax);

    if ($crypteasy) {
        $chars = $pair ? $charelv : $charelc;
        $element = $chars[random_int(0, strlen($chars) - 1)];
    } else {
        $element = $charel[random_int(0, strlen($charel) - 1)];
    }

    $pair = !$pair;

    $size = random_int($charsizemin, $charsizemax);
    $y = (int)($cryptheight / 1.4);

    $word .= $element;

    $letters[] = compact('element', 'font', 'angle', 'size', 'x', 'y');

    imagettftext(
        $imgTmp,
        $size,
        $angle,
        $x,
        $y,
        $black,
        $font,
        $element
    );

    $x += $charspace;
}

imagedestroy($imgTmp);

/*
|--------------------------------------------------------------------------
| Image finale
|--------------------------------------------------------------------------
*/

$img = imagecreatetruecolor($cryptwidth, $cryptheight);

if (!$img) {
    http_response_code(500);
    exit('Impossible de créer l’image finale.');
}

$bg = imagecolorallocate($img, $bgR, $bgG, $bgB);
imagefill($img, 0, 0, $bg);

$ink = imagecolorallocate($img, $charR, $charG, $charB);

/*
|--------------------------------------------------------------------------
| Dessin final propre (stable PHP 8.x)
|--------------------------------------------------------------------------
*/

foreach ($letters as $letter) {

    imagettftext(
        $img,
        (int)$letter['size'],
        (int)$letter['angle'],
        (int)$letter['x'],
        (int)$letter['y'],
        $ink,
        $letter['font'],
        $letter['element']
    );
}

/*
|--------------------------------------------------------------------------
| Bruit
|--------------------------------------------------------------------------
*/

for ($i = 0; $i < random_int($noisepxmin, $noisepxmax); $i++) {
    imagesetpixel(
        $img,
        random_int(0, $cryptwidth - 1),
        random_int(0, $cryptheight - 1),
        $ink
    );
}

for ($i = 0; $i < random_int($noiselinemin, $noiselinemax); $i++) {
    imageline(
        $img,
        random_int(0, $cryptwidth),
        random_int(0, $cryptheight),
        random_int(0, $cryptwidth),
        random_int(0, $cryptheight),
        $ink
    );
}

/*
|--------------------------------------------------------------------------
| Session CAPTCHA
|--------------------------------------------------------------------------
*/

$_SESSION['captcha'] = hash('sha256', $word);
$_SESSION['captcha_time'] = time();

/*
|--------------------------------------------------------------------------
| Output
|--------------------------------------------------------------------------
*/

header('Content-Type: image/png');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

imagepng($img);
imagedestroy($img);

exit;