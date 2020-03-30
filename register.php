<?php
require_once 'includes/init.php';

function redirect_error($message = null) {
  global $config;

  $file = 'register';
  if ( !is_null($message) ) $_SESSION["{$file}-form-error"] = $message;

  header("Location: {$config['site_url']}/auth.php");
  exit();
}

if (isset($_POST['email'])) {

  $acceptedKeys = [
    'email',
    'password',
    'repeat-password'
  ];

  foreach($acceptedKeys as $key) {
    if (!array_key_exists($key, $_POST)) {
      redirect_error();
    }
  }

  $email = $db->real_escape_string( htmlentities(strtolower($_POST['email']), ENT_QUOTES, "UTF-8") );
  $password = $db->real_escape_string( htmlentities($_POST['password'], ENT_QUOTES, "UTF-8") );
  $repeatedPassword = $db->real_escape_string( htmlentities($_POST['repeat-password'], ENT_QUOTES, "UTF-8") );

  $ok = true;

  foreach($_POST as $key => $value) {
    if (empty($_POST[$key])) {
      $ok = false;
      $_SESSION["register-form-error-{$key}"] = 'Pole nie może być puste';
    }
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $ok = false;
    $_SESSION["register-form-error-email"] = "Niepoprawny email";
  }

  if (!preg_match("/^(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/", $password)) {
    $ok = false;
    $_SESSION["register-form-error-password"] = "Niepoprawne hasło";
  }

  if ($password != $repeatedPassword) {
    $ok = false;
    $_SESSION["register-form-error-repeat-password"] = "Hasła muszą być takie same";
  }

  if (!$ok) {
    redirect_error('Popraw pola');
  }
  else {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $createUserQuery = "INSERT INTO users (email, password, firstname, lastname) VALUES ('{$email}', '{$hashedPassword}','','')";

    $response = $db->query($createUserQuery);
    if ($response) {
      $_SESSION['register-form-success'] = 'Użytkownik pomyślnie zarejestrowany.';
      header("Location: {$config['site_url']}/auth.php");
      exit();
    }
    else {
      redirect_error('Błąd podczas tworzenia użytkownika w bazie danych.');
    }
  }
}
