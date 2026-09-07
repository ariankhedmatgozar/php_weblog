<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    exit("Unauthorized");
}

if (!isset($_GET["file"])) {
    http_response_code(400);
    exit("File not specified.");
}

$fileName = basename($_GET["file"]);

require __DIR__ . "/config/db.php";

$stmt = $pdo->prepare("
    SELECT *
    FROM files
    WHERE stored_name = :stored_name
    AND user_id = :user_id
");

$stmt->execute([
    ":stored_name" => $fileName,
    ":user_id" => $_SESSION["user_id"]
]);

$file = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$file) {
    http_response_code(404);
    exit("File not found.");
}

$filePath = "/opt/lampp/uploads/" . $file["stored_name"];

if (!file_exists($filePath)) {
    http_response_code(404);
    exit("File not found.");
}

header("Content-Type: " . $file["mime_type"]);
header("Content-Disposition: attachment; filename=\"" . $file["original_name"] . "\"");

readfile($filePath);
exit;