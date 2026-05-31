<?php
declare(strict_types=1);

require __DIR__ . "/bootstrap.php";

$cfg = vp_comments_config();
if (!vp_comments_google_enabled()) {
    http_response_code(503);
    exit("Google sign-in is not configured.");
}

$return = vp_comments_safe_return(vp_comments_site_base() . "/blog");
$state = bin2hex(random_bytes(16));
$_SESSION["vp_oauth_state"] = $state;
$_SESSION["vp_oauth_return"] = $return;

$params = [
    "client_id" => trim((string) $cfg["google_client_id"]),
    "redirect_uri" => vp_comments_google_redirect_uri(),
    "response_type" => "code",
    "scope" => "openid email profile",
    "state" => $state,
    "access_type" => "online",
    "prompt" => "select_account",
];

header("Location: https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query($params));
exit;
