<?php
$db = require_once 'database.php';

if (!isset($_GET['g'])) header_error(); else header_xml();

echo $db->xml_("select * from users where `group` = ?", 'users', 'user', 'i', +$_GET['g']);
