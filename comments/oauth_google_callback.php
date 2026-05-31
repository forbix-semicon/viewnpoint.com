<?php
declare(strict_types=1);

require __DIR__ . "/bootstrap.php";

$return = (string) ($_SESSION["vp_oauth_return"] ?? vp_comments_site_base() . "/blog");

if (!vp_comments_google_enabled()) {
    vp_comments_flash("Google sign-in is not configured.", "error");
    vp_comments_redirect($return);
}

$error = (string) ($_GET["error"] ?? "");
if ($error !== "") {
    vp_comments_flash("Google sign-in was cancelled or failed.", "error");
    vp_comments_redirect($return);
}

$state = (string) ($_GET["state"] ?? "");
$expected = (string) ($_SESSION["vp_oauth_state"] ?? "");
unset($_SESSION["vp_oauth_state"]);

if ($state === "" || $expected === "" || !hash_equals($expected, $state)) {
    vp_comments_flash("Invalid Google sign-in session. Please try again.", "error");
    vp_comments_redirect($return);
}

$code = (string) ($_GET["code"] ?? "");
if ($code === "") {
    vp_comments_flash("Google did not return an authorization code.", "error");
    vp_comments_redirect($return);
}

try {
    $profile = vp_comments_google_fetch_profile($code);
    vp_comments_login_google_user($profile);
    vp_comments_flash("Signed in with Google.", "success");
} catch (Throwable $e) {
    vp_comments_flash("Google sign-in failed: " . $e->getMessage(), "error");
}

unset($_SESSION["vp_oauth_return"]);
vp_comments_redirect($return);
