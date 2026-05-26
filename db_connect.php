<?php
$host     = 'localhost'; 
$dbname   = 'codewarden'; 
$user     = 'root';
$password = ''; 

$pdo = new PDO('mysql:host=$host;dbname=$dbname;charset=utf8', $user, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
