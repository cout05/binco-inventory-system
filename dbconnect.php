<?php
$conn = new mysqli("localhost", "root", "", "inventory_db");

if ($conn->connect_error) {
    die("Can't connect to database");
}
