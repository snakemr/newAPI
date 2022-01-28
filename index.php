<?php
$db = require_once 'database.php';
header_json();
echo $db->json("select * from users");
