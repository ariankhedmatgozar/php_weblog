<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION["role"] !== "admin") {
    http_response_code(403);
    die("Access denied.");
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

$user_id = $_POST["user_id"] ?? null;
$role = $_POST["role"] ?? null;

if (!$user_id || !$role) {
    http_response_code(400);
    die("Invalid request.");
}

if (!in_array($role, ["user", "admin"], true)) {
    http_response_code(400);
    die("Invalid role.");
}

if ((int)$user_id === (int)$_SESSION["user_id"]) {
    http_response_code(403);
    die("You cannot change your own role.");
}

require "config/db.php";

$stmt = $pdo->prepare(
    "SELECT id
     FROM users
     WHERE id = :user_id"
);

$stmt->execute([
    "user_id" => $user_id
]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    http_response_code(404);
    die("User not found."); 
}

$stmt = $pdo->prepare(
    "UPDATE users
     SET role = :role
     WHERE id = :user_id"
);

$stmt->execute([
    "role" => $role,
    "user_id" => $user_id
]);

header("Location: admin.php");
exit;