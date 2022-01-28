# newAPI
very simple server API template

PHP 7
MySQL
JetBrains PhpStorm project

+database.sql - BD init script
!requests.http - request samples
database.php - simple framework

Project sample:
<?php
$db = require_once 'database.php';
header_json();
echo $db->json("select * from users");
