<?php

require "config/db.php";

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";


    // Username validation

    if ($username === "") {
        $errors[] = "Username is required.";
    } elseif (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters.";
    } elseif (strlen($username) > 50) {
        $errors[] = "Username must be less than 50 characters.";
    }


    // Email validation

    if ($email === "") {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }


    // Password validation

    if ($password === "") {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }


    // If there are no validation errors

    if (empty($errors)) {

	 $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, email, password)
		VALUES (:username, :email, :password)";

	 $stmt = $pdo->prepare($sql);

        try {

            $stmt->execute([
                ":username" => $username,
                ":email" => $email,
                ":password" => $passwordHash
            ]);

            $success = "User registered successfully!";

        } catch (PDOException $e) {

            $errors[] = "Could not create user.";

        }
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>
</head>

<body>

<h1>Register</h1>


<?php if (!empty($errors)): ?>

    <ul>

        <?php foreach ($errors as $error): ?>

            <li>
                <?= htmlspecialchars($error) ?>
            </li>

        <?php endforeach; ?>

    </ul>

<?php endif; ?>


<?php if ($success): ?>

    <p>
        <?= htmlspecialchars($success) ?>
    </p>

<?php endif; ?>


<form method="POST">

    <label>
        Username:
        <input
            type="text"
            name="username"
            value="<?= htmlspecialchars($username ?? "") ?>"
        >
    </label>

    <br><br>


    <label>
        Email:
        <input
            type="email"
            name="email"
            value="<?= htmlspecialchars($email ?? "") ?>"
        >
    </label>

    <br><br>


    <label>
        Password:
        <input
            type="password"
            name="password"
        >
    </label>

    <br><br>


    <button type="submit">
        Register
    </button>

</form>

</body>

</html>
