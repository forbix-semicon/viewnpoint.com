<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$vpCommentsConfig = [
    "admin_username" => "admin",
    "admin_email" => "moderator@viewnpoint.com",
    "admin_password" => "change-this-password",
    "google_enabled" => false,
    "google_client_id" => "",
    "google_client_secret" => "",
    "google_redirect_uri" => "",
    "facebook_enabled" => false,
    "facebook_app_id" => "",
    "facebook_app_secret" => "",
    "facebook_redirect_uri" => "",
];
$vpCommentsConfigPath = __DIR__ . "/config.php";
if (is_file($vpCommentsConfigPath)) {
    $loadedConfig = require $vpCommentsConfigPath;
    if (is_array($loadedConfig)) {
        $vpCommentsConfig = array_merge($vpCommentsConfig, $loadedConfig);
    }
}

define("VP_COMMENT_DB", __DIR__ . "/../db/viewnpoint_comments.sqlite");
define("VP_COMMENT_BACKUP_DB", __DIR__ . "/../db/viewnpoint_comments_backup.sqlite");
define("VP_USERS_CSV", __DIR__ . "/../db/viewnpoint_users.csv");
define("VP_COMMENTS_CSV", __DIR__ . "/../db/viewnpoint_comments.csv");
define("VP_ADMIN_USERNAME", (string) $vpCommentsConfig["admin_username"]);
define("VP_ADMIN_EMAIL", (string) $vpCommentsConfig["admin_email"]);
define("VP_ADMIN_PASSWORD", (string) $vpCommentsConfig["admin_password"]);

function vp_comments_config(): array
{
    global $vpCommentsConfig;

    return $vpCommentsConfig;
}

function vp_comments_site_base(): string
{
    $https = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off")
        || (($_SERVER["HTTP_PORT"] ?? "") === "443");
    $host = $_SERVER["HTTP_HOST"] ?? "localhost";
    $basePath = rtrim(str_replace("\\", "/", dirname(dirname($_SERVER["SCRIPT_NAME"] ?? ""))), "/");
    if ($basePath === "/" || $basePath === ".") {
        $basePath = "";
    }

    return ($https ? "https" : "http") . "://" . $host . $basePath;
}

function vp_comments_google_redirect_uri(): string
{
    $cfg = vp_comments_config();
    $custom = trim((string) ($cfg["google_redirect_uri"] ?? ""));
    if ($custom !== "") {
        return $custom;
    }

    return vp_comments_site_base() . "/comments/oauth_google_callback.php";
}

function vp_comments_google_enabled(): bool
{
    $cfg = vp_comments_config();
    $id = trim((string) ($cfg["google_client_id"] ?? ""));
    $secret = trim((string) ($cfg["google_client_secret"] ?? ""));

    return !empty($cfg["google_enabled"])
        && $id !== ""
        && $secret !== ""
        && stripos($id, "PASTE_") !== 0
        && stripos($secret, "PASTE_") !== 0;
}

function vp_comments_http_post_form(string $url, array $fields): array
{
    $body = http_build_query($fields);

    if (function_exists("curl_init")) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ["Content-Type: application/x-www-form-urlencoded"],
            CURLOPT_TIMEOUT => 30,
        ]);
        $response = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($response === false) {
            throw new RuntimeException("HTTP request failed.");
        }
        $decoded = json_decode($response, true);
        if (!is_array($decoded)) {
            throw new RuntimeException("Invalid response from remote server.");
        }
        if ($status >= 400) {
            $msg = (string) ($decoded["error_description"] ?? $decoded["error"] ?? "HTTP $status");
            throw new RuntimeException($msg);
        }

        return $decoded;
    }

    $context = stream_context_create([
        "http" => [
            "method" => "POST",
            "header" => "Content-Type: application/x-www-form-urlencoded\r\n",
            "content" => $body,
            "timeout" => 30,
            "ignore_errors" => true,
        ],
    ]);
    $response = file_get_contents($url, false, $context);
    if ($response === false) {
        throw new RuntimeException("HTTP request failed.");
    }
    $decoded = json_decode($response, true);
    if (!is_array($decoded)) {
        throw new RuntimeException("Invalid response from remote server.");
    }

    return $decoded;
}

