<?php
$db = require_once 'database.php';

if (!isset($_GET['g'])) header_error(); else header_json();

echo $db->json_("select * from users where `group` = ?", 'i', +$_GET['g']);
