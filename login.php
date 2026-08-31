<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.cookie_samesite', 'Lax');

session_start();

require "config/db.php";

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "") {
        $errors[] = "Username is required.";
    }

    if ($password === "") {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {

        $sql = "SELECT * FROM users WHERE username = :username";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":username" => $username
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

	if ($user && password_verify($password, $user["password"])) {
	
	session_regenerate_id(true);

	$_SESSION["user_id"] = $user["id"];
	$_SESSION["username"] = $user["username"];
	$_SESSION["role"] = $user["role"];

	header("Location: profile.php");
	exit;

        } else {

            $errors[] = "Invalid username or password.";
        }
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>

<body>

<h1>Login</h1>

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
        Username:
        <input type="text" name="username">
    </label>

    <br><br>

    <label>
        Password:
        <input type="password" name="password">
    </label>

    <br><br>

    <button type="submit">
        Login
    </button>

</form>

</body>

</html>
