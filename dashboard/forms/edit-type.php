<?php

if(!defined('SECURE_BOOT')) exit;

if (isset($_POST['action'])) {

  if ($_POST['action'] == 'edit-type') {

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

    $query = sprintf("UPDATE types SET name = '%s' WHERE id = '%s'",
      $db->real_escape_string($_POST['name']),
      $db->real_escape_string($_POST['id'])
    );

    $successful = $db->query($query);

    if ($successful) {
      $_SESSION['dashboard-form-success'] = 'Zedytowano typ';
      header("Location: {$config['site_url']}/dashboard.php?view=fleet");
      exit;
    }
    else {
      $_SESSION['dashboard-form-error'] = 'Błąd zapytania do bazy danych.';
      header("Location: {$config['site_url']}/dashboard.php?action=edit-type&id=".$_POST['id']);
      exit;
    }
  }
  else if ($_POST['action'] == 'delete-type') {
    $query = sprintf("DELETE FROM types WHERE id = '%s'",
      $db->real_escape_string($_POST['id'])
    );

    $successful = $db->query($query);
    if ($successful) {
      $_SESSION['dashboard-form-success'] = 'Usunięto typ';
      header("Location: {$config['site_url']}/dashboard.php?view=fleet");
      exit;
    }
    else {
      $error = $db->errno;
      $_SESSION['dashboard-form-error'] = $error === 1451 ? 'Usuń markę, która posiada przydzielony typ.' : 'Błąd zapytania do bazy danych.';
      header("Location: {$config['site_url']}/dashboard.php?view=fleet");
      exit;
    }
  }
}

$query = sprintf("SELECT * FROM types WHERE id = '%s'", $db->real_escape_string($_GET['id']));
$type = $db->query($query)->fetch_assoc();

?>

<h2>Edytuj typ</h2>

<?php if (isset($_SESSION['dashboard-form-error'])) : ?>
  <span class='error'>Błąd: <?= $_SESSION['dashboard-form-error'] ?></span>
  <?php unset($_SESSION['dashboard-form-error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['dashboard-form-success'])) : ?>
  <span class='success'><?= $_SESSION['dashboard-form-success'] ?></span>
  <?php unset($_SESSION['dashboard-form-success']); ?>
<?php endif; ?>

<?php input('name', 'Nazwa typu', $type['name']); ?>
