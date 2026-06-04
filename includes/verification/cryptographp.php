<?php session_start();

// Test cookie pour vérifier que le navigateur accepte les cookies
setcookie(
    "cryptcookietest",
    "1",
    [
        "expires" => time() + 300,
        "path" => "/",
        "secure" => isset($_SERVER['HTTPS']),
        "httponly" => true,
        "samesite" => "Lax"
    ]
);

// Redirection vers le générateur de CAPTCHA
header("Location: captcha_generate.php");
exit;