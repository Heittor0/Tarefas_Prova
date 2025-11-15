<?php

$host = 'ep-bold-unit-admbc0is-pooler.c-2.us-east-1.aws.neon.tech';

$user = 'neondb_owner';
$password = 'npg_iFdzG1RAQcw6';
$dbname = 'neondb';

$dsn = "pgsql:host=$host;dbname=$dbname;sslmode=require";
$endpoint_id = 'ep-bold-unit-admbc0is';

$dsn = "pgsql:host=$host;dbname=$dbname;user=$user;password=$password;sslmode=require;options='endpoint=$endpoint_id'";

try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

?>
