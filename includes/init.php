<?php

if (!defined('SECURE_BOOT')) define('SECURE_BOOT', true);
session_start();

if (!file_exists('./includes/config.php')) {
  echo 'Brak pliku konfiguracyjnego. Skopiuj plik config.sample.php do pliku config.php w folderze includes.';
  exit();
}

$config = require_once './includes/config.php';

require_once 'database.php';
require_once 'functions/verify-config.php';

$configErrors = verifyConfig($config);
if (!empty($configErrors)) {
  echo "Błąd w pliku konfiguracyjnym: <br />";

  foreach($configErrors as $error) {
    echo $error.'<br />';
  }
  exit;
}

$db = create_database_connection( $config['db'] );

if (isset($auth) && $auth && (!isset($_SESSION['user']) || $_SESSION['user'] === 0)) {
  header("Location: {$config['site_url']}/");
  exit();
}
else if (isset($_SESSION['user']) && $_SESSION['user'] !== 0) {
  $sessionUsers = $db->query("SELECT * FROM users WHERE id = '{$_SESSION['user']}'");
  if ($sessionUsers->num_rows == 0) {
    session_destroy();

    header("Location: {$config['site_url']}/");
    exit;
  }

  $sessionUser = $sessionUsers->fetch_assoc();
  define('USER_AUTHORIZED', true);
}
else {
  define('USER_AUTHORIZED', false);
}

date_default_timezone_set('Europe/Warsaw');

$rentStatus = [
  0 => 'Oczekuje na zatwierdzenie',
  1 => 'Odrzucone',
  2 => 'Zatwierdzone',
  3 => 'W trakcie wypożyczenia',
  4 => 'Zwrócono',
];

require_once './includes/functions/carinfo.php';
require_once './includes/functions/mail.php';
require_once './includes/functions/input.php';
require_once './includes/functions/rent_price.php';
