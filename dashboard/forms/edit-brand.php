<?php

if(!defined('SECURE_BOOT')) exit;

if (isset($_POST['action'])) {

  if ($_POST['action'] == 'edit-brand') {

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

    $query = sprintf("UPDATE brands SET name = '%s' WHERE id = '%s'",
      $db->real_escape_string($_POST['name']),
      $db->real_escape_string($_POST['id'])
    );

    $successful = $db->query($query);

    if ($successful) {
      $_SESSION['dashboard-form-success'] = 'Zedytowano markę';
      header("Location: {$config['site_url']}/dashboard.php?view=fleet");
      exit;
    }
    else {
      $_SESSION['dashboard-form-error'] = 'Błąd zapytania do bazy danych.';
      header("Location: {$config['site_url']}/dashboard.php?action=edit-type&id=".$_POST['id']);
      exit;
    }
  }
  else if ($_POST['action'] == 'delete-brand') {
    $query = sprintf("DELETE FROM brands WHERE id = '%s'",
      $db->real_escape_string($_POST['id'])
    );

    $successful = $db->query($query);
    if ($successful) {
      $_SESSION['dashboard-form-success'] = 'Usunięto markę';
      header("Location: {$config['site_url']}/dashboard.php?view=fleet");
      exit;
    }
    else {
      $error = $db->errno;
      $_SESSION['dashboard-form-error'] = $error === 1451 ? 'Usuń model, który posiada przydzieloną markę.' : 'Błąd zapytania do bazy danych.';
      header("Location: {$config['site_url']}/dashboard.php?view=fleet");
      exit;
    }
  }
}

$query = sprintf("SELECT * FROM brands WHERE id = '%s'", $db->real_escape_string($_GET['id']));
$brand = $db->query($query)->fetch_assoc();

?>

<h2>Edytuj markę</h2>

<?php if (isset($_SESSION['dashboard-form-error'])) : ?>
  <span class='error'>Błąd: <?= $_SESSION['dashboard-form-error'] ?></span>
  <?php unset($_SESSION['dashboard-form-error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['dashboard-form-success'])) : ?>
  <span class='success'><?= $_SESSION['dashboard-form-success'] ?></span>
  <?php unset($_SESSION['dashboard-form-success']); ?>
<?php endif; ?>

<?php input('name', 'Nazwa typu', $brand['name']); ?>
