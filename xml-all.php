<?php
$db = require_once 'database.php';
header_xml();
echo $db->xml("select * from users");
