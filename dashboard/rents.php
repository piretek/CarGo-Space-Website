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
              <th>Nr</th>
              <th>Klient</th>
              <th>Samochód</th>
              <th>Okres wypożyczenia</th>
              <th>Status</th>
              <th>Akcje</th>
            </tr>
            <?php

            $rentsQuery = "SELECT rents.* , CONCAT(clients.name, ' ', clients.surname, ' (', clients.pesel, ')') AS client FROM rents INNER JOIN clients ON clients.id = rents.client ORDER BY created_at DESC";

            $perPage = 15;
            $allRents = $db->query($rentsQuery)->num_rows;

            $page = isset($_GET['page']) && !empty($_GET['page']) ? $_GET['page'] : 1;
            if ($allRents > $perPage) {
              $totalPages = ceil($allRents / $perPage);
              $offset = ($page - 1) * $perPage;
            }
            else {
              $page = $totalPages = 1;
              $offset = 0;
            }

            $nextPage = $totalPages == $page ? null : $page + 1;
            $prevPage = $page - 1 == 0 ? null : $page - 1;

            $limitQuery = $rentsQuery." LIMIT {$offset}, {$perPage}";

            $rents = $db->query($limitQuery);
            if ($rents->num_rows == 0) : ?>

              <tr>
                <td colspan='5'>Brak wypożyczeń.</td>
              </tr>

            <?php else : ?>

              <?php while ($rent = $rents->fetch_assoc()) :
                $carInfo = carinfo($rent['car']); ?>

                <tr>
                  <td><?= $rent['id'] ?></td>
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
        <?php if ($totalPages > 1) : ?>
          <div class='pagination--container'>
            <?php if ($prevPage !== null) : ?>
              <a class='pagination' href='dashboard.php?view=rents&page=<?= $prevPage ?>'>Poprzednia</a>
            <?php endif; ?>

            <?php if ($prevPage !== null && $page != 1) : ?>
              <a class='pagination' href='dashboard.php?view=rents'>1</a>
              <span class='three-dots'>...</span>
            <?php endif; ?>

            <span class='pagination'><?= $page ?></span>

            <?php if ($totalPages !== $nextPage && $nextPage !== null) : ?>
              <span class='three-dots'>...</span>
              <a class='pagination' href='dashboard.php?view=rents&page=<?= $totalPages ?>'><?= $totalPages ?></a>
            <?php endif; ?>

            <?php if ($nextPage !== null) : ?>
              <a class='pagination' href='dashboard.php?view=rents&page=<?= $nextPage ?>'>Następna</a>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
