<?php

$pageName = 'Panel zarządzania';
$auth = true;
require_once './includes/init.php';

$actions = [
  // Array structure:
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
  // Models
  'add-model' => 'add-model',
  'edit-model' => 'edit-model',
  'delete-model' => 'edit-model',
  // User account
  'user-selfedit' => 'user-account',
  // Rents
  'add-rent' => 'add-rent',
  'edit-rent' => 'edit-rent',
  'edit-rent-status' => 'edit-rent',
  // Clients
  'add-client' => 'add-client',
  'edit-client' => 'edit-client',
  'delete-client' => 'edit-client',
];

if (isset($_POST['action'])) {
  $isNotAdd = substr($_POST['action'], 0, 3) !== 'add' && $_POST['action'] !== 'user-selfedit';

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

<div class="columns col-center min-page-height dashboard-view">
  <div class="column page-column">
    <div class="columns">
      <div class='column col-100'>
        <div class="columns">
          <div class="column col-20">
            <ul class='d-menu'>
              <li id='rents' class='page-bttn d-menu_element'>Wypożyczenia</li>
              <li id='clients' class='page-bttn d-menu_element'>Klienci</li>
              <li id='fleet' class='page-bttn d-menu_element'>Flota</li>
              <li id='account' class='page-bttn d-menu_element'>Twoje konto</li>
            </ul>
          </div>
          <div class="column col-80 dashboard--pages-container">
            <?php

            $actionsWhichUploadFile = [
              'add-fleet',
              'edit-fleet'
            ];

            if (
              (isset($_GET['action']) && !empty($_GET['action']) && substr($_GET['action'], 0, 3) === 'add') ||
              (isset($_GET['action']) && !empty($_GET['action']) && substr($_GET['action'], 0, 3) !== 'add' && isset($_GET['id']) && !empty($_GET['id']))) : ?>

              <form method='POST' <?= in_array($_GET['action'], $actionsWhichUploadFile) ? 'enctype="multipart/form-data"' : '' ?>>
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

            <?php elseif (isset($_GET['view']) && $_GET['view'] == 'car' && isset($_GET['id']) && !empty($_GET['id'])) : ?>

              <?php include_once "dashboard/car.php" ?>

            <?php elseif (isset($_GET['view']) && $_GET['view'] == 'client' && isset($_GET['id']) && !empty($_GET['id'])) : ?>

              <?php include_once "dashboard/client.php" ?>

            <?php elseif (isset($_GET['view']) && $_GET['view'] == 'rent' && isset($_GET['id']) && !empty($_GET['id'])) : ?>

              <?php include_once "dashboard/rent.php" ?>

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
                    Witaj, <?= !empty($sessionUser['firstname']) && !empty($sessionUser['lastname']) ? "{$sessionUser['firstname']} {$sessionUser['lastname']}" : $sessionUser['email'] ?>!
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
                <div id='page-clients' class="page column col-100">
                  <?php include_once 'dashboard/clients.php' ?>
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

<div class='dashboard-view-mobile min-page-height'>Niestety, ale system zarządzania nie obsługuje urządzeń mobilnych</div>

<script src='assets/js/dashboard.js'></script>
<?php include './includes/footer.php'; ?>
