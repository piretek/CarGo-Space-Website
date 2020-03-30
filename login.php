<?php

$auth = false;
require_once './includes/init.php';

include './includes/header.php';
?>

<div class='columns col-center'>
  <div class="column col-50">
    <div class='columns'>
      <div class='column col-100'>
        <h1>Logowanie do systemu</h1>
      </div>
      <div class='column col-50'>
        <form action="services/login.php" method="POST">
          <div class='input--container'>
            <label class='input--label' for="email">Email:</label>
            <input class='input' id="email" type="text" name="email" placeholder="podaj email">
          </div>
          <div>
          <div class='input--container'>
            <label class='input--label' for='password'>Hasło:</label>
            <input class='input' id='password' type="password" name="pass" placeholder="podaj hasło">
          <div>
          <div>
            <button type="submit">ZALOGUJ</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include './includes/footer.php'; ?>
