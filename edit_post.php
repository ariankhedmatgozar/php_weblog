<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require "config/db.php";

$post_id = $_GET["id"];

$stmt = $pdo->prepare(
    "SELECT id, user_id, title, content
     FROM posts
     WHERE id = :post_id
     AND user_id = :user_id"
);

$stmt->execute([
    "post_id" => $post_id,
    "user_id" => $_SESSION["user_id"]
]);

$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    http_response_code(404);
    die("Post not found.");
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = $_POST["title"];
    $content = $_POST["content"];

    $stmt = $pdo->prepare(
        "UPDATE posts
         SET title = :title,
             content = :content
         WHERE id = :post_id
         AND user_id = :user_id"
    );

    $stmt->execute([
        "title" => $title,
        "content" => $content,
        "post_id" => $post_id,
        "user_id" => $_SESSION["user_id"]
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
