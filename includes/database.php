<?php

if (!defined('SECURE_BOOT')) exit();

function import_database_schema($db, $name) {

  $needsToImport = false;

  $requiredColumns = ['brands', 'cars', 'clients', 'models', 'rents', 'types', 'users'];
  $columns = $db->query('SHOW TABLES');
  if ($columns->num_rows == 0) {
    $needsToImport = true;
  }
  else {
    while($column = $columns->fetch_assoc()) {
      if (!in_array($column['Tables_in_'.$name], $requiredColumns)) {
        $needsToImport = true;
      }
    }
  }

  if ($needsToImport) {
    if (!file_exists('./includes/database.sql')) {
      echo 'Plik schematu bazy danych nie istnieje.';
      exit();
    }

    $sqlImport = file_get_contents('./includes/database.sql');

    $successful = $db->query($sqlImport);

    return $successful;
  }
  else {
    return true;
  }
}

function create_database_connection($credentials) {

  $db = @new mysqli($credentials['host'], $credentials['login'], $credentials['pass'], $credentials['name']);
  if ($db->connect_errno != 0) {
    echo 'Błąd łączenia z bazą danych: '. $db->connect_errno." ".$db->connect_error;
    exit();
  }

  $db->set_charset('UTF-8');

  if (!import_database_schema($db, $credentials['name'])) {
    echo 'Błąd wgrywania schematu bazy danych: '.$db->error;
    exit;
  }

  return $db;
}
