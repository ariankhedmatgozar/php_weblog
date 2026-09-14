<?php

require "auth.php";

error_reporting(E_ALL);
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);


require "helpers/validation.php";
require "config/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"] ?? "");
    $content = trim($_POST["content"] ?? "");

    $errors = validate_post($title, $content);

    if (empty($errors)) {

        $stmt = $pdo->prepare(
            "INSERT INTO posts (user_id, title, content)
             VALUES (:user_id, :title, :content)"
        );

        $stmt->execute([
            "user_id" => $_SESSION["user_id"],
            "title" => $title,
            "content" => $content
        ]);

        header("Location: profile.php");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Create Post</title>
</head>

<body>

<h1>Create Post</h1>

<?php if (!empty($errors)): ?>

    <ul>

        <?php foreach ($errors as $error): ?>

            <li>
                <?= htmlspecialchars($error) ?>
            </li>

        <?php endforeach; ?>

    </ul>

<?php endif; ?>

<form method="POST">

    <label>
        Title:
        <input type="text" name="title">
    </label>

    <br><br>

    <label>
        Content:
        <textarea name="content"></textarea>
    </label>

    <br><br>

    <button type="submit">Create Post</button>

</form>

</body>

</html>
