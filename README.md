# newAPI
very simple server API template

PHP 7

MySQL

JetBrains PhpStorm project

-
+database.sql - BD init script

!requests.http - request samples

database.php - simple framework

.htaccess - apache configuration (hides ".php")

web.config - IIS configuration (hides ".php")

-
Project sample:

$db = require_once 'database.php';

header_json();

echo $db->json("select * from users");
