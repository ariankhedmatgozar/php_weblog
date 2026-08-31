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

$post_id = $_POST["id"] ?? "";
$title = trim($_POST["title"] ?? "");
$content = trim($_POST["content"] ?? "");

if (!ctype_digit($post_id)) {
    http_response_code(422);

    echo json_encode([
        "success" => false,
        "message" => "Invalid post ID."
    ]);

    exit;
}

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
    "UPDATE posts
     SET title = :title,
         content = :content
     WHERE id = :id
     AND user_id = :user_id"
);

$stmt->execute([
    "title" => $title,
    "content" => $content,
    "id" => $post_id,
    "user_id" => $_SESSION["user_id"]
]);

if ($stmt->rowCount() === 0) {
    http_response_code(404);

    echo json_encode([
        "success" => false,
        "message" => "Post not found."
    ]);

    exit;
}

echo json_encode([
    "success" => true,
    "message" => "Post updated successfully."
]);