function vp_comments_http_get_json(string $url, string $accessToken): array
{
    if (function_exists("curl_init")) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ["Authorization: Bearer " . $accessToken],
            CURLOPT_TIMEOUT => 30,
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        if ($response === false) {
            throw new RuntimeException("Failed to load Google profile.");
        }
        $decoded = json_decode($response, true);
        if (!is_array($decoded)) {
            throw new RuntimeException("Invalid Google profile response.");
        }

        return $decoded;
    }

    $context = stream_context_create([
        "http" => [
            "method" => "GET",
            "header" => "Authorization: Bearer " . $accessToken . "\r\n",
            "timeout" => 30,
            "ignore_errors" => true,
        ],
    ]);
    $response = file_get_contents($url, false, $context);
    if ($response === false) {
        throw new RuntimeException("Failed to load Google profile.");
    }
    $decoded = json_decode($response, true);
    if (!is_array($decoded)) {
        throw new RuntimeException("Invalid Google profile response.");
    }

    return $decoded;
}

function vp_comments_google_fetch_profile(string $code): array
{
    $cfg = vp_comments_config();
    $token = vp_comments_http_post_form("https://oauth2.googleapis.com/token", [
        "code" => $code,
        "client_id" => trim((string) $cfg["google_client_id"]),
        "client_secret" => trim((string) $cfg["google_client_secret"]),
        "redirect_uri" => vp_comments_google_redirect_uri(),
        "grant_type" => "authorization_code",
    ]);

    $accessToken = (string) ($token["access_token"] ?? "");
    if ($accessToken === "") {
        throw new RuntimeException("Google did not return an access token.");
    }

    $profile = vp_comments_http_get_json("https://openidconnect.googleapis.com/v1/userinfo", $accessToken);
    $sub = (string) ($profile["sub"] ?? "");
    $email = trim((string) ($profile["email"] ?? ""));
    $name = trim((string) ($profile["name"] ?? ""));

    if ($sub === "" || $email === "") {
        throw new RuntimeException("Google account email is required.");
    }

    return [
        "sub" => $sub,
        "email" => $email,
        "name" => $name !== "" ? $name : $email,
    ];
}

function vp_comments_login_google_user(array $profile): void
{
    $pdo = vp_comments_db();

    $stmt = $pdo->prepare(
        "SELECT * FROM users WHERE oauth_provider = 'google' AND oauth_id = :oauth_id LIMIT 1"
    );
    $stmt->execute([":oauth_id" => $profile["sub"]]);
    $user = $stmt->fetch();

    if (!$user) {
        $emailStmt = $pdo->prepare("SELECT * FROM users WHERE lower(email) = lower(:email) LIMIT 1");
        $emailStmt->execute([":email" => $profile["email"]]);
        $existing = $emailStmt->fetch();

        if ($existing) {
            if (($existing["oauth_provider"] ?? "local") === "google" && ($existing["oauth_id"] ?? "") === $profile["sub"]) {
                $user = $existing;
            } elseif (($existing["oauth_provider"] ?? "local") === "local") {
                throw new RuntimeException(
                    "This email already has a password account. Log in with email/password, or use a different Google account."
                );
            } else {
                throw new RuntimeException("This email is already registered with another sign-in method.");
            }
        }
    }

    if (!$user) {
        $insert = $pdo->prepare(
            "INSERT INTO users (username, email, display_name, password_hash, role, status, oauth_provider, oauth_id, created_at)
             VALUES (NULL, :email, :display_name, :password_hash, 'user', 'active', 'google', :oauth_id, :created_at)"
        );
        $insert->execute([
            ":email" => $profile["email"],
            ":display_name" => $profile["name"],
            ":password_hash" => "oauth:google",
            ":oauth_id" => $profile["sub"],
            ":created_at" => gmdate("c"),
        ]);
        $stmt = $pdo->prepare(
            "SELECT * FROM users WHERE oauth_provider = 'google' AND oauth_id = :oauth_id LIMIT 1"
        );
        $stmt->execute([":oauth_id" => $profile["sub"]]);
        $user = $stmt->fetch();
    }

    if (!$user) {
        throw new RuntimeException("Could not create Google user account.");
    }
    if (($user["status"] ?? "") === "blocked") {
        throw new RuntimeException("This account is blocked.");
    }

    vp_comments_login_user($user);
    vp_comments_backup();
}

