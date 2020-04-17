<?php

if(!defined('SECURE_BOOT')) exit;

if (isset($_POST['action'])) {

  if ($_POST['action'] == 'edit-rent') {

    $checkedKeys = ['car', 'to', 'from'];

    $ok = true;

    foreach($_POST as $key => $value) {
      if (in_array($key, $checkedKeys) && empty($value)) {
        $ok = false;
        $_SESSION['dashboard-form-error-'.$key] = 'Pole nie może być puste';
      }
    }

    $cars = $db->query( sprintf("SELECT * FROM cars WHERE id = '%s'",
    $db->real_escape_string($_POST['car'])
  ));

  if ($cars->num_rows == 0) {
    $ok = false;
    $_SESSION['dashboard-form-error-car'] = 'Taki pojazd nie istnieje.';
  }

  list($from_year, $from_month, $from_day) = explode("-",$_POST['from']);
  $from = mktime(0, 0, 0, $from_month, $from_day, $from_year);

  list($to_year, $to_month, $to_day) = explode("-",$_POST['to']);
  $to = mktime(23, 59, 59, $to_month, $to_day, $to_year);

  $availableFrom = strtotime("now +1 day midnight");
  $availableToFrom = strtotime("now +2 day midnight");
  $availableTo = strtotime("now +3 months");

  if($from < $availableFrom){
    $ok = false;
    $_SESSION['dashboard-form-error-from'] = "Data nie może być wcześniejsza niż ".date("d.m.Y", $availableFrom)."!";
  }

  if($to < $availableToFrom){
    $ok = false;
    $_SESSION['dashboard-form-error-to'] = "Data nie może być wcześniejsza niż ".date("d.m.Y", $availableToFrom)."!";
  }

  if($to > $availableTo){
    $ok = false;
    $_SESSION['dashboard-form-error-to'] = "Data nie może być późniejsza niż ".date("d.m.Y", $availableTo)."!";
  }

    if ($ok) {
      $query = sprintf("UPDATE `models` SET brand = '%s', type = '%s', model = '%s', year_from = '%s', year_to = '%s', price = '%s' WHERE id = '%s'",
        $db->real_escape_string($_POST['brand']),
        $db->real_escape_string($_POST['type']),
        $db->real_escape_string($_POST['name']),
        $db->real_escape_string($_POST['year_from']),
        $db->real_escape_string($_POST['year_to']),
        $db->real_escape_string($_POST['price']),
        $db->real_escape_string($_POST['id']),
      );
      $successful = $db->query($query);

      if ($successful) {
        $_SESSION['dashboard-form-success'] = 'Zmieniono model';
        header("Location: {$config['site_url']}/dashboard.php?view=rent");
        exit;
      }
      else {
        $_SESSION['dashboard-form-error'] = 'Błąd zapytania do bazy danych.';
        header("Location: {$config['site_url']}/dashboard.php?view=rents&id=".$_POST['id']);
        exit;
      }
    }
    else {
      $_SESSION['dashboard-form-error'] = 'Popraw wprowadzone dane.';
      header("Location: {$config['site_url']}/dashboard.php?view=rents&id=".$_POST['id']);
      exit;
    }
  }
  else if ($_POST['action'] == 'edit-rent-status') {
    $query = sprintf("UPDATE rents SET status = '%s' WHERE id = '%s'",
      $db->real_escape_string($_POST['new-status']),
      $db->real_escape_string($_POST['id']),
    );

    $successful = $db->query($query);

    if ($successful) {
      $_SESSION['dashboard-form-success'] = 'Zmieniono status';
      header("Location: {$config['site_url']}/dashboard.php?view=rent&id=".$_POST['id']);
      exit;
    }
    else {
      $_SESSION['dashboard-form-error'] = 'Błąd zapytania do bazy danych.';
      header("Location: {$config['site_url']}/dashboard.php?view=rent&id=".$_POST['id']);
      exit;
    }
  }
  else if ($_POST['action'] == 'delete-rent') {
    $query = sprintf("DELETE FROM rents WHERE id = '%s'",
      $db->real_escape_string($_POST['id'])
    );

    $successful = $db->query($query);
    if ($successful) {
      $_SESSION['dashboard-form-success'] = 'Usunięto wypożyczenie';
      header("Location: {$config['site_url']}/dashboard.php?view=rents");
      exit;
    }
    else {
      $error = $db->errno;
      $_SESSION['dashboard-form-error'] = $error === 1451 ? 'Usuń pojazd, który posiada przydzielony model.' : 'Błąd zapytania do bazy danych.';
      header("Location: {$config['site_url']}/dashboard.php?view=rents&id=".$_POST['id']);
      exit;
    }
  }
}
