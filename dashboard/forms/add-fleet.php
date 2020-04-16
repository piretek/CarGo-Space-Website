<?php

if(!defined('SECURE_BOOT')) exit;

if (isset($_POST['action'])) {

  $excludedKeys = ['action', 'MAX_FILE_SIZE', 'photo', 'price'];
  $ok = true;

  foreach($_POST as $key => $value) {
    if (!in_array($key, $excludedKeys) && empty($value)) {
      $ok = false;
      $_SESSION['dashboard-form-error-'.$key] = 'Pole nie może być puste';
    }
  }

  if (!$ok) {
    $_SESSION['dashboard-form-error'] = 'Popraw wprowadzone dane.';
    header("Location: {$config['site_url']}/dashboard.php?action=add-fleet");
    exit;
  }

  $models = $db->query("SELECT * FROM models WHERE id = '{$_POST['model']}'");
  if ($models->num_rows == 0) {
    $ok = false;
    $_SESSION['dashboard-form-error-model'] = 'Taki model nie istnieje.';
  }
  else {
    $model = $models->fetch_assoc();

    if (!preg_match('^(19|20)\d{2}$^', $_POST['year']) || $model['year_to'] < $_POST['year'] || $model['year_from'] > $_POST['year']) {
      $ok = false;
      $_SESSION['dashboard-form-error-year'] = "Niepoprawny rok. Model był produkowany w latach {$model['year_from']} - {$model['year_to']}.";
    }
  }

  $registrations = $db->query(sprintf("SELECT * FROM cars WHERE registration = '%s'", $db->real_escape_string($_POST['registration'])));
  if ($registrations->num_rows != 0) {
    $ok = false;
    $_SESSION['dashboard-form-error-registration'] = 'Taki pojazd już istnieje.';
  }

  if (strlen($_POST['engine']) > 20) {
    $ok = false;
    $_SESSION['dashboard-form-error-engine'] = 'To pole może mieć maks. 20 znaków.';
  }

  if (strlen($_POST['fuel']) > 15) {
    $ok = false;
    $_SESSION['dashboard-form-error-fuel'] = 'To pole może mieć maks. 15 znaków.';
  }

  if (strlen($_POST['clutch']) > 20) {
    $ok = false;
    $_SESSION['dashboard-form-error-clutch'] = 'To pole może mieć maks. 20 znaków.';
  }

  if (strlen($_POST['registration']) > 10) {
    $ok = false;
    $_SESSION['dashboard-form-error-registration'] = 'To pole może mieć maks. 10 znaków.';
  }

  if (!$ok) {
    $_SESSION['dashboard-form-error'] = 'Popraw wprowadzone dane.';
    header("Location: {$config['site_url']}/dashboard.php?action=add-fleet");
    exit;
  }

  $photo = '';
  $uploadDirectory = realpath(dirname(__DIR__, 2).$config['car_photo_upload_dir']);
  $allowedMimes = ['image/png', 'image/jpeg', 'image/jpg', 'image/jfif'];

  if ($_FILES['photo']['error'] == UPLOAD_ERR_OK || $_FILES['photo']['error'] == UPLOAD_ERR_NO_FILE) {
    if (!in_array($_FILES['photo']['type'], $allowedMimes) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
      $ok = false;
      $_SESSION['dashboard-form-error-photo'] = "Błędny format pliku";
    }
    else if ($_FILES['photo']['error'] == UPLOAD_ERR_OK) {
      $extension = explode('.', $_FILES['photo']['name']);
      $extension = $extension[array_key_last($extension)];

      $photo = md5(time().$_FILES['photo']['name']).'.'.$extension;

      $newName = $uploadDirectory.'/'.$photo;
      $tempName = $_FILES['photo']['tmp_name'];

      if( !move_uploaded_file($tempName, $newName) ) {
        $ok = false;
        $_SESSION['dashboard-form-error-photo'] = "Błąd podczas przenoszenia zdjęcia do docelowej lokalizacji.";
      }
    }
  }
  else {
    $ok = false;
    switch($_FILES['photo']['error']) {
      case UPLOAD_ERR_INI_SIZE  :
      case UPLOAD_ERR_FORM_SIZE :
        $_SESSION['dashboard-form-error-photo'] = "Przekroczony maksymalny rozmiar zdjęcia.";
        break;
      case UPLOAD_ERR_PARTIAL :
        $_SESSION['dashboard-form-error-photo'] = "Odebrano tylko część zdjęcia.";
        break;
      case UPLOAD_ERR_NO_TMP_DIR:
        $ $_SESSION['dashboard-form-error-photo'] = "Brak dostępu do katalogu tymczasowego.";
        break;
      case UPLOAD_ERR_CANT_WRITE:
        $_SESSION['dashboard-form-error-photo'] = "Nie udało się zapisać zdjęcia na dysku serwera.";
        break;
      case UPLOAD_ERR_EXTENSION:
        $_SESSION['dashboard-form-error-photo'] = "Ładowanie zdjęcia przerwane przez rozszerzenie PHP.";
        break;
      default :
        $_SESSION['dashboard-form-error-photo'] = "Nieznany typ błędu.";
        break;
    }
  }

  if ($ok) {
    $query = sprintf("INSERT INTO `cars` VALUES (null, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');",
      $db->real_escape_string($_POST['model']),
      $db->real_escape_string($_POST['year']),
      $db->real_escape_string($_POST['engine']),
      $db->real_escape_string($_POST['fuel']),
      $db->real_escape_string($_POST['clutch']),
      $db->real_escape_string($_POST['registration']),
      $db->real_escape_string(empty($_POST['price']) ? '0.00' : $_POST['price']),
      $db->real_escape_string($photo)
    );

    $successful = $db->query($query);

    if ($successful) {
      $_SESSION['dashboard-form-success'] = 'Dodano nowy pojazd';
      header("Location: {$config['site_url']}/dashboard.php?view=fleet");
      exit;
    }
    else {
      $_SESSION['dashboard-form-error'] = 'Błąd zapytania do bazy danych.';
      header("Location: {$config['site_url']}/dashboard.php?action=add-fleet");
      exit;
    }
  }
  else {
    $_SESSION['dashboard-form-error'] = 'Popraw wprowadzone dane.';
    header("Location: {$config['site_url']}/dashboard.php?action=add-fleet");
    exit;
  }
}

