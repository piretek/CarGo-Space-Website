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
            <?php if (
              (isset($_GET['action']) && !empty($_GET['action']) && substr($_GET['action'], 0, 3) === 'add') ||
              (isset($_GET['action']) && !empty($_GET['action']) && substr($_GET['action'], 0, 3) !== 'add' && isset($_GET['id']) && !empty($_GET['id']))) : ?>

              <form method='POST'>
                <input type='hidden' name='action' value='<?= $_GET['action'] ?>' />
                <input type='hidden' name='id' value='<?= $_GET['id'] ?>' />

                <?php

                $knownAction = true;
                if ($_GET['action'] === 'add-fleet') : ?>

                  <h2>Dodaj nowy pojazd</h2>

                <?php elseif ($_GET['action'] === 'edit-fleet') : ?>


                <?php else :

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
                <table>
                  <tbody>
                    <tr>
                      <th>Klient</th>
                      <th>Samochód</th>
                      <th>Okres wypożyczenia</th>
                      <th>Akcje</th>
                    </tr>
                    <?php

                    $rents = $db->query("SELECT rents.* , CONCAT(clients.name, ' ', clients.surname, ' (', clients.pesel, ')') AS client FROM rents INNER JOIN clients ON clients.id = rents.client");
                    if ($rents->num_rows == 0) : ?>

                      <tr>
                        <td colspan='4'>Brak pojazdów.</td>
                      </tr>

                    <?php else : ?>

                      <?php while ($rent = $rents->fetch_assoc()) :
                        $carInfo = carinfo($rent['car']); ?>

                        <tr>
                          <td><?= $rent['client'] ?></td>
                          <td><?= $carInfo['brand'].' '.$carInfo['model'].' | '.$carInfo['registration'] ?></td>
                          <td>
                            <?= date('d.m.Y', $rent['begin'])." - ".date('d.m.Y', $rent['end']) ?>
                          </td>
                          <td>Zobacz | Edytuj | Usuń</td>
                        </tr>

                      <?php endwhile; ?>

                    <?php endif; ?>
                  </tbody>
                </table>
                </div>
                <div id='page-fleet' class="page column col-100">
                  <div class='columns'>
                    <div class="column col-100">
                      <a href='?action=add-fleet'>
                        <button>
                          Dodaj pojazd
                        </button>
                      </a>
                    </div>
                    <div class="column col-100 fleet--cars">
                      <table>
                      <tbody>
                          <tr>
                            <th>Samochód</th>
                            <th>Rocznik</th>
                            <th>Rejestracja</th>
                            <th>Wypożyczony</th>
                            <th>Akcje</th>
                          </tr>

                          <?php

                          $cars = $db->query('SELECT * FROM cars');
                          if ($cars->num_rows == 0) : ?>

                            <tr>
                              <td colspan='4'>Brak pojazdów.</td>
                            </tr>

                          <?php else : ?>

                            <?php while ($car = $cars->fetch_assoc()) :
                              $carInfo = carinfo($car['id']); ?>

                              <tr>
                                <td><?= $carInfo['brand'].' '.$carInfo['model'] ?></td>
                                <td><?= $carInfo['year'] ?></td>
                                <td><?= $carInfo['registration'] ?></td>
                                <td>
                                  <?php
                                    $rents = $db->query("SELECT * FROM rents WHERE id = '{$car['id']}' AND begin <= '".time()."' AND end >= '".time()."'");
                                    if ($rents->num_rows == 0) {
                                      echo "<span class='success'>Nie</span>";
                                    }
                                    else {
                                      $rent = $rents->fetch_assoc();
                                      echo "<span class='error'>Tak (".date('d.m.Y', $rent['begin'])." - ".date('d.m.Y', $rent['end']).")</span>";
                                    }
                                  ?>
                                </td>
                                <td>Zobacz | Edytuj | Usuń</td>
                              </tr>

                            <?php endwhile; ?>

                          <?php endif; ?>
                        </tbody>
                      </table>
                    </div>
                    <div class="column col-100">
                      <div class="columns">
                        <div class="column col-50">
                          <div class="columns">
                            <div class="column col-100">
                              <h3>
                                Modele
                                <a href='?action=add-model'>
                                  <button>
                                    Dodaj
                                  </button>
                                </a>
                              </h3>
                            </div>
                            <div class="column col-100">
                              <table>
                                <tbody>
                                  <tr>
                                    <th>Marka</th>
                                    <th>Model</th>
                                    <th>Okres produkcji</th>
                                    <th>Akcje</th>
                                  </tr>

                                  <?php

                                  $models = $db->query('SELECT models.*, brands.name as brand FROM models INNER JOIN brands ON brands.id = models.brand');
                                  if ($models->num_rows == 0) : ?>

                                    <tr>
                                      <td colspan='4'>Brak modeli.</td>
                                    </tr>

                                  <?php else : ?>

                                    <?php while ($model = $models->fetch_assoc()) : ?>

                                      <tr>
                                        <td><?= $model['brand'] ?></td>
                                        <td><?= $model['model'] ?></td>
                                        <td><?= $model['year_from']." - ".$model['year_to'] ?></td>
                                        <td>Edytuj | Usuń</td>
                                      </tr>

                                    <?php endwhile; ?>

                                  <?php endif; ?>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                        <div class="column col-25">
                          <div class="columns">
                            <div class="column col-100">
                              <h3>
                                Marki
                                <a href='?action=add-brand'>
                                  <button>
                                    Dodaj
                                  </button>
                                </a>
                              </h3>
                            </div>
                            <div class="column col-100">
                              <table>
                                <tbody>
                                  <tr>
                                    <th>Marka</th>
                                    <th>Akcje</th>
                                  </tr>

                                  <?php

                                  $brands = $db->query('SELECT * FROM brands');
                                  if ($brands->num_rows == 0) : ?>

                                    <tr>
                                      <td colspan='2'>Brak marek.</td>
                                    </tr>

                                  <?php else : ?>

                                    <?php while ($brand = $brands->fetch_assoc()) : ?>

                                      <tr>
                                        <td><?= $brand['name'] ?></td>
                                        <td>
                                          <span>
                                            Edytuj | Usuń
                                          </span>
                                        </td>
                                      </tr>

                                    <?php endwhile; ?>

                                  <?php endif; ?>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                        <div class="column col-25">
                          <div class="columns">
                            <div class="column col-100">
                              <h3>
                                Typy
                                <a href='?action=add-type'>
                                  <button>
                                    Dodaj
                                  </button>
                                </a>
                              </h3>
                            </div>
                            <div class="column col-100">
                              <table>
                                <tbody>
                                  <tr>
                                    <th>Typ</th>
                                    <th>Akcje</th>
                                  </tr>

                                  <?php

                                  $types = $db->query('SELECT * FROM types');
                                  if ($types->num_rows == 0) : ?>

                                    <tr>
                                      <td colspan='2'>Brak typów.</td>
                                    </tr>

                                  <?php else : ?>

                                    <?php while ($type = $types->fetch_assoc()) : ?>

                                      <tr>
                                        <td><?= $type['name'] ?></td>
                                        <td>Edytuj | Usuń</td>
                                      </tr>

                                    <?php endwhile; ?>

                                  <?php endif; ?>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
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
                </div>
                <div id='page-account' class="page column col-100">
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
