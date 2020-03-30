<?php

if (!defined('SECURE_BOOT')) define('SECURE_BOOT', true);
session_start();

if (!file_exists('./includes/config.php')) {
  echo 'Brak pliku konfiguracyjnego. Skopiuj plik config.sample.php do pliku config.php w folderze includes.';
  exit();
}

$config = require_once './includes/config.php';

require_once 'database.php';
$db = create_database_connection( $config['db'] );

if ($auth && ($_SESSION['user'] === 0 || !isset($_SESSION['user']))) {
  header("Location: {$config['site_url']}/");
  exit();
}
