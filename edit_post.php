```php
<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require "config/db.php";


if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}


$post_id = $_GET["id"] ?? null;

if (!$post_id) {
    http_response_code(400);
    die("Invalid post ID.");
}


$stmt = $pdo->prepare(
    "SELECT id, user_id, title, content
     FROM posts
     WHERE id = :post_id"
);

$stmt->execute([
    "post_id" => $post_id
]);

$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    http_response_code(404);
    die("Post not found.");
}


$is_owner = ($post["user_id"] == $_SESSION["user_id"]);
$is_admin = ($_SESSION["role"] === "admin");

if (!$is_owner && !$is_admin) {
    http_response_code(403);
    die("Access denied.");
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (
        !isset($_POST["csrf_token"]) ||
        !isset($_SESSION["csrf_token"]) ||
        !hash_equals(
            $_SESSION["csrf_token"],
            $_POST["csrf_token"]
        )
    ) {
        http_response_code(403);
        die("Invalid CSRF token.");
    }


    $title = $_POST["title"] ?? "";
    $content = $_POST["content"] ?? "";


    $stmt = $pdo->prepare(
        "UPDATE posts
         SET title = :title,
             content = :content
         WHERE id = :post_id"
    );

    $stmt->execute([
        "title" => $title,
        "content" => $content,
        "post_id" => $post_id
    ]);


    header("Location: post.php?id=" . $post_id);
    exit;
}

?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Post</title>
</head>

<body>

<h1>Edit Post</h1>

<form method="POST">

    <input
        type="hidden"
        name="csrf_token"
        value="<?= htmlspecialchars($_SESSION["csrf_token"]) ?>"
    >

    <label>
        Title:
        <input
            type="text"
            name="title"
            value="<?= htmlspecialchars($post["title"]) ?>"
        >
    </label>

    <br><br>

    <label>
        Content:
        <textarea name="content"><?= htmlspecialchars($post["content"]) ?></textarea>
    </label>

    <br><br>

    <button type="submit">Update Post</button>

</form>

</body>

</html>
```
