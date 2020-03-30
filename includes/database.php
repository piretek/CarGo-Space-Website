<?php

if (!defined('SECURE_BOOT')) exit();

function create_database_connection($credentials) {

  $db = @new mysqli($credentials['host'], $credentials['login'], $credentials['pass'], $credentials['name']);
  if ($db->connect_errno != 0) {
    echo 'Błąd łączenia z bazą danych: '. $db->connect_errno." ".$db->connect_error;
    exit();
  }

  $db->set_charset('UTF-8');

  return $db;
}
