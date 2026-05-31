<?php
declare(strict_types=1);

require __DIR__ . "/bootstrap.php";

$basePath = rtrim(str_replace("\\", "/", dirname(dirname($_SERVER["SCRIPT_NAME"] ?? ""))), "/");
if ($basePath === "/" || $basePath === ".") {
    $basePath = "";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    vp_comments_check_csrf();
    $action = (string) ($_POST["action"] ?? "");

    try {
        if ($action === "admin_login") {
            $identifier = trim((string) ($_POST["identifier"] ?? ""));
            $password = (string) ($_POST["password"] ?? "");
            $user = vp_comments_find_user($identifier);

            if (!$user || !password_verify($password, (string) $user["password_hash"]) || ($user["role"] ?? "") !== "admin") {
                throw new RuntimeException("Invalid admin login.");
            }

            vp_comments_login_user($user);
            vp_comments_flash("Admin login successful.", "success");
            vp_comments_redirect(($basePath === "" ? "" : $basePath) . "/comments/admin.php");
        }

        $admin = vp_comments_user();
        if (!vp_comments_is_admin($admin)) {
            throw new RuntimeException("Admin access required.");
        }

        $commentId = (int) ($_POST["comment_id"] ?? 0);
        $userId = (int) ($_POST["user_id"] ?? 0);

        if (in_array($action, ["approve", "disapprove", "delete"], true) && $commentId > 0) {
            $status = [
                "approve" => "approved",
                "disapprove" => "disapproved",
                "delete" => "deleted",
            ][$action];
            $stmt = vp_comments_db()->prepare(
                "UPDATE comments
                 SET status = :status, moderated_at = :moderated_at, moderated_by = :moderated_by
                 WHERE id = :id"
            );
            $stmt->execute([
                ":status" => $status,
                ":moderated_at" => gmdate("c"),
                ":moderated_by" => (int) $admin["id"],
                ":id" => $commentId,
            ]);
            vp_comments_log((int) $admin["id"], "comment", $commentId, $action);
            vp_comments_backup();
            vp_comments_flash("Comment " . $status . ".", "success");
        } elseif (in_array($action, ["block", "unblock"], true) && $userId > 0) {
            if ($userId === (int) $admin["id"]) {
                throw new RuntimeException("You cannot block your own admin account.");
            }

            $status = $action === "block" ? "blocked" : "active";
            $stmt = vp_comments_db()->prepare("UPDATE users SET status = :status WHERE id = :id AND role != 'admin'");
            $stmt->execute([
                ":status" => $status,
                ":id" => $userId,
            ]);
            vp_comments_log((int) $admin["id"], "user", $userId, $action);
            vp_comments_backup();
            vp_comments_flash("User " . $status . ".", "success");
        } elseif ($action === "logout") {
            unset($_SESSION["vp_comments_user_id"]);
            vp_comments_flash("Logged out.", "info");
        } else {
            throw new RuntimeException("Unsupported admin action.");
        }
    } catch (Throwable $e) {
        vp_comments_flash($e->getMessage(), "error");
    }

    vp_comments_redirect(($basePath === "" ? "" : $basePath) . "/comments/admin.php");
}

$admin = vp_comments_user();
$isAdmin = vp_comments_is_admin($admin);
$flash = vp_comments_take_flash();
$comments = $isAdmin ? vp_comments_recent_for_admin() : [];
$users = $isAdmin ? vp_comments_users_for_admin() : [];