function vp_comments_user_uses_oauth(array $user): bool
{
    return ($user["oauth_provider"] ?? "local") !== "local"
        || strpos((string) ($user["password_hash"] ?? ""), "oauth:") === 0;
}

function vp_comments_h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
}

function vp_comments_db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dbDir = dirname(VP_COMMENT_DB);
    if (!is_dir($dbDir)) {
        mkdir($dbDir, 0775, true);
    }

    $pdo = new PDO("sqlite:" . VP_COMMENT_DB);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec("PRAGMA foreign_keys = ON");
    vp_comments_install($pdo);

    return $pdo;
}

function vp_comments_install(PDO $pdo): void
{
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE,
            email TEXT NOT NULL UNIQUE,
            display_name TEXT NOT NULL,
            password_hash TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT 'user',
            status TEXT NOT NULL DEFAULT 'active',
            created_at TEXT NOT NULL,
            last_login_at TEXT
        )"
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS comments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            post_path TEXT NOT NULL,
            post_title TEXT NOT NULL,
            user_id INTEGER NOT NULL,
            body TEXT NOT NULL,
            status TEXT NOT NULL DEFAULT 'pending',
            ip_address TEXT,
            user_agent TEXT,
            created_at TEXT NOT NULL,
            moderated_at TEXT,
            moderated_by INTEGER,
            FOREIGN KEY (user_id) REFERENCES users(id),
            FOREIGN KEY (moderated_by) REFERENCES users(id)
        )"
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS moderation_log (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            admin_id INTEGER NOT NULL,
            target_type TEXT NOT NULL,
            target_id INTEGER NOT NULL,
            action TEXT NOT NULL,
            note TEXT,
            created_at TEXT NOT NULL,
            FOREIGN KEY (admin_id) REFERENCES users(id)
        )"
    );

    vp_comments_migrate_users_oauth($pdo);

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email LIMIT 1");
    $stmt->execute([
        ":username" => VP_ADMIN_USERNAME,
        ":email" => VP_ADMIN_EMAIL,
    ]);

    if (!$stmt->fetch()) {
        $insert = $pdo->prepare(
            "INSERT INTO users (username, email, display_name, password_hash, role, status, created_at)
             VALUES (:username, :email, :display_name, :password_hash, 'admin', 'active', :created_at)"
        );
        $insert->execute([
            ":username" => VP_ADMIN_USERNAME,
            ":email" => VP_ADMIN_EMAIL,
            ":display_name" => "ViewNPoint Moderator",
            ":password_hash" => password_hash(VP_ADMIN_PASSWORD, PASSWORD_DEFAULT),
            ":created_at" => gmdate("c"),
        ]);
    }

    if (!is_file(VP_COMMENT_BACKUP_DB) || !is_file(VP_USERS_CSV) || !is_file(VP_COMMENTS_CSV)) {
        vp_comments_backup();
    }
}

