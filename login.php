<?php
require_once 'includes/init.php';

if (isset($_POST['email']) && !isset($_SESSION['user'])) {

  $acceptedKeys = [
    'email',
    'password'
  ];

  foreach($acceptedKeys as $key) {
    if (!array_key_exists($key, $_POST)) {
      $_SESSION['login-form-error'] = 'Niepoprawny login lub hasło.';
      header("Location: {$config['site_url']}/auth.php");
      exit();
    }
  }

  $email = $db->real_escape_string( htmlentities(strtolower($_POST['email']), ENT_QUOTES, "UTF-8") );
  $password = $db->real_escape_string( $_POST['password'] );

  $users = $db->query(sprintf("SELECT * FROM users WHERE email = '%s'",
    $email
  ));

  if ($users->num_rows == 0) {
    $_SESSION['login-form-error'] = 'Niepoprawny login lub hasło.';
    header("Location: {$config['site_url']}/auth.php");
    exit();
  }
  else {
    $user = $users->fetch_assoc();

    if (password_verify($password, $user['password'])) {

      $_SESSION['user'] = (int) $user['id'];

      header("Location: {$config['site_url']}/dashboard.php");
      exit();
    }
    else {
      $_SESSION['login-form-error'] = 'Niepoprawny login lub hasło.';
      header("Location: {$config['site_url']}/auth.php");
      exit();
    }
  }
}
