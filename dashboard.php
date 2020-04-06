<?php

$auth = true;
require_once './includes/init.php';

include './includes/header.php';
?>

<div class="columns col-center">
  <div class="column col-80">
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
            <div class="columns">
              <div class="column col-50">
                <h3 class='page--title'></h3>
              </div>
              <div class="column col-50">
                <p>
                  Witaj, <?= $sessionUser['email'] ?>
                  <a class='logout-link' href='logout.php'>
                    <button>Wyloguj się</button>
                  </a>
                </p>
              </div>
            </div>
            <div class="columns">
              <div id='page-rents' class="page column col-100"></div>
              <div id='page-fleet' class="page column col-100"></div>
              <div id='page-users' class="page column col-100"></div>
              <div id='page-account' class="page column col-100"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<?php include './includes/footer.php'; ?>
