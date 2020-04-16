<?php

if(!defined('SECURE_BOOT')) exit;

?>

<form method='POST'>
  <input type='hidden' name='action' value='user-selfedit' />
  <div class='columns'>
    <div class="column col-100">
      <h4>Zmiana imienia i nazwiska:</h4>
      <?php input('firstname', 'Imię:', $sessionUser['firstname'] ) ?>
      <?php input('lastname', 'Nazwisko:', $sessionUser['lastname']) ?>
    </div>
    <div class="column col-50">
      <h4>Zmiana e-email:</h4>
      <?php input('email', 'Nowy e-mail: ') ?>
      <p>Aktualny email: <?= $sessionUser['email'] ?></p>
    </div>
    <div class="column col-50">
      <h4>Zmiana hasła:</h4>
      <?php input('password', 'Nowe hasło:', '', '', 'password') ?>
      <?php input('repeatedPassword', 'Powtórz nowe hasło:', '', '', 'password') ?>
    </div>
    <div class="column col-50">
      <?php input('oldPassword', 'Aktualne hasło:', '', '', 'password') ?>
    </div>
  </div>
  <button type='submit'>Zaktualizuj</button>
</form>
