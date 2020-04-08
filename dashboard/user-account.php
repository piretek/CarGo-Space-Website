<?php

if(!defined('SECURE_BOOT')) exit;

?>

<form method='POST'>
  <input type='hidden' name='action' value='user-selfedit' />
  <div class='columns'>
    <div class="column col-100">
      <h4>Zmiana imienia i nazwiska:</h4>
      <div class='input--container'>
        <label class='input--label' for='firstname'>Imię:</label>
        <input class='input' id='firstname' type="text" name="firstname" value='<?= $sessionUser['firstname'] ?>'>
      </div>
      <div class='input--container'>
        <label class='input--label' for='lastname'>Nazwisko:</label>
        <input class='input' id='lastname' type="text" name="lastname" value='<?= $sessionUser['lastname'] ?>'>
      </div>
    </div>
    <div class="column col-50">
      <h4>Zmiana e-email:</h4>
      <div class='input--container'>
        <label class='input--label' for='email'>Nowy e-mail:</label>
        <input class='input' id='email' type="text" name="email">
      </div>
      <p>Aktualny email: <?= $sessionUser['email'] ?></p>
    </div>
    <div class="column col-50">
      <h4>Zmiana hasła:</h4>
      <div class='input--container'>
        <label class='input--label' for='password'>Nowe hasło:</label>
        <input class='input' id='password' type="password" name="password">
      </div><div class='input--container'>
        <label class='input--label' for='repeatedPassword'>Powtórz nowe hasło:</label>
        <input class='input' id='repeatedPassword' type="password" name="repeatedPassword">
      </div>
    </div>
    <div class="column col-50">
      <div class='input--container'>
        <label class='input--label' for='oldPassword'>Aktualne hasło:</label>
        <input class='input' id='oldPassword' type="password" name="oldPassword">
      </div>
    </div>
  </div>
  <button type='submit'>Zaktualizuj</button>
</form>
