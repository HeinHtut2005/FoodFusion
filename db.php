<?php

$conn = new mysqli("localhost", "root", "", "foodfusion_db1");

if ($conn->connect_error) {
    die("Database connection failed");
}

?>