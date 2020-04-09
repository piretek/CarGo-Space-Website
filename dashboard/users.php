<?php

if(!defined('SECURE_BOOT')) exit;

?>


<table>
  <tbody>
    <tr>
      <th>Email</th>
      <th>Imię i nazwisko</th>
      <th>Uprawnienia</th>
      <th>Akcje</th>
    </tr>
    <?php

    $users = $db->query('SELECT * FROM users');
    if ($users->num_rows == 0) : ?>

      <tr>
        <td colspan='2'>Brak użytkowników.</td>
      </tr>

    <?php else : ?>

      <?php while ($user = $users->fetch_assoc()) : ?>

        <tr>
          <td><?= $user['email'] ?></td>
          <td><?= $user['firstname'].' '.$user['lastname'] ?></td>
          <td></td>
          <td>Edytuj | Usuń</td>
        </tr>

      <?php endwhile; ?>

    <?php endif; ?>
  </tbody>
</table>
