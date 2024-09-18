<?php
define("DB_HOST", "mariadb");
define("USER_NAME", "root");
define("PASSWORD", "secret");
define("DATABASE", "Classification");

$conn = mysqli_connect(DB_HOST, USER_NAME, PASSWORD, DATABASE);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error() . " (Error code: " . mysqli_connect_errno() . ")");
}
