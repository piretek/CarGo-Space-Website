<?php

if(!defined('SECURE_BOOT')) exit;

if (isset($_POST['action'])) {

  $excludedKeys = ['action', 'brand', 'type'];

  $ok = true;

  foreach($_POST as $key => $value) {
    if (!in_array($key, $excludedKeys) && empty($value)) {
      $ok = false;
      $_SESSION['dashboard-form-error-'.$key] = 'Pole nie może być puste';
    }
  }

  if (!$ok) {
    $_SESSION['dashboard-form-error'] = 'Popraw wprowadzone dane.';
    header("Location: {$config['site_url']}/dashboard.php?action=add-model");
    exit;
  }

  if (strlen($_POST['name']) > 30) {
    $ok = false;
    $_SESSION['dashboard-form-error-name'] = 'Nazwa nie może mieć więcej niż 30 znaków';
  }

  if (!preg_match('^(19|20)\d{2}$^', $_POST['year_from'])) {
    $ok = false;
    $_SESSION['dashboard-form-error-year_from'] = 'Niepoprawny rok';
  }

  if (!preg_match('^(19|20)\d{2}$^', $_POST['year_to']) || $_POST['year_to'] > date('Y')) {
    $ok = false;
    $_SESSION['dashboard-form-error-year_to'] = 'Niepoprawny rok';
  }

  if ($ok) {
    $query = sprintf("INSERT INTO models VALUES (null, '%s', '%s', '%s', '%s', '%s', '%s');",
      $db->real_escape_string($_POST['brand']),
      $db->real_escape_string($_POST['type']),
      $db->real_escape_string($_POST['name']),
      $db->real_escape_string($_POST['year_from']),
      $db->real_escape_string($_POST['year_to']),
      $db->real_escape_string($_POST['price'])
    );

    $successful = $db->query($query);

    if ($successful) {
      $_SESSION['dashboard-form-success'] = 'Dodano nowy model';
      header("Location: {$config['site_url']}/dashboard.php?view=fleet");
      exit;
    }
    else {
      $_SESSION['dashboard-form-error'] = 'Błąd zapytania do bazy danych.';
      header("Location: {$config['site_url']}/dashboard.php?action=add-model");
      exit;
    }
  }
  else {
    $_SESSION['dashboard-form-error'] = 'Popraw wprowadzone dane.';
    header("Location: {$config['site_url']}/dashboard.php?action=add-model");
    exit;
  }
}

?>

<h2>Dodaj nowy model</h2>

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
    <label for='brand'>Marka</label><?php

    $brands = $db->query("SELECT * FROM brands");
    if ($brands->num_rows == 0) : ?>

      <p id='brand'>Brak marek w systemie. Dodaj je <a href="dashboard.php?action=add-brand">we flocie.</a></p>

    <?php else : ?>
        <select id='brand' name='brand' class='input--select'>
        <?php while($brand = $brands->fetch_assoc()) : ?>

          <option value='<?= $brand['id'] ?>'><?= $brand['name'] ?></option>

        <?php endwhile; ?>
        </select>
    <?php endif;?>

  </div>
  <div class="column col-100">
    <?php input('name', 'Nazwa'); ?>
  </div>
  <div class="column col-100">

    <label for='type'>Typ</label><?php

    $types = $db->query("SELECT * FROM types");
    if ($types->num_rows == 0) : ?>

      <p id='type'>Brak typów w systemie. Dodaj je <a href="dashboard.php?action=add-type">we flocie.</a></p>

    <?php else : ?>
        <select id='type' name='type' class='input--select'>
        <?php while($type = $types->fetch_assoc()) : ?>

          <option value='<?= $type['id'] ?>'><?= $type['name'] ?></option>

        <?php endwhile; ?>
        </select>
    <?php endif;?>

  </div>
  <div class="column col-100">
    <p>Okres produkcji modelu</p>
    <?php input('year_from', 'Od:', '2000', 'Rok rozpoczęcia produkcji', 'number'); ?>
    <?php input('year_to', 'Do:', date('Y'), 'Rok zakończenia produkcji', 'number'); ?>
  </div>
  <div class="column col-100">
    <?php input('price', 'Cena wypożyczenia za 1 dobę:', '', 'Cena w złotówkach', 'number', null, ['step' => '0.01']); ?>
  </div>
</div>
