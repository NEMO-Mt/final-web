<?php
declare(strict_types=1);
function getConnection(): mysqli
{
    $hostname = '27.254.134.26';
    $dbName = 'k1god_fastcamp';
    $username = 'k1god_fastcamp';
    $password = 'M330350390m';
    $conn = new mysqli($hostname, $username, $password, $dbName);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}