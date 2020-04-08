<?php

$pageName = 'Panel zarządzania';
$auth = true;
require_once './includes/init.php';

include './includes/header.php';
?>

<div class="columns col-center">
  <div class="column col-75">
    <div class="columns">
      <div class='column col-100'>
        <div class="columns">
          <div class="col-20">
            <ul class='d-menu'>
              <li id='rents' class='page-bttn d-menu_element'>Wypożyczenia</li>
              <li id='fleet' class='page-bttn d-menu_element'>Flota</li>
              <li id='users' class='page-bttn d-menu_element'>Użytkownicy</li>
              <li id='account' class='page-bttn d-menu_element'>Twoje konto</li>
            </ul>
          </div>
          <div class="col-80 dashboard--pages-container">
            <?php if (
              (isset($_GET['action']) && !empty($_GET['action']) && substr($_GET['action'], 0, 3) === 'add') ||
              (isset($_GET['action']) && !empty($_GET['action']) && substr($_GET['action'], 0, 3) !== 'add' && isset($_GET['id']) && !empty($_GET['id']))) : ?>

              <form method='POST'>
                <input type='hidden' name='action' value='<?= $_GET['action'] ?>' />
                <input type='hidden' name='id' value='<?= $_GET['id'] ?>' />

                <?php

                $knownAction = true;
                if ($_GET['action'] === 'add-fleet') :

                  include_once 'dashboard/forms/add-fleet.php';

                elseif ($_GET['action'] === 'edit-fleet') :

                  include_once 'dashboard/forms/edit-fleet.php';

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
