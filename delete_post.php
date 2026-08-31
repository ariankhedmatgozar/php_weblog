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

$post_id = $_POST["id"];

$stmt = $pdo->prepare(
    "DELETE FROM posts
     WHERE id = :post_id
     AND user_id = :user_id"
);

$stmt->execute([
    "post_id" => $post_id,
    "user_id" => $_SESSION["user_id"]
]);

header("Location: profile.php");
exit;
