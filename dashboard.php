<?php

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
            <div class="columns">
              <div class="column col-50">
                <h3 class='page--title'></h3>
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
                <table>
                 <tbody>
                    <tr>
                      <th>Klient</th>
                      <th>Samochód</th>
                      <th>Czas</th>
                      <th>Akcje</th>
                    </tr>
                    <tr>
                      <td>Piotr Czarnecki</td>
                      <td>Audi A4 I LU 0123A</td>
                      <td>06.04.2020 - 08.04.2020</td>
                      <td>Zobacz | Edytuj | Usuń</td>
                    </tr>
                    <tr>
                      <td>Piotr Czarnecki</td>
                      <td>Audi A4 I LU 0123A</td>
                      <td>06.04.2020 - 08.04.2020</td>
                      <td>Zobacz | Edytuj | Usuń</td>
                    </tr>
                    <tr>
                      <td>Piotr Czarnecki</td>
                      <td>Audi A4 I LU 0123A</td>
                      <td>06.04.2020 - 08.04.2020</td>
                      <td>Zobacz | Edytuj | Usuń</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div id='page-fleet' class="page column col-100">
                <div class='columns'>
                  <div class="column col-100">
                    Dodaj pojazd
                  </div>
                  <div class="column col-100">
                    <table>
                     <tbody>
                        <tr>
                          <th>Samochód</th>
                          <th>Rejestracja</th>
                          <th>Wypożyczony</th>
                          <th>Akcje</th>
                        </tr>
                        <tr>
                          <td>Audi A4 I (B5)</td>
                          <td>LU 0123A</td>
                          <td>Tak (06.04.2020 - 08.04.2020)</td>
                          <td>Zobacz | Edytuj | Usuń</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div id='page-users' class="page column col-100">
                <table>
                  <tbody>
                    <tr>
                      <th>Email</th>
                      <th>Imię i nazwisko</th>
                      <th>Uprawnienia</th>
                      <th>Akcje</th>
                    </tr>
                    <tr>
                      <td></td>
                      <td>LU 0123A</td>
                      <td></td>
                      <td>Edytuj | Usuń</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div id='page-account' class="page column col-100"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src='assets/js/dashboard.js'></script>
<?php include './includes/footer.php'; ?>
