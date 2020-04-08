<?php

if(!defined('SECURE_BOOT')) exit;

?>

<table>
  <tbody>
    <tr>
      <th>Klient</th>
      <th>Samochód</th>
      <th>Okres wypożyczenia</th>
      <th>Akcje</th>
    </tr>
    <?php

    $rents = $db->query("SELECT rents.* , CONCAT(clients.name, ' ', clients.surname, ' (', clients.pesel, ')') AS client FROM rents INNER JOIN clients ON clients.id = rents.client");
    if ($rents->num_rows == 0) : ?>

      <tr>
        <td colspan='4'>Brak pojazdów.</td>
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
          <td>Zobacz | Edytuj | Usuń</td>
        </tr>

      <?php endwhile; ?>

    <?php endif; ?>
  </tbody>
</table>
