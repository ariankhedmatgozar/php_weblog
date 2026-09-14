<?php

require "auth.php";

echo "Welcome " . htmlspecialchars($_SESSION['username']);
