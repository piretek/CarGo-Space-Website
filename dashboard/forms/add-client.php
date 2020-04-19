<?php

if(!defined('SECURE_BOOT')) exit;

if (isset($_POST['action'])) {

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
    $query = sprintf("INSERT INTO clients VALUES (null, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');",
      $db->real_escape_string($_POST['name']),
      $db->real_escape_string($_POST['surname']),
      $db->real_escape_string($_POST['city']),
      $db->real_escape_string($_POST['street']),
      $db->real_escape_string($_POST['number']),
      $db->real_escape_string($_POST['phone']),
      $db->real_escape_string($_POST['email']),
      $db->real_escape_string($_POST['pesel'])
    );

    $successful = $db->query($query);

    if ($successful) {
      $_SESSION['dashboard-form-success'] = 'Dodano nowego klienta';
      header("Location: {$config['site_url']}/dashboard.php?view=clients");
      exit;
    }
    else {
      $_SESSION['dashboard-form-error'] = 'Błąd zapytania do bazy danych.';
      header("Location: {$config['site_url']}/dashboard.php?action=add-client");
      exit;
    }
  }
  else {
    $_SESSION['dashboard-form-error'] = 'Popraw wprowadzone dane.';
    header("Location: {$config['site_url']}/dashboard.php?action=add-client");
    exit;
  }
}

?>

<h2>Dodaj nowego klienta</h2>

<?php if (isset($_SESSION['dashboard-form-error'])) : ?>
  <span class='error'>Błąd: <?= $_SESSION['dashboard-form-error'] ?></span>
  <?php unset($_SESSION['dashboard-form-error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['dashboard-form-success'])) : ?>
  <span class='success'><?= $_SESSION['dashboard-form-success'] ?></span>
  <?php unset($_SESSION['dashboard-form-success']); ?>
<?php endif; ?>

<div class="columns">
  <div class="column col-100">
    <?php input('name', 'Imię:', '', 'np. Jan') ?>
  </div>
  <div class="column col-100">
    <?php input('surname', 'Nazwisko:', '', 'np. Kowalski') ?>
  </div>
  <div class="column col-100">
    <?php input('pesel', 'PESEL:', '', 'Numer PESEL (11 cyfr)', 'text', null, [
      'minlength' => '11',
      'maxlength' => '11'
    ]) ?>
    <?php input('city', 'Miejscowość:', '', 'np. Warszawa') ?>
  </div>
  <div class="column col-100">
    <?php input('street', 'Ulica:', '', 'np. ul. Wiejska') ?>
  </div>
  <div class="column col-100">
    <?php input('number', 'Numer domu/bloku:', '', 'np. 3/1') ?>
  </div>
  <div class="column col-100">
    <?php input('phone', 'Numer telefonu:', '', 'np. 555 555 555') ?>
  </div>
  <div class="column col-100">
    <?php input('email', 'Email:', '', 'np. jan.kowalski@example.com', 'email') ?>
  </div>
</div>
