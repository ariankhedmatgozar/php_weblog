<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    exit("Unauthorized");
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
    "txt"
];

$fileName = basename($file["name"]);

$extension = strtolower(
    pathinfo($fileName, PATHINFO_EXTENSION)
);

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
    "text/plain"
];

if (!in_array($mimeType, $allowedMimeTypes, true)) {
    exit("Invalid file type.");
}

$uploadDir = __DIR__ . "/" ;


$fileName = basename($file["name"]);

$destination = $uploadDir . $fileName;

if (move_uploaded_file($file["tmp_name"], $destination)) {
    echo "File uploaded successfully.";
} else {
    echo "Upload failed.";
}

