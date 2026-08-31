<?php

function validate_post(string $title, string $content): array
{
    $errors = [];

    if ($title === "") {
        $errors["title"] = "Title is required.";
    } elseif (strlen($title) < 3) {
        $errors["title"] = "Title must be at least 3 characters.";
    } elseif (strlen($title) > 100) {
        $errors["title"] = "Title must not exceed 100 characters.";
    }

    if ($content === "") {
        $errors["content"] = "Content is required.";
    } elseif (strlen($content) > 5000) {
        $errors["content"] = "Content must not exceed 5000 characters.";
    }

    return $errors;
}