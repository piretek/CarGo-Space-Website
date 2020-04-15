<?php

if(!defined('SECURE_BOOT')) exit;

if (isset($_POST['action'])) {

  if (empty($_POST['name'])) {
    $_SESSION['dashboard-form-error-name'] = 'Pole nie może być puste';
    header("Location: {$config['site_url']}/dashboard.php?action=add-type");
    exit;
  }

  if (strlen($_POST['name']) > 20) {
    $_SESSION['dashboard-form-error-name'] = 'Nazwa nie może mieć więcej niż 20 znaków';
    header("Location: {$config['site_url']}/dashboard.php?action=add-type");
    exit;
  }

  $query = sprintf("INSERT INTO `types` VALUES (null, '%s');", $db->real_escape_string($_POST['name']));
  $successful = $db->query($query);

  if ($successful) {
    $_SESSION['dashboard-form-success'] = 'Dodano nowy typ';
    header("Location: {$config['site_url']}/dashboard.php?view=fleet");
    exit;
  }
  else {
    $_SESSION['dashboard-form-error'] = 'Błąd zapytania do bazy danych.';
    header("Location: {$config['site_url']}/dashboard.php?action=add-type");
    exit;
  }
}

?>

<h2>Dodaj nowy typ</h2>

<?php if (isset($_SESSION['dashboard-form-error'])) : ?>
  <span class='error'>Błąd: <?= $_SESSION['dashboard-form-error'] ?></span>
  <?php unset($_SESSION['dashboard-form-error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['dashboard-form-success'])) : ?>
  <span class='success'><?= $_SESSION['dashboard-form-success'] ?></span>
  <?php unset($_SESSION['dashboard-form-success']); ?>
<?php endif; ?>

<?php input('name', 'Nazwa typu'); ?>
