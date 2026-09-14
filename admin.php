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

$sql = "
    SELECT
        id,
        username,
        role
    FROM users
    ORDER BY id DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                <th>Actions</th>
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

                    <td>

                        <a href="edit_post.php?id=<?= $post["id"] ?>">
                            Edit
                        </a>

                        <form method="POST" action="delete_post.php" style="display:inline;">

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

                    </td>

                </tr>

            <?php endforeach; ?>

        </tbody>

    </table>

<?php endif; ?>

<hr>

<h2>Users</h2>

<?php if (empty($users)): ?>

    <p>No users found.</p>

<?php else: ?>

    <table border="1" cellpadding="10">

        <thead>

            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>

        </thead>

        <tbody>

            <?php foreach ($users as $user): ?>

                <tr>

                    <td>
                        <?= htmlspecialchars($user["id"]) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($user["username"]) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($user["role"]) ?>
                    </td>

                    <td>

                        <form method="POST" action="change_role.php">

                            <input
                                type="hidden"
                                name="user_id"
                                value="<?= htmlspecialchars($user["id"]) ?>"
                            >

                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= htmlspecialchars($_SESSION["csrf_token"]) ?>"
                            >

                            <select name="role">

                                <option
                                    value="user"
                                    <?= $user["role"] === "user" ? "selected" : "" ?>
                                >
                                    user
                                </option>

                                <option
                                    value="admin"
                                    <?= $user["role"] === "admin" ? "selected" : "" ?>
                                >
                                    admin
                                </option>

                            </select>

                            <button type="submit">
                                Change Role
                            </button>

                        </form>

                    </td>

                </tr>

            <?php endforeach; ?>

        </tbody>

    </table>

<?php endif; ?>

</body>

</html>