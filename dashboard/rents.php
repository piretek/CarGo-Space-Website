<?php

if(!defined('SECURE_BOOT')) exit;

?>
<div class='columns'>
  <div class='column col-100'>
    <div class='columns columns__no-spacing'>
      <div class="column col-100 bm-2">
        <a href='dashboard.php?action=add-rent'><button>Dodaj nowe wypożyczenie</button></a>
      </div>
      <div class='column col-100'>
        <table>
          <tbody>
            <tr>
              <th>Klient</th>
              <th>Samochód</th>
              <th>Okres wypożyczenia</th>
              <th>Status</th>
              <th>Akcje</th>
            </tr>
            <?php

            $rents = $db->query("SELECT rents.* , CONCAT(clients.name, ' ', clients.surname, ' (', clients.pesel, ')') AS client FROM rents INNER JOIN clients ON clients.id = rents.client");
            if ($rents->num_rows == 0) : ?>

              <tr>
                <td colspan='5'>Brak wypożyczeń.</td>
              </tr>

            <?php else : ?>

              <?php while ($rent = $rents->fetch_assoc()) :
                $carInfo = carinfo($rent['car']); ?>

                <tr>
                  <td><?= $rent['client'] ?></td>
                  <td><?= $carInfo['brand'].' '.$carInfo['model'].' | '.$carInfo['registration'] ?></td>
                  <td>
                    <?= date('d.m.Y', $rent['begin'])." - ".date('d.m.Y', $rent['end']) ?>
                  </td>
                  <td><?= $rentStatus[ $rent['status'] ] ?></td>
                  <td>
                    <a href="?view=rent&id=<?= $rent['id'] ?>">Zarządzaj</a>
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
