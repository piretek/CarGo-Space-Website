<?php

if(!defined('SECURE_BOOT')) exit;

if (isset($_POST['action'])) {

  $excludedKeys = ['action', 'car-presented-price', 'client-id', 'search-for-client', 'client'];
  $ok = true;

  foreach($_POST as $key => $value) {
    if (!in_array($key, $excludedKeys) && empty($value)) {
      $ok = false;
      $_SESSION['dashboard-form-error-'.$key] = 'Pole nie może być puste';
    }
  }

  if ($_POST['client-id'] == 0) {
    foreach($_POST['client'] as $key => $value) {
      if (!in_array($key, $excludedKeys) && empty($value)) {
        $ok = false;
        $_SESSION['dashboard-form-error-client['.$key.']'] = 'Pole nie może być puste';
      }
    }

    if (!$ok) {
      $_SESSION['bad-cl-creds'] = true;
      $_SESSION['dashboard-form-error-client-id'] = "Proszę wybrać klienta.";
    }
  }

  if (!$ok) {
    $_SESSION['dashboard-form-error'] = 'Popraw wprowadzone dane.';
    header("Location: {$config['site_url']}/dashboard.php?action=add-rent");
    exit;
  }

  $cars = $db->query( sprintf("SELECT * FROM cars WHERE id = '%s'",
    $db->real_escape_string($_POST['car'])
  ));

  if ($cars->num_rows == 0) {
    $ok = false;
    $_SESSION['dashboard-form-error-car'] = 'Taki pojazd nie istnieje.';
  }

  list($from_year, $from_month, $from_day) = explode("-",$_POST['from']);
  $from = mktime(0, 0, 0, $from_month, $from_day, $from_year);

  list($to_year, $to_month, $to_day) = explode("-",$_POST['to']);
  $to = mktime(23, 59, 59, $to_month, $to_day, $to_year);

  $availableFrom = strtotime("now +1 day midnight");
  $availableToFrom = strtotime("now +2 day midnight");
  $availableTo = strtotime("now +3 months");

  if($from < $availableFrom){
    $ok = false;
    $_SESSION['dashboard-form-error-from'] = "Data nie może być wcześniejsza niż ".date("d.m.Y", $availableFrom)."!";
  }

  if($to < $availableToFrom){
    $ok = false;
    $_SESSION['dashboard-form-error-to'] = "Data nie może być wcześniejsza niż ".date("d.m.Y", $availableToFrom)."!";
  }

  if($to > $availableTo){
    $ok = false;
    $_SESSION['dashboard-form-error-to'] = "Data nie może być późniejsza niż ".date("d.m.Y", $availableTo)."!";
  }

  if ($_POST['client-id'] == 0) {
    if(strlen($_POST['client']['pesel']) != 11){
      $ok = false;
      $_SESSION['dashboard-form-error-client[pesel]'] = "PESEL musi mieć 11 cyfr!";
    }

    if(strlen($_POST['client']['phone']) != 9){
      $ok = false;
      $_SESSION['dashboard-form-error-client[phone]'] = "Numer telefonu musi mieć 9 cyfr!";
    }

    if (!filter_var($_POST['client']["email"], FILTER_VALIDATE_EMAIL)) {
      $ok = false;
      $_SESSION['dashboard-form-error-client[email]'] = "Niepoprawny email";
    }

    if (!$ok) {
      $_SESSION['bad-cl-creds'] = true;
      $_SESSION['dashboard-form-error-client-id'] = "Proszę wybrać klienta.";
    }
  }

  if (isset($_POST['accept'])) {
    $isRentedQuery = sprintf("SELECT * FROM rents WHERE car = '%s' AND ((begin <= '%s' AND end >= '%s') OR (begin <= '%s' AND end >= '%s')) AND (status = '3' OR status = '2')",
    $db->real_escape_string($_POST['car']),
      $db->real_escape_string($from),
      $db->real_escape_string($from),
      $db->real_escape_string($to),
      $db->real_escape_string($to)
    );

    $isRented = $db->query($isRentedQuery)->num_rows != 0 ? true : false;

    if ($isRented) {
      $ok = false;
      $_SESSION['dashboard-form-error-car'] = 'To auto jest już wynajmowane w tym okresie. Znajdź inny dogodny termin lub zmień pojazd.';
    }
  }

  if ($ok) {
    if ($_POST['client-id'] == 0) {
      $query = sprintf("INSERT INTO clients VALUES (null, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
        $db->real_escape_string($_POST['client']['name']),
        $db->real_escape_string($_POST['client']['surname']),
        $db->real_escape_string($_POST['client']['city']),
        $db->real_escape_string($_POST['client']['street']),
        $db->real_escape_string($_POST['client']['number']),
        $db->real_escape_string($_POST['client']['phone']),
        $db->real_escape_string($_POST['client']['email']),
        $db->real_escape_string($_POST['client']['pesel'])
      );

      $client_success = $db->query($query);
      if (!$client_success) {
        $_SESSION['dashboard-form-error'] = 'Błąd tworzenia nowego klienta.';
        header("Location: {$config['site_url']}/dashboard.php?action=add-rent");
        exit;
      }
      else {
        $clientId = $db->insert_id;
      }
    }
    else {
      $clientId = $_POST['client-id'];
    }

    $client = $db->query("SELECT * FROM clients WHERE id = '$clientId'")->fetch_assoc();

    if (isset($_POST['accept'])) {
      $status = 2;
    }
    else {
      $status = 0;
    }

    $query = sprintf("INSERT INTO rents VALUES (null, '%s', '%s', '%s', '%s', '%s', '%s', '%s');",
      $db->real_escape_string($clientId),
      $db->real_escape_string($_POST['car']),
      $db->real_escape_string($from),
      $db->real_escape_string($to),
      $db->real_escape_string(isset($_POST['insurance']) ? '1' : '0'),
      $db->real_escape_string($status),
      time()
    );

    $successful = $db->query($query);
    $rentId = $db->insert_id;

    $rentedCar = carinfo($_POST['car']);

    $sentMain = send_mail($client, 'rent-created', [
      'rent-id' => $rentId,
      'rent-car' => "{$rentedCar['brand']} {$rentedCar['model']} {$rentedCar['engine']} {$rentedCar['fuel']} {$rentedCar['registration']}",
      'rent-time' => date('d.m.Y', $from).' - '.date('d.m.Y', $to),
      'rent-price' => rent_price($rentId).' zł'
    ]);

    if ($status == 2) {
      $sentStatusChange = send_mail($client, 'rent-status-changed', [
        'rent-id' => $rentId,
        'rent-car' => "{$rentedCar['brand']} {$rentedCar['model']} {$rentedCar['engine']} {$rentedCar['fuel']} {$rentedCar['registration']}",
        'rent-time' => date('d.m.Y', $from).' - '.date('d.m.Y', $to),
        'rent-price' => rent_price($rentId).' zł',
        'rent-newstatus' => 2
      ]);
    }
    else {
      $sentStatusChange = true;
    }

    if (!$sentMain || !$sentStatusChange) {
      $_SESSION['dashboard-form-error'] = '';
      if ($sentMain) $_SESSION['dashboard-form-error'] .= 'Błąd wysyłania maila o utworzeniu wypożyczenia.<br />';
      if ($sentStatusChange) $_SESSION['dashboard-form-error'] .= 'Błąd wysyłania maila o potwierdzeniu wypożyczenia.';
    }

    if ($successful) {
      $_SESSION['dashboard-form-success'] = 'Dodano nowe wypożyczenie';
      header("Location: {$config['site_url']}/dashboard.php?view=rents");
      exit;
    }
    else {
      $_SESSION['dashboard-form-error'] = 'Błąd zapytania do bazy danych.';
      header("Location: {$config['site_url']}/dashboard.php?action=add-rent");
      exit;
    }
  }
  else {
    $_SESSION['dashboard-form-error'] = 'Popraw wprowadzone dane.';
    header("Location: {$config['site_url']}/dashboard.php?action=add-rent");
    exit;
  }
}

