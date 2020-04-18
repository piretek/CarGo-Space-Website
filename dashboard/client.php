<?php

if(!defined('SECURE_BOOT')) exit;

$query = sprintf("SELECT * FROM clients WHERE id = '%s'", $db->real_escape_string($_GET['id']));
$clients = $db->query($query);
if ($clients->num_rows == 0) {
  header('Location: dashboard.php?view=clients');
  exit;
}
$client = $clients->fetch_assoc();

?>

<h2>Zarządzanie klientem</h2>

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
    <a href='dashboard.php?view=clients'><button>&lt; Powrót</button></a>
    <form class='as-anchor-button' method='post'>
      <input type='hidden' name='action' value='delete-client' />
      <input type='hidden' name='id' value='<?= $client['id'] ?>' />
      <button type='submit'>Usuń</button>
    </form>
  </div>

  <div class='column col-50'>
    <form method='post'>
      <input type='hidden' name='action' value='edit-client' />
      <input type='hidden' name='id' value='<?= $client['id'] ?>' />

      <div class="column col-100">
        <?php input('name', 'Imię:', $client['name'], 'np. Jan') ?>
      </div>
      <div class="column col-100">
        <?php input('surname', 'Nazwisko:', $client['surname'], 'np. Kowalski') ?>
      </div>
      <div class="column col-100">
        <?php input('pesel', 'PESEL:', $client['pesel'], 'Numer PESEL (11 cyfr)', 'text', null, [
          'minlength' => '11',
          'maxlength' => '11'
        ]) ?>
        <?php input('city', 'Miejscowość:', $client['city'], 'np. Warszawa') ?>
      </div>
      <div class="column col-100">
        <?php input('street', 'Ulica:', $client['street'], 'np. ul. Wiejska') ?>
      </div>
      <div class="column col-100">
        <?php input('number', 'Numer domu/bloku:', $client['number'], 'np. 3/1') ?>
      </div>
      <div class="column col-100">
        <?php input('phone', 'Numer telefonu:', $client['phone'], 'np. 555 555 555') ?>
      </div>
      <div class="column col-100">
        <?php input('email', 'Email:', $client['email'], 'np. jan.kowalski@example.com', 'email') ?>
      </div>
      <button type='submit'>Zatwierdź</button>
    </form>
  </div>
  <div class='column col-50 ml-2'>
    <h3><?= $client['surname'].' '.$client['name'] ?></h3>
    <ul>
      <li><strong>PESEL:</strong> <?= $client['pesel'] ?></li>
      <li><strong>E-mail:</strong> <a href='mailto: <?= $client['email'] ?>'><?= $client['email'] ?></a></li>
      <li><strong>Telefon:</strong> <a href='tel: <?= $client['phone'] ?>'><?= chunk_split($client['phone'], 3, ' ') ?></a></li>
    </ul>
    <?php
    $rents = $db->query(sprintf("SELECT * FROM rents WHERE client = '%s' AND (begin <= '%d' AND end >= '%d') AND (status = '3' OR status = '2')",
      $db->real_escape_string($_GET['id']),
      time(),
      time(),
    ));

    if ($rents->num_rows != 0) : $rent = $rents->fetch_assoc(); $car = carinfo($rent['car']); ?>

      <h3 class='mt-2'>Aktualne wypożyczenie</h3>
      <ul>
        <li><strong>Pojazd:</strong> <?= $car['brand'].' '.$car['model'].' | '.$car['registration'] ?></li>
        <li><strong>Okres:</strong> <?= date('d.m.Y', $rent['begin']).' - '.date('d.m.Y', $rent['end']) ?></li>
      </ul>
      <a href='dashboard.php?view=rent&id=<?= $rent['id'] ?>'>Zobacz szczegóły dot. wypożyczenia</a>

    <?php endif; ?>

    <h3 class='mt-2'>Ostatnie 15 wypożyczeń</h3>
    <table>
      <tbody>
        <tr>
          <th>Nr</th>
          <th>Status</th>
          <th>Okres wypożyczenia</th>
          <th>Akcje</th>
        </tr>

        <?php

        $rentQuery = sprintf("SELECT * FROM rents WHERE client = '%s' LIMIT 15",
          $db->real_escape_string($_GET['id'])
        );

        $rents = $db->query($rentQuery);
        if ($rents->num_rows == 0) : ?>

          <tr>
            <td colspan='4'>Brak wypożyczeń</td>
          </tr>

        <?php else : ?>

          <?php while ($rent = $rents->fetch_assoc()) : ?>
            <tr>
              <td><?= $rent['id'] ?></td>
              <td><?= $rentStatus[$rent['status']] ?></td>
              <td><?= date('d.m.Y', $rent['begin']).' - '.date('d.m.Y', $rent['end']) ?></td>
              <td>
                <a href="dashboard.php?view=rent&id=<?= $rent['id'] ?>">Zobacz</a>
              </td>
            </tr>

          <?php endwhile; ?>

        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
