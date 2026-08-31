<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION["role"] !== "admin") {
    http_response_code(403);
    die("Access denied.");
}

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

<p>
    You have administrator access.
</p>

</body>

</html>
