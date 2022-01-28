<?php
$db = require_once 'database.php';

if (!isset($_POST['login']) || !isset($_POST['password'])) header_error(); else header_json();

$login = $db->query_("select * from users where name=? and pass=?", 'ss', $_POST['login'], $_POST['password'])->rows();

if ($login)
    echo json_encode(array('session'=>rand()));
else
    echo json_encode(array('error'=>'Invalid user name or password'));
