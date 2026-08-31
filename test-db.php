<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require "db.php";

$sql = "SELECT * FROM users";

$stmt = $pdo->query($sql);

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($users as $user) {
	echo "ID: " . $user["id"] . "<br>";
	echo "Username: " . $user["username"] . "<br>";
	echo "Email: " . $user["email"] . "<br>";
	echo "<hr>";
}
