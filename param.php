<?php
$db = require_once 'database.php';

$data = decoded_json();

if (!isset($data['group'])) header_error(); else header_json();

echo $db->json_("select * from users where `group` = ?", 'i', +$data['group']);
