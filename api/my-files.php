<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized"
    ]);
    exit;
}

require __DIR__ . "/../config/db.php";

$stmt = $pdo->prepare("
    SELECT
        id,
        original_name,
        stored_name,
        mime_type,
        size,
        created_at
    FROM files
    WHERE user_id = :user_id
    ORDER BY created_at DESC
");

$stmt->execute([
    ":user_id" => $_SESSION["user_id"]
]);

$files = $stmt->fetchAll(PDO::FETCH_ASSOC);

header("Content-Type: application/json");

echo json_encode([
    "success" => true,
    "data" => $files
]);