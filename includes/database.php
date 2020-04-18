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
    if (!file_exists('./includes/database-schema.php')) {
      echo 'Plik schematu bazy danych nie istnieje.';
      exit();
    }

    $sqlStatements = require './includes/database-schema.php';

    $successful = [];
    foreach($sqlStatements as $sqlStatement) {
      $successful[] = $db->query($sqlStatement);
    }

    return !in_array(false, $successful);
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
