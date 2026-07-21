<?php
declare(strict_types=1);

header("Content-Type: application/json; charset=UTF-8");

if (($_SERVER["REQUEST_METHOD"] ?? "GET") !== "POST") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "POST only."]);
    exit;
}

require_once __DIR__ . "/comments/bootstrap.php";

$name = trim((string) ($_POST["name"] ?? ""));
$email = trim((string) ($_POST["email"] ?? ""));
$subject = trim((string) ($_POST["subject"] ?? ""));
$message = trim((string) ($_POST["message"] ?? ""));
$honeypot = trim((string) ($_POST["website"] ?? ""));

if ($honeypot !== "") {
    echo json_encode(["success" => true, "message" => "Thanks. We got your note."]);
    exit;
}

if ($name === "" || strlen($name) > 120) {
    http_response_code(422);
    echo json_encode(["success" => false, "message" => "Please enter your name."]);
    exit;
}

if ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 180) {
    http_response_code(422);
    echo json_encode(["success" => false, "message" => "Please enter a valid email."]);
    exit;
}

if ($message === "" || strlen($message) < 10) {
    http_response_code(422);
    echo json_encode(["success" => false, "message" => "Write a bit more in the message (at least a sentence)."]);
    exit;
}

if (strlen($message) > 5000) {
    http_response_code(422);
    echo json_encode(["success" => false, "message" => "Message is too long."]);
    exit;
}

if (strlen($subject) > 160) {
    http_response_code(422);
    echo json_encode(["success" => false, "message" => "Subject is too long."]);
    exit;
}

try {
    vp_contact_save_message([
        "name" => $name,
        "email" => $email,
        "subject" => $subject,
        "message" => $message,
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Could not save right now. Please try again later.",
    ]);
    exit;
}

echo json_encode([
    "success" => true,
    "message" => "Thanks — your note is with us. We'll get back to you if needed.",
]);
