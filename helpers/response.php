<?php

function json_response(
    bool $success,
    string $message = "",
    array $data = [],
    int $status = 200
): never {

    http_response_code($status);

    echo json_encode([
        "success" => $success,
        "message" => $message,
        "data" => $data
    ]);

    exit;
}