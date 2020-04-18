<?php

if(!defined('SECURE_BOOT')) exit;

$query = sprintf("SELECT * FROM cars WHERE id = '%s'", $db->real_escape_string($_GET['id']));
$cars = $db->query($query);
if ($cars->num_rows == 0) {
  echo '<p>Pojazd o takim ID nie istnieje</p>';
}
else {
  $car = $cars->fetch_assoc();
  $carInfo = carInfo($car['id']); ?>

  <div class="columns">
    <div class="column col-100 bm-2">
      <a href='dashboard.php?view=fleet'><button>&lt; Powrót</button></a>
      <a href="dashboard.php?action=edit-fleet&id=<?= $car['id'] ?>"><button>Edytuj</button></a>
      <form class='as-anchor-button' method='post'>
        <input type='hidden' name='action' value='delete-fleet' />
        <input type='hidden' name='id' value='<?= $car['id'] ?>' />
        <button type='submit'>Usuń</button>
      </form>
    </div>
    <div class="column col-1per3">
      <h2><?= "{$carInfo['brand']} {$carInfo['model']}" ?></h2>
      <ul>
        <li><strong>Rocznik:</strong> <?= $carInfo['year'] ?></li>
        <li><strong>Typ:</strong> <?= $carInfo['type'] ?></li>
        <li><strong>Silnik:</strong> <?= $carInfo['engine'] ?></li>
        <li><strong>Skrzynia biegów:</strong> <?= $carInfo['clutch'] ?></li>
        <li><strong>Rodzaj paliwa:</strong> <?= $carInfo['fuel'] ?></li>
        <li><strong>Rejestracja:</strong> <?= $carInfo['registration'] ?></li>
      </ul>
      <p><strong>Cena za 1 dobę:</strong> <?= str_replace('.',',', $carInfo['price']) ?> zł
    </div>
    <div class="column col-1per3 image-preview ip-center">
      <?php if (!empty($car['image'])) : ?>
        <img src='<?= $carInfo['image'] ?>' alt='Zdjęcie pojazdu' title='Zdjęcie poglądowe pojazdu' />
      <?php endif; ?>
    </div>
    <div class="column col-1per3">
      <h3 class='bm'>Status wypożyczenia</h3>
      <?php

      $rentQuery = sprintf("SELECT rents.*, CONCAT(clients.name, ' ', clients.surname) AS client, clients.phone FROM rents INNER JOIN clients ON rents.client = clients.id WHERE car = '%s' AND begin <= '%d' AND end >= '%d' AND (status = '3' OR status = '2')",
        $db->real_escape_string($_GET['id']),
        time(),
        time()
      );

      $rents = $db->query($rentQuery);
      if ($rents->num_rows == 0) : ?>

        <p><span class='success'>Wolny, dostępny do wypożyczenia</span></p>
        <a href='dashboard.php?action=add-rent&car=<?= $car['id'] ?>'>
          <button>Dodaj nowe wypożyczenie dla auta</button>
        </a>

      <?php else : $rent = $rents->fetch_assoc() ?>

      <p><span class='error'>Pojazd jest wypożyczony</span></p>
      <p><strong>Klient: </strong> <?= $rent['client'] ?></p>
      <p><strong>Telefon: </strong> <?= $rent['phone'] ?></p>
      <p><strong>Okres wypożyczenia: </strong><br /> <?= date('d.m.Y', $rent['begin']).' - '.date('d.m.Y', $rent['end']) ?></p>
      <p><strong>Cena: </strong> <?= rent_price($rent['id']) ?> zł</p>

      <?php endif; ?>
    </div>
    <div class='column col-100'>
      <hr />
      <div class="columns columns__no-spacing columns__bigger-v-spacing">
        <div class="column col-50">
          <h3>Ostatnie 15 wypożyczeń</h3>
          <table>
            <tbody>
              <tr>
                <th>Klient</th>
                <th>Status</th>
                <th>Okres wypożyczenia</th>
                <th>Akcje</th>
              </tr>

              <?php

              $rentQuery = sprintf("SELECT rents.*, CONCAT(clients.name, ' ', clients.surname) AS client FROM rents INNER JOIN clients ON rents.client = clients.id WHERE car = '%s' LIMIT 15",
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
                    <td><?= $rent['client'] ?></td>
                    <td><?= $rentStatus[$rent['status']] ?></td>
                    <td><?= date('d.m.Y', $rent['begin']).' - '.date('d.m.Y', $rent['end']) ?></td>
                    <td>
                      <a href="?view=rent&id=<?= $car['id'] ?>">Zobacz</a>
                    </td>
                  </tr>

                <?php endwhile; ?>

              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
<?php } ?>
