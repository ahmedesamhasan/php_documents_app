<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host     = 'localhost';
$user     = 'root';
$password = '';
$db_name  = 'tutorial';

try {
  $connect = new mysqli($host, $user, $password, $db_name);


  $connect->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
  error_log('Database connection error: ' . $e->getMessage());
  exit('failed to connect to database');
}