$doWeHaveAnyClient = $db->query("SELECT * FROM clients")->num_rows != 0 ? true : false;

?>

<h2>Dodaj nowe wypożyczenie</h2>

<?php if (isset($_SESSION['dashboard-form-error'])) : ?>
  <span class='error'>Błąd: <?= $_SESSION['dashboard-form-error'] ?></span>
  <?php unset($_SESSION['dashboard-form-error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['dashboard-form-success'])) : ?>
  <span class='success'><?= $_SESSION['dashboard-form-success'] ?></span>
  <?php unset($_SESSION['dashboard-form-success']); ?>
<?php endif; ?>

<div class="columns">
  <div class="column col-100 mb-2">
    <a href='dashboard.php?view=rents'><button type='button'>&lt; Powrót</button></a>
  </div>
  <div class="column col-100 mb-2">
    <div class='input--container'>
      <label for='car'>Pojazd</label><?php

      $carIds = $db->query("SELECT id FROM cars");
      if ($carIds->num_rows == 0) : ?>

        <p id='car'>Brak pojazdów w systemie. Dodaj je <a href="dashboard.php?action=add-model">we flocie.</a></p>

      <?php else : ?>
        <input type='hidden' name='car-presented-price' value='' />
        <select id='car' name='car' class='input--select'>
          <option <?= !isset($_GET['car']) ? 'selected' : '' ?> value='0'>Wybierz dostępny pojazd...</option>

          <?php while($carId = $carIds->fetch_assoc()) : $car = carInfo($carId['id']); ?>

          <option <?= isset($_GET['car']) && $_GET['car'] == $carId['id'] ? 'selected' : '' ?> data-price='<?= rent_price($carId['id'], true) ?>' value='<?= $carId['id'] ?>'><?= "{$car['brand']} {$car['model']} ({$car['type']} {$car['engine']} - {$car['fuel']}) {$car['year']} r. | {$car['registration']} | ".rent_price($carId['id'], true)." zł" ?></option>

          <?php endwhile; ?>
        </select>

        <span class='input--error'><?php $errField = 'car'; if (isset($_SESSION['dashboard-form-error-'.$errField])) { echo $_SESSION['dashboard-form-error-'.$errField]; unset($_SESSION['dashboard-form-error-'.$errField]); } ?></span>
      <?php endif;?>
    </div>
  </div>
  <div class="column col-50">
    <h3>Dane dot. wypożyczenia</h3>
    <?php input('from', 'Od:', isset($_GET['from']) && !empty($_GET['from']) ? $_GET['from'] : '', '', 'date') ?>
    <?php input('to', 'Do:', isset($_GET['to']) && !empty($_GET['to']) ? $_GET['to'] : '', '', 'date') ?>
    <?php input('insurance', 'Z ubezpieczeniem', 'on', 'insurance', 'checkbox', null, ['checked' => 'checked']) ?>
    <?php input('accept', 'Potwierdzić od razu wypożyczenie?', 'on', 'accept', 'checkbox', null, ['checked' => 'checked']) ?>
  </div>
  <div class='column col-50'>
    <h3>Dane dot. klienta</h3>

    <div class="columns columns__no-spacing cards">
      <?php if ($doWeHaveAnyClient) : ?><div for='search' class="column col-50 card">Szukaj klienta</div><?php endif; ?>
      <div for='new' class="column col-50 card <?php if ( isset($_SESSION['bad-cl-creds']) ) { echo 'active'; } ?>">Nowy klient</div>
    </div>

    <?php if ($doWeHaveAnyClient) : ?>

    <div id='search' class='card-box'>
      <?php input('search-for-client', 'Znajdź klienta w aktualnym zbiorze:', '', 'np. Imię i nazwisko, PESEL') ?>
      <?php input('client-id', '', '0', '', 'hidden') ?>
      <p class='search-results'><strong>Wybrany klient:</strong> <span></span></p>
      <table class='search-results'>
        <thead>
          <tr>
            <th>Imię i nazwisko</th>
            <th>PESEL</th>
            <th>Akcje</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="3" class="no-results">Wpisz min. 2 znaki.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <?php endif; ?>

    <div id='new' class='card-box <?php if ( isset($_SESSION['bad-cl-creds']) ) { echo 'active'; unset($_SESSION['bad-cl-creds']); } ?>'>
      <?php input('client[name]', 'Imię:', '', 'np. Jan') ?>
      <?php input('client[surname]', 'Nazwisko:', '', 'np. Kowalski') ?>
      <?php input('client[pesel]', 'PESEL:', '', 'Numer PESEL (11 cyfr)', 'text', null, [
        'minlength' => '11',
        'maxlength' => '11'
      ]) ?>
      <?php input('client[city]', 'Miejscowość:', '', 'np. Warszawa') ?>
      <?php input('client[street]', 'Ulica:', '', 'np. ul. Wiejska') ?>
      <?php input('client[number]', 'Numer domu/bloku:', '', 'np. 3/1') ?>
      <?php input('client[phone]', 'Numer telefonu:', '', 'np. 555 555 555') ?>
      <?php input('client[email]', 'Email:', '', 'np. jan.kowalski@example.com', 'email') ?>
    </div>
  </div>
  <script>
    const clients = [
      <?php
      // Tak Sorko/Sorze, wiemy że mogliśmy wstawić tutaj AJAXa i wykonać zapytanie XHR, ale!...
      // Michał i Paulina nie wiedzą co to i jak się posługiwać za pomocą takiego prostego API,
      // dlatego nie utrudnialiśmy tego zadania i skorzystaliśmy z tej możliwości, że można pisać PHP w tagach script.
      // Jak coś działa, tzn. że nie jest głupie :)

      $clients = $db->query('SELECT id, name, surname, pesel FROM clients');
      if ($clients->num_rows != 0) {
        while($client = $clients->fetch_assoc()) {
          printf("{ id: parseInt('%s'), name: '%s', surname: '%s', pesel: '%s' },\n",
            $client['id'],
            $client['name'],
            $client['surname'],
            $client['pesel'],
          );
        }
      }

      ?>
    ]
  </script>
  <script src='assets/js/rent.js'></script>
</div>
