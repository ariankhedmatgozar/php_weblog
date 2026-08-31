<?php

session_start();

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Method not allowed."
    ]);

    exit;
}

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "Unauthorized."
    ]);

    exit;
}

if (
    !isset($_POST["csrf_token"]) ||
    !isset($_SESSION["csrf_token"]) ||
    !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
) {
    http_response_code(403);

    echo json_encode([
        "success" => false,
        "message" => "Invalid CSRF token."
    ]);

    exit;
}

require "../config/db.php";
require "../helpers/validation.php";

$title = trim($_POST["title"] ?? "");
$content = trim($_POST["content"] ?? "");

$errors = validate_post($title, $content);

if (!empty($errors)) {
    http_response_code(422);

    echo json_encode([
        "success" => false,
        "errors" => $errors
    ]);

    exit;
}

$stmt = $pdo->prepare(
    "INSERT INTO posts (user_id, title, content)
     VALUES (:user_id, :title, :content)"
);

$stmt->execute([
    "user_id" => $_SESSION["user_id"],
    "title" => $title,
    "content" => $content
]);

echo json_encode([
    "success" => true,
    "message" => "Post created successfully.",
    "post" => [
        "id" => $pdo->lastInsertId(),
        "title" => $title,
        "content" => $content
    ]
]);