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

$post_id = $_POST["id"] ?? "";

if (!ctype_digit($post_id)) {
    http_response_code(422);

    echo json_encode([
        "success" => false,
        "message" => "Invalid post ID."
    ]);

    exit;
}

$stmt = $pdo->prepare(
    "SELECT id
     FROM posts
     WHERE id = :id
     AND user_id = :user_id"
);

$stmt->execute([
    "id" => $post_id,
    "user_id" => $_SESSION["user_id"]
]);

$post = $stmt->fetch();

if (!$post) {
    http_response_code(404);

    echo json_encode([
        "success" => false,
        "message" => "Post not found."
    ]);

    exit;
}

$stmt = $pdo->prepare(
    "DELETE FROM posts
     WHERE id = :id
     AND user_id = :user_id"
);

$stmt->execute([
    "id" => $post_id,
    "user_id" => $_SESSION["user_id"]
]);

echo json_encode([
    "success" => true,
    "message" => "Post deleted successfully."
]);