function vp_comments_migrate_users_oauth(PDO $pdo): void
{
    $columns = [];
    foreach ($pdo->query("PRAGMA table_info(users)") as $row) {
        $columns[(string) $row["name"]] = true;
    }
    if (!isset($columns["oauth_provider"])) {
        $pdo->exec("ALTER TABLE users ADD COLUMN oauth_provider TEXT NOT NULL DEFAULT 'local'");
    }
    if (!isset($columns["oauth_id"])) {
        $pdo->exec("ALTER TABLE users ADD COLUMN oauth_id TEXT");
    }
}

function vp_comments_csrf(): string
{
    if (empty($_SESSION["vp_comments_csrf"])) {
        $_SESSION["vp_comments_csrf"] = bin2hex(random_bytes(32));
    }

    return $_SESSION["vp_comments_csrf"];
}

function vp_comments_check_csrf(): void
{
    $token = (string) ($_POST["csrf"] ?? "");
    if ($token === "" || !hash_equals(vp_comments_csrf(), $token)) {
        http_response_code(400);
        exit("Invalid request token.");
    }
}

function vp_comments_user(): ?array
{
    if (empty($_SESSION["vp_comments_user_id"])) {
        return null;
    }

    $stmt = vp_comments_db()->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
    $stmt->execute([":id" => (int) $_SESSION["vp_comments_user_id"]]);
    $user = $stmt->fetch();

    return $user ?: null;
}

function vp_comments_is_admin(?array $user = null): bool
{
    $user = $user ?? vp_comments_user();

    return is_array($user) && ($user["role"] ?? "") === "admin" && ($user["status"] ?? "") === "active";
}

function vp_comments_login_user(array $user): void
{
    $_SESSION["vp_comments_user_id"] = (int) $user["id"];
    $stmt = vp_comments_db()->prepare("UPDATE users SET last_login_at = :last_login_at WHERE id = :id");
    $stmt->execute([
        ":last_login_at" => gmdate("c"),
        ":id" => (int) $user["id"],
    ]);
}

function vp_comments_flash(string $message, string $type = "info"): void
{
    $_SESSION["vp_comments_flash"] = [
        "message" => $message,
        "type" => $type,
    ];
}

function vp_comments_take_flash(): ?array
{
    $flash = $_SESSION["vp_comments_flash"] ?? null;
    unset($_SESSION["vp_comments_flash"]);

    return is_array($flash) ? $flash : null;
}

function vp_comments_safe_return(string $fallback = "/blog"): string
{
    $return = (string) ($_POST["return"] ?? $_GET["return"] ?? $fallback);
    if ($return === "" || preg_match('/[\r\n]/', $return)) {
        return $fallback;
    }

    return $return;
}

function vp_comments_redirect(string $to): void
{
    header("Location: " . $to);
    exit;
}

function vp_comments_find_user(string $identifier): ?array
{
    $stmt = vp_comments_db()->prepare(
        "SELECT * FROM users WHERE lower(email) = lower(:identifier) OR lower(username) = lower(:identifier) LIMIT 1"
    );
    $stmt->execute([":identifier" => trim($identifier)]);
    $user = $stmt->fetch();

    return $user ?: null;
}

function vp_comments_public_for_post(string $postPath): array
{
    $stmt = vp_comments_db()->prepare(
        "SELECT c.*, u.display_name
         FROM comments c
         JOIN users u ON u.id = c.user_id
         WHERE c.post_path = :post_path AND c.status = 'approved' AND u.status = 'active'
         ORDER BY c.created_at ASC"
    );
    $stmt->execute([":post_path" => $postPath]);

    return $stmt->fetchAll();
}

function vp_comments_recent_for_admin(): array
{
    $stmt = vp_comments_db()->query(
        "SELECT c.*, u.email, u.username, u.display_name, u.status AS user_status
         FROM comments c
         JOIN users u ON u.id = c.user_id
         ORDER BY
            CASE c.status WHEN 'pending' THEN 0 WHEN 'approved' THEN 1 WHEN 'disapproved' THEN 2 ELSE 3 END,
            c.created_at DESC"
    );

    return $stmt->fetchAll();
}

