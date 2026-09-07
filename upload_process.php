<?php

session_start();

require __DIR__ . "/config/db.php";

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    exit("Unauthorized");
}

if (!isset($_POST["csrf_token"])) {
    http_response_code(403);
    exit("CSRF token missing.");
}

if (!hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])) {
    http_response_code(403);
    exit("Invalid CSRF token.");
}

if (!isset($_FILES["file"])) {
    exit("No file uploaded.");
}

$file = $_FILES["file"];

if ($file["error"] !== UPLOAD_ERR_OK) {
    exit("File upload error.");
}

$maxFileSize = 2 * 1024 * 1024;

if ($file["size"] > $maxFileSize) { 
    exit("File is too large."); 
}


$allowedExtensions = [
    "jpg",
    "jpeg",
    "png",
    "gif",
    "pdf",
    "txt",
];


$originalName = basename($file["name"]);

$extension = strtolower(
    pathinfo($originalName, PATHINFO_EXTENSION)
);

$randomName = bin2hex(random_bytes(16));

$fileName = $randomName . "." . $extension;

$uploadDir = "/opt/lampp/uploads/" ;

$destination = $uploadDir . $fileName;


if (!in_array($extension, $allowedExtensions, true)) {
    exit("File type not allowed.");
}

$finfo = new finfo(FILEINFO_MIME_TYPE);

$mimeType = $finfo->file($file["tmp_name"]);

$allowedMimeTypes = [
    "image/jpeg",
    "image/png",
    "image/gif",
    "application/pdf",
    "text/plain",
    
];

if (!in_array($mimeType, $allowedMimeTypes, true)) {
    exit("Invalid file type.");
}

$signatures = [
    "image/jpeg" => ["FF", "D8", "FF"],
    "image/png"  => ["89", "50", "4E", "47"],
    "image/gif"  => ["47", "49", "46"],
    "application/pdf" => ["25", "50", "44", "46"],
];

if (isset($signatures[$mimeType])) {

    $fileHandle = fopen($file["tmp_name"], "rb");

    $bytes = fread($fileHandle, 4);

    fclose($fileHandle);

    $hex = strtoupper(bin2hex($bytes));

    $expected = implode("", $signatures[$mimeType]);

    if (strpos($hex, $expected) !== 0) {
        exit("Invalid file signature.");
    }
}   

if (move_uploaded_file($file["tmp_name"], $destination)) {

    try {

        $stmt = $pdo->prepare("
            INSERT INTO files (
                user_id,
                original_name,
                stored_name,
                mime_type,
                size
            )
            VALUES (
                :user_id,
                :original_name,
                :stored_name,
                :mime_type,
                :size
            )
        ");

        $stmt->execute([
            ":user_id" => $_SESSION["user_id"],
            ":original_name" => $originalName,
            ":stored_name" => $fileName,
            ":mime_type" => $mimeType,
            ":size" => $file["size"]
        ]);

        echo "File uploaded successfully.";

    } catch (PDOException $e) {

        if (file_exists($destination)) {
            unlink($destination);
        }

        http_response_code(500);
        echo "Upload failed.";
    }

} else {
    echo "Upload failed.";
}

