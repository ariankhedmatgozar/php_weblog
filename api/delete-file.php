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

if (!isset($_POST["csrf_token"])) {
    http_response_code(403);

    echo json_encode([
        "success" => false,
        "message" => "CSRF token missing."
    ]);

    exit;
}

if (!hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])) {
    http_response_code(403);

    echo json_encode([
        "success" => false,
        "message" => "Invalid CSRF token."
    ]);

    exit;
}

if (!isset($_POST["id"])) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "File ID is required."
    ]);
    exit;
}

$fileId = (int) $_POST["id"];

require __DIR__ . "/../config/db.php";

$stmt = $pdo->prepare("
    SELECT *
    FROM files
    WHERE id = :id
    AND user_id = :user_id
");

$stmt->execute([
    ":id" => $fileId,
    ":user_id" => $_SESSION["user_id"]
]);

$file = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$file) {
    http_response_code(404);
    echo json_encode([
        "success" => false,
        "message" => "File not found."
    ]);
    exit;
}

$filePath = "/opt/lampp/uploads/" . $file["stored_name"];

try {

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        DELETE FROM files
        WHERE id = :id
        AND user_id = :user_id
    ");

    $stmt->execute([
        ":id" => $fileId,
        ":user_id" => $_SESSION["user_id"]
    ]);

    if (file_exists($filePath)) {

        if (!unlink($filePath)) {
            throw new Exception("Failed to delete file.");
        }
    }

    $pdo->commit();

    header("Content-Type: application/json");

    echo json_encode([
        "success" => true,
        "message" => "File deleted successfully."
    ]);

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "File deletion failed."
    ]);
}