function admin_action_button(string $label, string $action, int $commentId = 0, int $userId = 0): void
{
    ?>
    <form method="post" style="display:inline">
        <input type="hidden" name="csrf" value="<?= vp_comments_h(vp_comments_csrf()) ?>">
        <input type="hidden" name="action" value="<?= vp_comments_h($action) ?>">
        <?php if ($commentId > 0): ?>
            <input type="hidden" name="comment_id" value="<?= $commentId ?>">
        <?php endif; ?>
        <?php if ($userId > 0): ?>
            <input type="hidden" name="user_id" value="<?= $userId ?>">
        <?php endif; ?>
        <button type="submit"><?= vp_comments_h($label) ?></button>
    </form>
    <?php
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Comment Moderation | ViewNPoint</title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #f5f7fb; color: #151827; }
        .wrap { width: min(1180px, calc(100% - 2rem)); margin: 2rem auto; }
        .panel { background: #fff; border: 1px solid #dce2ee; border-radius: 12px; padding: 1rem; margin-bottom: 1rem; box-shadow: 0 10px 28px rgba(20, 25, 40, .08); }
        h1, h2 { margin-top: 0; }
        table { width: 100%; border-collapse: collapse; min-width: 900px; }
        th, td { text-align: left; padding: .7rem; border-bottom: 1px solid #e3e8f2; vertical-align: top; }
        th { background: #eef3ff; }
        textarea, input { width: 100%; padding: .7rem; margin: .35rem 0 .7rem; border: 1px solid #cfd7e6; border-radius: 8px; }
        button, .link { border: 0; border-radius: 8px; padding: .55rem .75rem; background: #5b6ee6; color: #fff; cursor: pointer; text-decoration: none; display: inline-block; margin: .15rem; }
        button:hover, .link:hover { background: #4658c7; }
        .danger button { background: #c03636; }
        .muted { color: #5d687a; }
        .status { display: inline-block; border-radius: 999px; padding: .2rem .55rem; font-size: .82rem; background: #eef3ff; }
        .flash { padding: .8rem 1rem; border-radius: 10px; margin-bottom: 1rem; background: #eef3ff; }
        .flash-error { background: #feecec; color: #8a1f1f; }
        .flash-success { background: #eaf8ee; color: #146b2f; }
        .table-scroll { overflow-x: auto; }
    </style>
</head>
<body>
<main class="wrap">
    <h1>ViewNPoint Comment Moderation</h1>
    <p><a class="link" href="<?= vp_comments_h(($basePath === "" ? "" : $basePath) . "/blog") ?>">Back To Blog</a></p>

    <?php if ($flash): ?>
        <div class="flash flash-<?= vp_comments_h((string) $flash["type"]) ?>">
            <?= vp_comments_h((string) $flash["message"]) ?>
        </div>
    <?php endif; ?>

    <?php if (!$isAdmin): ?>
        <section class="panel">
            <h2>Admin Login</h2>
            <p class="muted">Use username <strong>admin</strong> and the configured admin password.</p>
            <form method="post">
                <input type="hidden" name="csrf" value="<?= vp_comments_h(vp_comments_csrf()) ?>">
                <input type="hidden" name="action" value="admin_login">
                <label>Admin ID or email</label>
                <input type="text" name="identifier" value="admin" required>
                <label>Password</label>
                <input type="password" name="password" required>
                <button type="submit">Login</button>
            </form>
        </section>
    <?php else: ?>
        <section class="panel">
            <h2>Admin</h2>
            <p>Logged in as <?= vp_comments_h((string) $admin["display_name"]) ?> (<?= vp_comments_h((string) $admin["email"]) ?>).</p>
            <form method="post">
                <input type="hidden" name="csrf" value="<?= vp_comments_h(vp_comments_csrf()) ?>">
                <button type="submit" name="action" value="logout">Logout</button>
            </form>
        </section>

        <section class="panel">
            <h2>Comments</h2>
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Status</th>
                            <th>Post</th>
                            <th>User</th>
                            <th>Comment</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($comments as $comment): ?>
                        <tr>
                            <td><?= (int) $comment["id"] ?></td>
                            <td><span class="status"><?= vp_comments_h((string) $comment["status"]) ?></span></td>
                            <td><?= vp_comments_h((string) $comment["post_title"]) ?></td>
                            <td>
                                <?= vp_comments_h((string) $comment["display_name"]) ?><br>
                                <span class="muted"><?= vp_comments_h((string) $comment["email"]) ?></span><br>
                                <span class="muted">user: <?= vp_comments_h((string) $comment["user_status"]) ?></span>
                            </td>
                            <td><?= nl2br(vp_comments_h((string) $comment["body"])) ?></td>
                            <td><?= vp_comments_h((string) $comment["created_at"]) ?></td>
                            <td>
                                <?php admin_action_button("Approve", "approve", (int) $comment["id"]); ?>
                                <?php admin_action_button("Disapprove", "disapprove", (int) $comment["id"]); ?>
                                <span class="danger"><?php admin_action_button("Delete", "delete", (int) $comment["id"]); ?></span>
                                <?php if (($comment["user_status"] ?? "") === "blocked"): ?>
                                    <?php admin_action_button("Unblock User", "unblock", 0, (int) $comment["user_id"]); ?>
                                <?php else: ?>
                                    <span class="danger"><?php admin_action_button("Block User", "block", 0, (int) $comment["user_id"]); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$comments): ?>
                        <tr><td colspan="7">No comments yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="panel">
            <h2>Users</h2>
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= (int) $user["id"] ?></td>
                            <td><?= vp_comments_h((string) $user["display_name"]) ?></td>
                            <td><?= vp_comments_h((string) $user["email"]) ?></td>
                            <td><?= vp_comments_h((string) $user["role"]) ?></td>
                            <td><?= vp_comments_h((string) $user["status"]) ?></td>
                            <td><?= vp_comments_h((string) $user["created_at"]) ?></td>
                            <td><?= vp_comments_h((string) ($user["last_login_at"] ?? "")) ?></td>
                            <td>
                                <?php if (($user["role"] ?? "") !== "admin"): ?>
                                    <?php if (($user["status"] ?? "") === "blocked"): ?>
                                        <?php admin_action_button("Unblock", "unblock", 0, (int) $user["id"]); ?>
                                    <?php else: ?>
                                        <span class="danger"><?php admin_action_button("Block", "block", 0, (int) $user["id"]); ?></span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="muted">Protected</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php endif; ?>
</main>
</body>
</html>
