<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    die("Method Not Allowed.");
}

if (
    !isset($_POST["csrf_token"]) ||
    !isset($_SESSION["csrf_token"]) ||
    !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
) {
    http_response_code(403);
    die("Invalid CSRF token.");
}

require "config/db.php";

$post_id = $_POST["id"] ?? null;

if (!$post_id) {
    http_response_code(400);
    die("Invalid post ID.");
}


$stmt = $pdo->prepare(
    "SELECT user_id
     FROM posts
     WHERE id = :post_id"
);

$stmt->execute([
    "post_id" => $post_id
]);

$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    http_response_code(404);
    die("Post not found.");
}


$is_owner = ($post["user_id"] == $_SESSION["user_id"]);
$is_admin = ($_SESSION["role"] === "admin");

if (!$is_owner && !$is_admin) {
    http_response_code(403);
    die("Access denied.");
}


$stmt = $pdo->prepare(
    "DELETE FROM posts
     WHERE id = :post_id"
);

$stmt->execute([
    "post_id" => $post_id
]);


header("Location: profile.php");
exit;