function vp_comments_users_for_admin(): array
{
    $stmt = vp_comments_db()->query(
        "SELECT id, username, email, display_name, role, status, created_at, last_login_at
         FROM users
         ORDER BY created_at DESC"
    );

    return $stmt->fetchAll();
}

function vp_comments_log(int $adminId, string $targetType, int $targetId, string $action, string $note = ""): void
{
    $stmt = vp_comments_db()->prepare(
        "INSERT INTO moderation_log (admin_id, target_type, target_id, action, note, created_at)
         VALUES (:admin_id, :target_type, :target_id, :action, :note, :created_at)"
    );
    $stmt->execute([
        ":admin_id" => $adminId,
        ":target_type" => $targetType,
        ":target_id" => $targetId,
        ":action" => $action,
        ":note" => $note,
        ":created_at" => gmdate("c"),
    ]);
}

function vp_comments_backup(): void
{
    if (is_file(VP_COMMENT_DB)) {
        copy(VP_COMMENT_DB, VP_COMMENT_BACKUP_DB);
    }

    vp_comments_export_csv(
        VP_USERS_CSV,
        ["id", "username", "email", "display_name", "role", "status", "created_at", "last_login_at"],
        "SELECT id, username, email, display_name, role, status, created_at, last_login_at FROM users ORDER BY id ASC"
    );
    vp_comments_export_csv(
        VP_COMMENTS_CSV,
        ["id", "post_path", "post_title", "user_id", "email", "display_name", "body", "status", "ip_address", "created_at", "moderated_at", "moderated_by"],
        "SELECT c.id, c.post_path, c.post_title, c.user_id, u.email, u.display_name, c.body, c.status, c.ip_address, c.created_at, c.moderated_at, c.moderated_by
         FROM comments c
         JOIN users u ON u.id = c.user_id
         ORDER BY c.id ASC"
    );
}

function vp_comments_export_csv(string $path, array $headers, string $query): void
{
    $handle = fopen($path, "wb");
    if ($handle === false) {
        return;
    }

    fputcsv($handle, $headers);
    foreach (vp_comments_db()->query($query) as $row) {
        $line = [];
        foreach ($headers as $header) {
            $line[] = (string) ($row[$header] ?? "");
        }
        fputcsv($handle, $line);
    }

    fclose($handle);
}

function vp_comments_current_url(): string
{
    $scheme = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? "https" : "http";
    $host = $_SERVER["HTTP_HOST"] ?? "localhost";
    $uri = $_SERVER["REQUEST_URI"] ?? "/";

    return $scheme . "://" . $host . $uri;
}

