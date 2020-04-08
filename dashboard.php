<?php

$pageName = 'Panel zarządzania';
$auth = true;
require_once './includes/init.php';

$actions = [
  // 'action' => 'file',

  // Fleet
  'add-fleet' => 'add-fleet',
  'edit-fleet' => 'edit-fleet',
  // Types
  'add-type' => 'add-type',
  'edit-type' => 'edit-type',
  'delete-type' => 'edit-type',
  // Brands
  'add-brand' => 'add-brand',
  'edit-brand' => 'edit-brand',
  'delete-brand' => 'edit-brand',
];

if (isset($_POST['action'])) {
  $isNotAdd = substr($_POST['action'], 0, 3) !== 'add';

  if ($isNotAdd && (!isset($_POST['id']) || empty($_POST['id']))) {
    echo 'Nieznana akcja.';
    exit;
  }
  else {
    if (array_key_exists($_POST['action'], $actions)) {
      require_once 'dashboard/forms/'.$actions[$_POST['action']].'.php';
    }
    else {
      echo 'Nieznana akcja.';
      exit;
    }
  }

}

include './includes/header.php';
?>

<div class="columns col-center">
  <div class="column col-75">
    <div class="columns">
      <div class='column col-100'>
        <div class="columns">
          <div class="column col-20">
            <ul class='d-menu'>
              <li id='rents' class='page-bttn d-menu_element'>Wypożyczenia</li>
              <li id='fleet' class='page-bttn d-menu_element'>Flota</li>
              <li id='users' class='page-bttn d-menu_element'>Użytkownicy</li>
              <li id='account' class='page-bttn d-menu_element'>Twoje konto</li>
            </ul>
          </div>
          <div class="column col-80 dashboard--pages-container">
            <?php if (
              (isset($_GET['action']) && !empty($_GET['action']) && substr($_GET['action'], 0, 3) === 'add') ||
              (isset($_GET['action']) && !empty($_GET['action']) && substr($_GET['action'], 0, 3) !== 'add' && isset($_GET['id']) && !empty($_GET['id']))) : ?>

              <form method='POST'>
                <input type='hidden' name='action' value='<?= $_GET['action'] ?>' />
                <?php if (isset($_GET['id'])) : ?>
                  <input type='hidden' name='id' value='<?= $_GET['id'] ?>' />
                <?php endif; ?>

                <?php

                $knownAction = true;
                if (array_key_exists($_GET['action'], $actions)) :

                  require_once 'dashboard/forms/'.$actions[$_GET['action']].'.php';

                else :

                  echo 'Nieznana akcja.';
                  $knownAction = false;

                endif;

                if ($knownAction) : ?>
                  <button type='submit'><?= substr($_GET['action'], 0, 3) !== 'add' ? 'Zaktualizuj' : 'Dodaj' ?></button>
                <?php endif; ?>

              </form>

            <?php else : ?>

              <div class="columns">
                <div class="column col-50">
                  <h2 class='page--title'></h2>

                  <?php if (isset($_SESSION['dashboard-form-error'])) : ?>
                    <span class='error'>Błąd: <?= $_SESSION['dashboard-form-error'] ?></span>
                    <?php unset($_SESSION['dashboard-form-error']); ?>
                  <?php endif; ?>

                  <?php if (isset($_SESSION['dashboard-form-success'])) : ?>
                    <span class='success'><?= $_SESSION['dashboard-form-success'] ?></span>
                    <?php unset($_SESSION['dashboard-form-success']); ?>
                  <?php endif; ?>
                </div>
                <div class="column col-50">
                  <p class='nm ta-right'>
                    Witaj, <?= $sessionUser['email'] ?>
                    <a class='logout-link' href='logout.php'>
                      <button>Wyloguj się</button>
                    </a>
                  </p>
                </div>
              </div>
              <div class="columns">
                <div id='page-rents' class="page column col-100">
                  <?php include_once 'dashboard/rents.php' ?>
                </div>
                <div id='page-fleet' class="page column col-100">
                  <?php include_once 'dashboard/fleet.php' ?>
                </div>
                <div id='page-users' class="page column col-100">
                  <?php include_once 'dashboard/users.php' ?>
                </div>
                <div id='page-account' class="page column col-100">
                  <?php include_once 'dashboard/user-account.php' ?>
                </div>
              </div>
            <?php endif;?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src='assets/js/dashboard.js'></script>
<?php include './includes/footer.php'; ?>
