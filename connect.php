<?php
$connection = mysqli_connect("localhost", "root", "", "dbstorestock");

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
