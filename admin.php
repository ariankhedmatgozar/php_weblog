<?php

session_start();

require "config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION["role"] !== "admin") {
    http_response_code(403);
    die("Access denied.");
}


// گرفتن همه پست‌ها به همراه username نویسنده
$sql = "
    SELECT
        posts.id,
        posts.title,
        posts.content,
        users.username
    FROM posts
    JOIN users
        ON posts.user_id = users.id
    ORDER BY posts.id DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
</head>

<body>

<h1>Admin Panel</h1>

<p>
    Welcome, <?= htmlspecialchars($_SESSION["username"]) ?>
</p>

<hr>

<h2>Posts</h2>

<?php if (empty($posts)): ?>

    <p>No posts found.</p>

<?php else: ?>

    <table border="1" cellpadding="10">

        <thead>

            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Content</th>
            </tr>

        </thead>

        <tbody>

            <?php foreach ($posts as $post): ?>

                <tr>

                    <td>
                        <?= htmlspecialchars($post["id"]) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($post["title"]) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($post["username"]) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($post["content"]) ?>
                    </td>

                </tr>

            <?php endforeach; ?>

        </tbody>

    </table>

<?php endif; ?>

</body>

</html>