<?php

$pageName = 'Logowanie';
$auth = false;
require_once './includes/init.php';

if (USER_AUTHORIZED) {
  header('Location: dashboard.php');
  exit;
}

include './includes/header.php';
?>

<div class='columns col-center'>
  <div class="column col-50">
    <div class='columns'>
      <div class='column col-100'>
        <h1>Logowanie do systemu</h1>
      </div>
      <div class='column col-50'>
        <h4>Zaloguj się</h4>

        <?php if (isset($_SESSION['login-form-error'])) : ?>
          <span class='error'>Błąd: <?= $_SESSION['login-form-error'] ?></span>
          <?php unset($_SESSION['login-form-error']); ?>
        <?php endif; ?>

        <form action="login.php" method="POST">
          <div class='input--container'>
            <label class='input--label' for="email">Email:</label>
            <input class='input' id="email" type="text" name="email" placeholder="podaj email">
          </div>
          <div class='input--container'>
            <label class='input--label' for='password'>Hasło:</label>
            <input class='input' id='password' type="password" name="password" placeholder="podaj hasło">
          </div>
          <div>
            <button type="submit">ZALOGUJ</button>
          </div>
        </form>
      </div>

      <?php if ($db->query("SELECT * FROM users")->num_rows == 0) : ?>
        <div class='column col-50'>
          <h4>Zarejestruj się</h4>

          <?php if (isset($_SESSION['register-form-error'])) : ?>
            <span class='error'>Błąd: <?= $_SESSION['register-form-error'] ?></span>
            <?php unset($_SESSION['register-form-error']); ?>
          <?php endif; ?>

          <form action="register.php" method="POST">
            <div class='input--container'>
              <label class='input--label' for="email">Email:</label>
              <input class='input' id="email" type="text" name="email" placeholder="podaj email">
              <span class='input--error'>
                <?php
                if (isset($_SESSION['register-form-error-email']))  {
                  echo $_SESSION['register-form-error-email'];
                  unset($_SESSION['register-form-error-email']);
                }
                ?>
              </span>
            </div>
            <div class='input--container'>
              <label class='input--label' for='password'>Hasło:</label>
              <input class='input' id='password' type="password" name="password" placeholder="podaj hasło">
              <span class='input--error'>
                <?php
                if (isset($_SESSION["register-form-error-password"])) {
                  echo $_SESSION["register-form-error-password"];
                  unset($_SESSION["register-form-error-password"]);
                }
                ?>
            </div>
            <div class='input--container'>
              <label class='input--label' for='repeat-password'>Powtórz hasło:</label>
              <input class='input' id='repeat-password' type="password" name="repeat-password" placeholder="podaj hasło">
              <span class='input--error'>
                <?php
                if (isset($_SESSION["register-form-error-repeat-password"])) {
                  echo $_SESSION["register-form-error-repeat-password"];
                  unset($_SESSION["register-form-error-repeat-password"]);
                }
                ?>
            </div>
            <div>
              <button type="submit">Zarejestruj</button>
            </div>
          </form>
        </div>
      <?php endif; ?>

      <?php if (isset($_SESSION['register-form-success'])) : ?>
        <span class='success'><?= $_SESSION['register-form-success'] ?></span>
        <?php unset($_SESSION['register-form-success']); ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include './includes/footer.php'; ?>