function vp_render_comments(array $post, string $basePath): void
{
    try {
        $user = vp_comments_user();
        $approved = vp_comments_public_for_post((string) $post["path"]);
        $flash = vp_comments_take_flash();
    } catch (Throwable $e) {
        ?>
        <section class="comments-box">
            <h2>Comments</h2>
            <p class="comment-error">Comments are not available: <?= vp_comments_h($e->getMessage()) ?></p>
        </section>
        <?php
        return;
    }

    $actionUrl = ($basePath === "" ? "" : $basePath) . "/comments/action.php";
    $returnUrl = vp_comments_current_url();
    ?>
    <section class="comments-box" id="comments">
        <h2>Comments</h2>
        <p class="comments-note">Write your view after login. Your comment appears only after the moderator approves it.</p>

        <?php if ($flash): ?>
            <div class="comment-flash comment-flash-<?= vp_comments_h((string) $flash["type"]) ?>">
                <?= vp_comments_h((string) $flash["message"]) ?>
            </div>
        <?php endif; ?>

        <?php if ($approved): ?>
            <div class="comment-list">
                <?php foreach ($approved as $comment): ?>
                    <article class="comment-item">
                        <div class="comment-meta">
                            <strong><?= vp_comments_h((string) $comment["display_name"]) ?></strong>
                            <span><?= vp_comments_h(date("j M Y, g:i A", strtotime((string) $comment["created_at"]))) ?></span>
                        </div>
                        <p><?= nl2br(vp_comments_h((string) $comment["body"])) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="comments-empty">No approved comments yet. Be the first to share a view.</p>
        <?php endif; ?>

        <?php if ($user && ($user["status"] ?? "") === "active"): ?>
            <form class="comment-form" method="post" action="<?= vp_comments_h($actionUrl) ?>">
                <input type="hidden" name="csrf" value="<?= vp_comments_h(vp_comments_csrf()) ?>">
                <input type="hidden" name="action" value="comment">
                <input type="hidden" name="return" value="<?= vp_comments_h($returnUrl) ?>">
                <input type="hidden" name="post_path" value="<?= vp_comments_h((string) $post["path"]) ?>">
                <input type="hidden" name="post_title" value="<?= vp_comments_h((string) $post["title"]) ?>">
                <label for="comment-body-<?= vp_comments_h((string) $post["slug"]) ?>">Write a comment</label>
                <textarea id="comment-body-<?= vp_comments_h((string) $post["slug"]) ?>" name="body" rows="5" maxlength="2000" required></textarea>
                <button class="btn btn-primary" type="submit">Submit For Approval</button>
                <p class="comments-note">
                    Logged in as <?= vp_comments_h((string) $user["display_name"]) ?>.
                    <button class="link-button" type="submit" name="action" value="logout" formnovalidate>Logout</button>
                    <?php if (vp_comments_is_admin($user)): ?>
                        · <a href="<?= vp_comments_h(($basePath === "" ? "" : $basePath) . "/comments/admin.php") ?>">Moderation</a>
                    <?php endif; ?>
                </p>
            </form>
        <?php elseif ($user && ($user["status"] ?? "") === "blocked"): ?>
            <p class="comment-error">Your account is blocked from commenting.</p>
        <?php else: ?>
            <?php if (vp_comments_google_enabled()): ?>
                <?php
                $googleStart = ($basePath === "" ? "" : $basePath)
                    . "/comments/oauth_google.php?return="
                    . rawurlencode($returnUrl);
                ?>
                <div class="comment-oauth-row">
                    <a class="btn btn-google" href="<?= vp_comments_h($googleStart) ?>">Sign in with Google</a>
                </div>
                <p class="comments-note oauth-divider">Or use email and password below.</p>
            <?php endif; ?>
            <div class="comment-auth-grid">
                <form class="comment-form" method="post" action="<?= vp_comments_h($actionUrl) ?>">
                    <h3>Login</h3>
                    <input type="hidden" name="csrf" value="<?= vp_comments_h(vp_comments_csrf()) ?>">
                    <input type="hidden" name="action" value="login">
                    <input type="hidden" name="return" value="<?= vp_comments_h($returnUrl) ?>">
                    <label>Email or username</label>
                    <input type="text" name="identifier" autocomplete="username" required>
                    <label>Password</label>
                    <input type="password" name="password" autocomplete="current-password" required>
                    <button class="btn btn-primary" type="submit">Login</button>
                </form>

                <form class="comment-form" method="post" action="<?= vp_comments_h($actionUrl) ?>">
                    <h3>Create Account</h3>
                    <input type="hidden" name="csrf" value="<?= vp_comments_h(vp_comments_csrf()) ?>">
                    <input type="hidden" name="action" value="register">
                    <input type="hidden" name="return" value="<?= vp_comments_h($returnUrl) ?>">
                    <label>Name</label>
                    <input type="text" name="display_name" maxlength="80" required>
                    <label>Email ID (Gmail or any email)</label>
                    <input type="email" name="email" autocomplete="email" required>
                    <label>Password</label>
                    <input type="password" name="password" minlength="6" autocomplete="new-password" required>
                    <button class="btn btn-muted" type="submit">Create Account</button>
                </form>
            </div>
        <?php endif; ?>
    </section>
    <?php
}
