<?php

if(!defined('SECURE_BOOT')) exit;

if (isset($_POST['action'])) {

  if ($_POST['action'] == 'edit-rent') {

    $checkedKeys = ['car', 'to', 'from'];

    $ok = true;

    foreach($_POST as $key => $value) {
      if (in_array($key, $checkedKeys) && empty($value)) {
        $ok = false;
        $_SESSION['dashboard-form-error-'.$key] = 'Pole nie może być puste';
      }
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

    if ($ok) {

      $query = sprintf("UPDATE rents SET car = '%s', begin = '%s', end = '%s' WHERE id = '%s'",
        $db->real_escape_string($_POST['car']),
        $db->real_escape_string($from),
        $db->real_escape_string($to),
        $db->real_escape_string($_POST['id']),
      );
      $successful = $db->query($query);

      $rent = $db->query(sprintf("SELECT rents.* , clients.name, clients.surname, clients.email FROM rents INNER JOIN clients ON clients.id = rents.client WHERE rents.id = '%s'",
        $db->real_escape_string($_POST['id'])
      ))->fetch_assoc();

      $rentedCar = carinfo($rent['car']);
      $sent = send_mail($rent, 'rent-edited', [
        'rent-id' => $rent['id'],
        'rent-car' => "{$rentedCar['brand']} {$rentedCar['model']} {$rentedCar['engine']} {$rentedCar['fuel']} {$rentedCar['registration']}",
        'rent-time' => date('d.m.Y', $from).' - '.date('d.m.Y', $to),
        'rent-price' => rent_price($db->insert_id).' zł'
      ]);

      if (!$sent) {
        $_SESSION['dashboard-form-error'] = 'Błąd podczas wysyłania wiadomości do klienta.';
      }

      if ($successful) {
        $_SESSION['dashboard-form-success'] = 'Zmieniono dane';
        header("Location: {$config['site_url']}/dashboard.php?view=rent&id=".$_POST['id']);
        exit;
      }
      else {
        $_SESSION['dashboard-form-error'] = 'Błąd zapytania do bazy danych.';
        header("Location: {$config['site_url']}/dashboard.php?view=rent&id=".$_POST['id']);
        exit;
      }
    }
    else {
      $_SESSION['dashboard-form-error'] = 'Popraw wprowadzone dane.';
      header("Location: {$config['site_url']}/dashboard.php?view=rent&id=".$_POST['id']);
      exit;
    }
  }
  else if ($_POST['action'] == 'edit-rent-status') {

    $rent = $db->query(sprintf("SELECT rents.* , clients.name, clients.surname, clients.email FROM rents INNER JOIN clients ON clients.id = rents.client WHERE rents.id = '%s'",
      $db->real_escape_string($_POST['id'])
    ))->fetch_assoc();

    if ($_POST['new-status'] == 3) {

      $isRentedQuery = sprintf("SELECT * FROM rents WHERE ((begin <= '%s' AND end >= '%s') OR (begin <= '%s' AND end >= '%s')) AND status = '3'",
        $db->real_escape_string($rent['begin']),
        $db->real_escape_string($rent['begin']),
        $db->real_escape_string($rent['end']),
        $db->real_escape_string($rent['end']),
      );

      $isRented = $db->query($isRentedQuery)->num_rows != 0 ? true : false;

      if ($isRented) {
        $_SESSION['dashboard-form-error'] = 'To auto jest już wynajmowane w tym okresie. Znajdź inny dogodny termin lub zmień pojazd.';
        header("Location: {$config['site_url']}/dashboard.php?view=rent&id=".$_POST['id']);
        exit;
      }
    }

    $query = sprintf("UPDATE rents SET status = '%s' WHERE id = '%s'",
      $db->real_escape_string($_POST['new-status']),
      $db->real_escape_string($_POST['id']),
    );

    $successful = $db->query($query);

    $rentedCar = carinfo($rent['car']);
    $sent = send_mail($rent, 'rent-status-changed', [
      'rent-id' => $rent['id'],
      'rent-car' => "{$rentedCar['brand']} {$rentedCar['model']} {$rentedCar['engine']} {$rentedCar['fuel']} {$rentedCar['registration']}",
      'rent-time' => date('d.m.Y', $rent['begin']).' - '.date('d.m.Y', $rent['end']),
      'rent-price' => rent_price($rent['id']).' zł',
      'rent-newstatus' => $_POST['new-status']
    ]);

    if (!$sent) {
      $_SESSION['dashboard-form-error'] = 'Błąd podczas wysyłania wiadomości do klienta.';
    }

    if ($successful) {
      $_SESSION['dashboard-form-success'] = 'Zmieniono status';
      header("Location: {$config['site_url']}/dashboard.php?view=rent&id=".$_POST['id']);
      exit;
    }
    else {
      $_SESSION['dashboard-form-error'] = 'Błąd zapytania do bazy danych.';
      header("Location: {$config['site_url']}/dashboard.php?view=rent&id=".$_POST['id']);
      exit;
    }
  }
  else if ($_POST['action'] == 'delete-rent') {
    $query = sprintf("DELETE FROM rents WHERE id = '%s'",
      $db->real_escape_string($_POST['id'])
    );

    $successful = $db->query($query);
    if ($successful) {
      $_SESSION['dashboard-form-success'] = 'Usunięto wypożyczenie';
      header("Location: {$config['site_url']}/dashboard.php?view=rents");
      exit;
    }
    else {
      $_SESSION['dashboard-form-error'] = 'Błąd zapytania do bazy danych.';
      header("Location: {$config['site_url']}/dashboard.php?view=rents&id=".$_POST['id']);
      exit;
    }
  }
}