?>

<h2>Dodaj nowy pojazd</h2>

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
    <label for='model'>Model</label><?php

    $models = $db->query("SELECT models.id, models.model as name, models.year_from, models.year_to, brands.name as brand, types.name AS type FROM ((models INNER JOIN brands ON brands.id = models.brand) INNER JOIN types ON types.id = models.type)");
    if ($models->num_rows == 0) : ?>

      <p id='model'>Brak modeli w systemie. Dodaj je <a href="dashboard.php?action=add-model">we flocie.</a></p>

    <?php else :
      $maxYear = 0; ?>

      <select id='model' name='model' class='input--select'>
      <?php while($model = $models->fetch_assoc()) : $maxYear = $maxYear < $model['year_to'] ? (int) $model['year_to'] : $maxYear ?>

        <option value='<?= $model['id'] ?>'><?= "{$model['brand']} {$model['name']} ({$model['type']}) | {$model['year_from']} - {$model['year_to']}" ?></option>

      <?php endwhile; ?>
      </select>
    <?php endif;?>
  </div>
  <div class="column col-100">
    <?php input('year', 'Rok produkcji', isset($maxYear) ? $maxYear : '', 'Podaj rok produkcji pojazdu', 'number') ?>
  </div>
  <div class="column col-100">
    <?php input('engine', 'Silnik', '', 'Podaj parametry silnika') ?>
  </div>
  <div class="column col-100">
    <?php input('fuel', 'Paliwo', '', 'Podaj rodzaj używanego paliwa (Benzyna, Diesel, LPG)') ?>
  </div>
  <div class="column col-100">
    <p>Skrzynia biegów</p>
    <?php input('clutch-auto', 'Automatyczna', 'Automatyczna', 'clutch', 'radio', null, ['checked' => 'checked']) ?>
    <?php input('clutch-manual', 'Manualna', 'Manualna', 'clutch', 'radio') ?>
  </div>
  <div class="column col-100">
    <?php input('registration', 'Rejestracja') ?>
  </div>
  <div class="column col-100">
    <?php input('price', 'Cena wypożyczenia za 1 dobę:', '', 'Cena w złotówkach', 'number', null, ['step' => '0.01']); ?>
    <p>Jeżeli pole pozostanie puste, cena zostanie odziedziczona po cenie modelu.</p>
  </div>
  <div class="column col-100">
    <?php input('photo', 'Zdjęcie pojazdu', '', '', 'file', null, ['accept' => "image/png, image/jpeg, image/jpg, image/jfif"] ) ?>
    <input type="hidden" name="MAX_FILE_SIZE" value="4194304" />
  </div>
</div>
