<?php

if(!defined('SECURE_BOOT')) exit;

function calcPrice( $calculation, $id, $isCar = false ) {

  $price = (float) str_replace(',', '', rent_price($id, $isCar));

  $calculated = $calculation($price);

  return number_format($calculated, 2);
}

$query = sprintf("SELECT * FROM rents WHERE id = '%s'", $db->real_escape_string($_GET['id']));
$rents = $db->query($query);
if ($rents->num_rows == 0) {
  header('Location: dashboard.php?view=rents');
  exit;
}
$rent = $rents->fetch_assoc();

$car = carinfo($rent['car']);
$clients = $db->query(sprintf("SELECT * FROM clients WHERE id = '%s'", $rent['client']));
$client = $clients->fetch_assoc();

?>

<div class='columns'>
  <div class='column col-100'>
    <a href='dashboard.php?view=rents'><button>&lt; Powrót</button></a>
  </div>
  <div class='columns col-100'>
    <h2>Zarządzanie wypożyczeniem</h2>
  </div>
</div>

<?php if (isset($_SESSION['dashboard-form-error'])) : ?>
  <span class='error'>Błąd: <?= $_SESSION['dashboard-form-error'] ?></span>
  <?php unset($_SESSION['dashboard-form-error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['dashboard-form-success'])) : ?>
  <span class='success'><?= $_SESSION['dashboard-form-success'] ?></span>
  <?php unset($_SESSION['dashboard-form-success']); ?>
<?php endif; ?>


<div class="columns">
  <div class='column col-50'>
    <h4>Informacje o wypożyczeniu</h4>
    <ul>
      <li><strong>Pojazd: </strong> <a href='dashboard.php?view=car&id=<?= $rent['car'] ?>'><?= "{$car['brand']} {$car['model']} - {$car['registration']}" ?></a></li>
      <li><strong>Klient: </strong> <?= $client['surname'].' '.$client['name'].' ('.$client['pesel'].')' ?></li>
      <li><strong>Data zgłoszenia: </strong> <?= date('d.m.Y H:i', $rent['created_at']) ?></li>
      <li><strong>Okres: </strong> <?= date('d.m.Y', $rent['begin']).' - '.date('d.m.Y', $rent['end']) ?></li>
    </ul>
    <h4>Koszty wypożyczenia</h4>
    <ul>
      <li><strong>Cena za 1 dzień: </strong> <?= calcPrice(function($price) { return $price; }, $car['id'], true) ?> zł</li>
      <li><strong>Cena netto: </strong> <?= calcPrice(function($price) { return $price * 0.77; }, $rent['id']) ?> zł</li>
      <li><strong>VAT (23%): </strong> <?= calcPrice(function($price) { return $price * 0.23; }, $rent['id']) ?> zł</li>
      <li><strong>Cena brutto: </strong> <?= rent_price($rent['id']) ?> zł</li>
    </ul>
  </div>
  <div class='column col-50'>
    <div class="columns columns__no-spacing">
      <div class="column col-100 mb-2">
        <h3>Status wypożyczenia</h3>
        <strong class='dashboard-status'><?= $rentStatus[$rent['status']] ?></strong>
        <div class='mt-2'>
          <?php if ($rent['status'] == 0) : ?>
            <form class='as-anchor' method='post'>
              <input type='hidden' name='action' value='edit-rent-status' />
              <input type='hidden' name='id' value='<?= $rent['id'] ?>' />
              <input type='hidden' name='new-status' value='2' />
              <button type='submit' class='accept'>Zaakceptuj</button>
            </form>
            <form class='as-anchor' method='post'>
              <input type='hidden' name='action' value='edit-rent-status' />
              <input type='hidden' name='id' value='<?= $rent['id'] ?>' />
              <input type='hidden' name='new-status' value='1' />
              <button type='submit' class='cancel'>Odrzuć</button>
            </form>
          <?php elseif ($rent['status'] == 2) : ?>
            <form class='as-anchor' method='post'>
              <input type='hidden' name='action' value='edit-rent-status' />
              <input type='hidden' name='id' value='<?= $rent['id'] ?>' />
              <input type='hidden' name='new-status' value='3' />
              <button type='submit' class='accept'>Zgłoś wypożyczenie przez klienta</button>
            </form>
          <?php elseif ($rent['status'] == 3) : ?>
            <form class='as-anchor' method='post'>
              <input type='hidden' name='action' value='edit-rent-status' />
              <input type='hidden' name='id' value='<?= $rent['id'] ?>' />
              <input type='hidden' name='new-status' value='4' />
              <button type='submit' class='cancel'>Zgłoś oddanie pojazdu</button>
            </form>
          <?php endif; ?>
        </div>
      </div>
      <div class="column col-100">
        <?php if ($rent['status'] == 0 || $rent['status'] == 2) : ?>
          <h4>Edycja wypożyczenia</h4>
          <form method='post'>
            <input type='hidden' name='action' value='edit-rent' />
            <input type='hidden' name='id' value='<?= $rent['id'] ?>' />

            <div class='input--container'>
              <label for='car'>Pojazd</label><?php

              $carIds = $db->query("SELECT id FROM cars");
              if ($carIds->num_rows == 0) : ?>

                <p id='car'>Brak pojazdów w systemie. Dodaj je <a href="dashboard.php?action=add-model">we flocie.</a></p>

              <?php else : ?>
                <input type='hidden' name='car-presented-price' value='' />
                <select id='car' name='car' class='input--select'>
                  <option <?= !isset($_GET['car']) ? 'selected' : '' ?> value='0'>Wybierz dostępny pojazd...</option>

                  <?php while($carId = $carIds->fetch_assoc()) : $carInfo = carinfo($carId['id']); ?>

                  <option <?= $car['id'] == $carId['id'] ? 'selected' : '' ?> value='<?= $carId['id'] ?>'><?= "{$carInfo['brand']} {$carInfo['model']} ({$carInfo['type']} {$carInfo['engine']} - {$carInfo['fuel']}) {$carInfo['year']} r. | {$carInfo['registration']} | ".rent_price($carId['id'], true)." zł" ?></option>

                  <?php endwhile; ?>
                </select>

                <span class='input--error'><?php $errField = 'car'; if (isset($_SESSION['dashboard-form-error-'.$errField])) { echo $_SESSION['dashboard-form-error-'.$errField]; unset($_SESSION['dashboard-form-error-'.$errField]); } ?></span>
              <?php endif;?>
            </div>

            <?php input('from', 'Od:', date('Y-m-d', $rent['begin']), '', 'date') ?>
            <?php input('to', 'Do:', date('Y-m-d', $rent['end']), '', 'date') ?>

            <input type='hidden' name='action' value='edit-rent' />
            <input type='hidden' name='id' value='<?= $car['id'] ?>' />
            <button type='submit'>Zmień</button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
