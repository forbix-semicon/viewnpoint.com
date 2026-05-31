<?php
declare(strict_types=1);

require __DIR__ . "/bootstrap.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Method not allowed.");
}

vp_comments_check_csrf();

$action = (string) ($_POST["action"] ?? "");
$return = vp_comments_safe_return("/");

try {
    switch ($action) {
        case "register":
            $name = trim((string) ($_POST["display_name"] ?? ""));
            $email = trim((string) ($_POST["email"] ?? ""));
            $password = (string) ($_POST["password"] ?? "");

            if ($name === "" || strlen($name) > 80) {
                throw new RuntimeException("Please enter a valid name.");
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new RuntimeException("Please enter a valid email address.");
            }
            if (strlen($password) < 6) {
                throw new RuntimeException("Password must be at least 6 characters.");
            }

            $stmt = vp_comments_db()->prepare(
                "INSERT INTO users (username, email, display_name, password_hash, role, status, created_at)
                 VALUES (NULL, :email, :display_name, :password_hash, 'user', 'active', :created_at)"
            );
            $stmt->execute([
                ":email" => $email,
                ":display_name" => $name,
                ":password_hash" => password_hash($password, PASSWORD_DEFAULT),
                ":created_at" => gmdate("c"),
            ]);

            $user = vp_comments_find_user($email);
            if ($user) {
                vp_comments_login_user($user);
            }
            vp_comments_backup();
            vp_comments_flash("Account created. You can now submit comments for approval.", "success");
            break;

        case "login":
            $identifier = trim((string) ($_POST["identifier"] ?? ""));
            $password = (string) ($_POST["password"] ?? "");
            $user = vp_comments_find_user($identifier);

            if (!$user) {
                throw new RuntimeException("Invalid login details.");
            }
            if (vp_comments_user_uses_oauth($user)) {
                throw new RuntimeException("This account uses Google sign-in. Click Sign in with Google.");
            }
            if (!password_verify($password, (string) $user["password_hash"])) {
                throw new RuntimeException("Invalid login details.");
            }
            if (($user["status"] ?? "") === "blocked") {
                throw new RuntimeException("This account is blocked.");
            }

            vp_comments_login_user($user);
            vp_comments_flash("Logged in successfully.", "success");
            break;

        case "logout":
            unset($_SESSION["vp_comments_user_id"]);
            vp_comments_flash("Logged out.", "info");
            break;

        case "comment":
            $user = vp_comments_user();
            if (!$user) {
                throw new RuntimeException("Please login before commenting.");
            }
            if (($user["status"] ?? "") === "blocked") {
                throw new RuntimeException("This account is blocked from commenting.");
            }

            $body = trim((string) ($_POST["body"] ?? ""));
            $postPath = trim((string) ($_POST["post_path"] ?? ""));
            $postTitle = trim((string) ($_POST["post_title"] ?? ""));

            if ($postPath === "" || $postTitle === "") {
                throw new RuntimeException("Invalid blog post.");
            }
            if (strlen($body) < 3) {
                throw new RuntimeException("Comment is too short.");
            }
            if (strlen($body) > 2000) {
                throw new RuntimeException("Comment is too long.");
            }

            $stmt = vp_comments_db()->prepare(
                "INSERT INTO comments (post_path, post_title, user_id, body, status, ip_address, user_agent, created_at)
                 VALUES (:post_path, :post_title, :user_id, :body, 'pending', :ip_address, :user_agent, :created_at)"
            );
            $stmt->execute([
                ":post_path" => $postPath,
                ":post_title" => $postTitle,
                ":user_id" => (int) $user["id"],
                ":body" => $body,
                ":ip_address" => (string) ($_SERVER["REMOTE_ADDR"] ?? ""),
                ":user_agent" => substr((string) ($_SERVER["HTTP_USER_AGENT"] ?? ""), 0, 255),
                ":created_at" => gmdate("c"),
            ]);

            vp_comments_backup();
            vp_comments_flash("Comment submitted. It will appear after moderator approval.", "success");
            break;

        default:
            throw new RuntimeException("Unsupported action.");
    }
} catch (PDOException $e) {
    $message = strpos($e->getMessage(), "UNIQUE") !== false ? "This email is already registered." : "Database error.";
    vp_comments_flash($message, "error");
} catch (Throwable $e) {
    vp_comments_flash($e->getMessage(), "error");
}

vp_comments_redirect($return);
