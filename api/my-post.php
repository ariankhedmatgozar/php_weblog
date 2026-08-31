<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();

header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "Unauthorized"
    ]);

    exit;
}

require "../config/db.php";

$stmt = $pdo->prepare(
    "SELECT id, title, content
     FROM posts
     WHERE user_id = :user_id
     ORDER BY id DESC"
);

$stmt->execute([
    "user_id" => $_SESSION["user_id"]
]);

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "success" => true,
    "posts" => $posts
]);

