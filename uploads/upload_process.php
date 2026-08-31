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

$uploadDir = __DIR__ . "/" ;


$fileName = basename($file["name"]);

$destination = $uploadDir . $fileName;

if (move_uploaded_file($file["tmp_name"], $destination)) {
    echo "File uploaded successfully.";
} else {
    echo "Upload failed.";
}

