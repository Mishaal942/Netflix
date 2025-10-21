<?php
$host = "localhost";
$db = "dbnmpbout7b2uo";
$user = "uppbmi0whibtc";
$pass = "bjgew6ykgu1v";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
