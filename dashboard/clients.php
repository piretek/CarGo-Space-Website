<?php

if(!defined('SECURE_BOOT')) exit;

?>
<div class='columns'>
  <div class='column col-100'>
    <div class='columns columns__no-spacing'>
      <div class="column col-100 mb-2">
        <a href='dashboard.php?action=add-client'><button>Dodaj nowego klienta</button></a>
      </div>
      <div class='column col-100'>
        <table>
          <tbody>
            <tr>
              <th>Nr</th>
              <th>Imię i nazwisko</th>
              <th>PESEL</th>
              <th>Nr telefonu</th>
              <th>Akcje</th>
            </tr>
            <?php

            $clientsQuery = "SELECT * FROM clients ORDER BY surname, name ASC";

            $perPage = 15;
            $allClients = $db->query($clientsQuery)->num_rows;

            $page = isset($_GET['page']) && !empty($_GET['page']) ? $_GET['page'] : 1;
            if ($allClients > $perPage) {
              $totalPages = ceil($allClients / $perPage);
              $offset = ($page - 1) * $perPage;
            }
            else {
              $page = $totalPages = 1;
              $offset = 0;
            }

            $nextPage = $totalPages == $page ? null : $page + 1;
            $prevPage = $page - 1 == 0 ? null : $page - 1;

            $limitQuery = $clientsQuery." LIMIT {$offset}, {$perPage}";

            $clients = $db->query($limitQuery);
            if ($clients->num_rows == 0) : ?>

              <tr>
                <td colspan='5'>Brak klientów.</td>
              </tr>

            <?php else : ?>

              <?php while ($client = $clients->fetch_assoc()) : ?>
                <tr>
                  <td><?= $client['id'] ?></td>
                  <td><?= $client['surname'].' '.$client['name'] ?></td>
                  <td><?= $client['pesel'] ?></td>
                  <td><?= $client['phone'] ?></td>
                  <td>
                    <a href="dashboard.php?view=client&id=<?= $client['id'] ?>">Zarządzaj</a> |
                    <form class='as-anchor' method='post'>
                      <input type='hidden' name='action' value='delete-client' />
                      <input type='hidden' name='id' value='<?= $client['id'] ?>' />
                      <button type='submit'>Usuń</button>
                    </form>
                  </td>
                </tr>

              <?php endwhile; ?>

            <?php endif; ?>
          </tbody>
        </table>
        <?php if ($totalPages > 1) : ?>
          <div class='pagination--container'>
            <?php if ($prevPage !== null) : ?>
              <a class='pagination' href='dashboard.php?view=clients&page=<?= $prevPage ?>'>Poprzednia</a>
            <?php endif; ?>

            <?php if ($prevPage !== null && $page != 1) : ?>
              <a class='pagination' href='dashboard.php?view=clients'>1</a>
              <span class='three-dots'>...</span>
            <?php endif; ?>

            <span class='pagination'><?= $page ?></span>

            <?php if ($totalPages !== $nextPage && $nextPage !== null) : ?>
              <span class='three-dots'>...</span>
              <a class='pagination' href='dashboard.php?view=clients&page=<?= $totalPages ?>'><?= $totalPages ?></a>
            <?php endif; ?>

            <?php if ($nextPage !== null) : ?>
              <a class='pagination' href='dashboard.php?view=clients&page=<?= $nextPage ?>'>Następna</a>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
