<?php

if(!defined('SECURE_BOOT')) exit;

if (isset($_POST['action'])) {

  if ($_POST['action'] == 'edit-client') {
    $excludedKeys = ['action'];

    $ok = true;

    foreach($_POST as $key => $value) {
      if (!in_array($key, $excludedKeys) && empty($value)) {
        $ok = false;
        $_SESSION['dashboard-form-error-'.$key] = 'Pole nie może być puste';
      }
    }

    if (!$ok) {
      $_SESSION['dashboard-form-error'] = 'Popraw wprowadzone dane.';
      header("Location: {$config['site_url']}/dashboard.php?action=add-client");
      exit;
    }

    if(strlen($_POST['pesel']) != 11){
      $ok = false;
      $_SESSION['dashboard-form-error-pesel'] = "PESEL musi mieć 11 cyfr!";
    }

    if(strlen($_POST['phone']) != 9){
      $ok = false;
      $_SESSION['dashboard-form-error-phone'] = "Numer telefonu musi mieć 9 cyfr!";
    }

    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
      $ok = false;
      $_SESSION['dashboard-form-error-email'] = "Niepoprawny email";
    }

    if ($ok) {
      $query = sprintf("UPDATE clients SET name = '%s', surname = '%s', city = '%s', street = '%s', number = '%s', phone = '%s', email = '%s', pesel = '%s' WHERE id = '%s';",
        $db->real_escape_string($_POST['name']),
        $db->real_escape_string($_POST['surname']),
        $db->real_escape_string($_POST['city']),
        $db->real_escape_string($_POST['street']),
        $db->real_escape_string($_POST['number']),
        $db->real_escape_string($_POST['phone']),
        $db->real_escape_string($_POST['email']),
        $db->real_escape_string($_POST['pesel']),
        $db->real_escape_string($_POST['id']),
      );

      $successful = $db->query($query);

      if ($successful) {
        $_SESSION['dashboard-form-success'] = 'Zmieniono dane klienta';
        header("Location: {$config['site_url']}/dashboard.php?view=client&id=".$_POST['id']);
        exit;
      }
      else {
        $_SESSION['dashboard-form-error'] = 'Błąd zapytania do bazy danych.';
        header("Location: {$config['site_url']}/dashboard.php?view=client&id=".$_POST['id']);
        exit;
      }
    }
    else {
      $_SESSION['dashboard-form-error'] = 'Popraw wprowadzone dane.';
      header("Location: {$config['site_url']}/dashboard.php?view=client&id=".$_POST['id']);
      exit;
    }
  }
  else if ($_POST['action'] == 'delete-client') {
    $query = sprintf("DELETE FROM clients WHERE id = '%s'",
      $db->real_escape_string($_POST['id'])
    );

    $successful = $db->query($query);
    if ($successful) {
      $_SESSION['dashboard-form-success'] = 'Usunięto klienta';
      header("Location: {$config['site_url']}/dashboard.php?view=clients");
      exit;
    }
    else {
      $error = $db->errno;
      $_SESSION['dashboard-form-error'] = $error === 1451 ? 'By usunąć klienta, najpierw należy usunąć wszelkie wypożyczenia.' : 'Błąd zapytania do bazy danych.';
      header("Location: {$config['site_url']}/dashboard.php?view=clients");
      exit;
    }
  }
}
