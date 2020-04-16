<?php

if(!defined('SECURE_BOOT')) exit;

if (isset($_POST['action'])) {

  $ok = true;

  $checkForEmpty = [
    'firstname',
    'lastname',
    'oldPassword'
  ];

  foreach ($checkForEmpty as $key){
    if (empty($_POST[$key])) {
      $ok = false;
      $_SESSION['dashboard-form-error-firstname'] = 'Pole nie może być puste';
    }
  }

  $emailWillChange = false;
  if (!empty($_POST['email'])) {
    $emailWillChange = true;

    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ) {
      $ok = false;
      $_SESSION["dashboard-form-error-email"] = "Niepoprawny email";
    }

    $emailExists = $db->query("SELECT * FROM users WHERE email = {$_POST['email']}")->num_rows;
    if($emailExists == 1){
      $ok = false;
      $_SESSION["dashboard-form-error-email"] = "Ten email jest już ustawiony";
    }
  }

  $passwordWillChange = false;
  if (!empty($_POST['password'])) {
    $passwordWillChange = true;

    if (!preg_match("/^(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/", $_POST['password'])) {
      $ok = false;
      $_SESSION["dashboard-form-error-password"] = "Niepoprawne hasło";
    }

    if ($_POST['password'] != $_POST['repeatedPassword']) {
      $ok = false;
      $_SESSION["dashboard-form-error-repeatedPassword"] = "Hasła muszą być takie same";
    }

    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
  }

  $users = $db->query("SELECT * FROM users WHERE id = {$_SESSION['user']}");
  $user = $users->fetch_assoc();

  if (!password_verify($_POST['oldPassword'], $user['password'])) {
    $ok = false;
    $_SESSION["dashboard-form-error-oldPassword"] = "Hasło nieprawidłowe";
  }

  if ($ok) {

    $query1 = "UPDATE users SET firstname = '{$_POST['firstname']}', lastname = '{$_POST['lastname']}' WHERE id = '{$_SESSION['user']}'";
    $query2 = "UPDATE users SET email = '{$_POST['email']}' WHERE id = '{$_SESSION['user']}'";
    $query3 = "UPDATE users SET password = '{$hashedPassword}' WHERE id = '{$_SESSION['user']}'";
    
    $successful1 = $db->query($query1);
    $successful2 = $emailWillChange ? $db->query($query2) : true;
    $successful3 = $passwordWillChange ? $db->query($query3) : true;

    if ($successful1 && $successful2 && $successful3) {
      $_SESSION['dashboard-form-success'] = 'Zaktualizowano dane';
      header("Location: {$config['site_url']}/dashboard.php?view=account");
      exit;
    }
    else {
      $_SESSION['dashboard-form-error'] = 'Błąd zapytania do bazy danych.';
      header("Location: {$config['site_url']}/dashboard.php?view=account");
      exit;
    }
  }
  else {
    $_SESSION['dashboard-form-error'] = 'Popraw wprowadzone dane.';
    header("Location: {$config['site_url']}/dashboard.php?view=account");
    exit;
  }
}
