<?php

require "auth.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


require "config/db.php";

$post_id = $_GET["id"];
$user_id = $_SESSION["user_id"];
$role = $_SESSION["role"];

if ($role === "admin") {
  $stmt = $pdo->prepare(
    "SELECT id, user_id, title, content
     FROM posts
     WHERE id = :post_id"
  );
  $stmt->execute([
    "post_id" => $post_id
  ]);
} else {

  $stmt = $pdo->prepare(
    "SELECT id, user_id, title, content
     FROM posts
     WHERE id = :post_id
     AND user_id = :user_id"
);

$stmt->execute([
    "post_id" => $post_id,
    "user_id" => $user_id
]);
}

$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    http_response_code(404);
    die("Post not found.");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($post["title"]) ?></title>
</head>

<body>

<h1><?= htmlspecialchars($post["title"]) ?></h1>

<p>
    <?= htmlspecialchars($post["content"]) ?>
</p>

<p>
    Owner ID:
    <?= htmlspecialchars($post["user_id"]) ?>
</p>

<?php require "csrf.php"; ?> 

<form method="POST" action="delete_post.php">

    <input
        type="hidden"
        name="id"
        value="<?= htmlspecialchars($post["id"]) ?>"
    >

    <input
        type="hidden"
        name="csrf_token"
        value="<?= htmlspecialchars($_SESSION["csrf_token"]) ?>"
        >

    <button type="submit">
        Delete
    </button>

</form>

</body>

</html>
