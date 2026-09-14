<?php

require "auth.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require 'config/db.php';
require 'csrf.php';

$user_id = $_SESSION["user_id"];


$sql = "SELECT id, username, role
			  FROM users
			  WHERE id = :id";

$stmt = $pdo->prepare($sql);

$stmt->execute([
	":id" => $user_id
]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
	http_response_code(404);
	die("User not found.");
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Profile</title>
</head>

<body>

<h1>Profile</h1>

<p>
    User ID:
    <?= htmlspecialchars($user["id"]) ?>
</p>

<p>
    Username:
    <?= htmlspecialchars($user["username"]) ?>
</p>

<p>
    Role:
    <?= htmlspecialchars($user["role"]) ?>
</p>

<div id="posts-container">
    <p>Loading posts...</p>
</div>

<a href="logout.php">Logout</a>

<form id="create-post-form">

    <input
        type="text"
        name="title"
        placeholder="Title"
    >

    <textarea
        name="content"
        placeholder="Content"
    ></textarea>

    <input
        type="hidden"
        name="csrf_token"
        value="<?= htmlspecialchars($_SESSION["csrf_token"]) ?>"
    >

    <button type="submit">
        Create Post
    </button>

</form>

<div id="form-message"></div>

<script src="js/profile.js"></script>

</body>

</html>
