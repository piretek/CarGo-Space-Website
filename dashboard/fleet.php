<?php

if(!defined('SECURE_BOOT')) exit;

?>

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
              <td>Zobacz |
                <a href="?action=edit-fleet&id=<?= $car['id'] ?>">Edytuj</a> |
                <form class='as-anchor' method='post'>
                  <input type='hidden' name='action' value='delete-fleet' />
                  <input type='hidden' name='id' value='<?= $car['id'] ?>' />
                  <button type='submit'>Usuń</button>
                </form>
              </td>
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
                      <td>
                        <a href="?action=edit-model&id=<?= $model['id'] ?>">Edytuj</a> |
                        <form class='as-anchor' method='post'>
                          <input type='hidden' name='action' value='delete-model' />
                          <input type='hidden' name='id' value='<?= $model['id'] ?>' />
                          <button type='submit'>Usuń</button>
                        </form>
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
                        <a href="?action=edit-brand&id=<?= $brand['id'] ?>">Edytuj</a> |
                        <form class='as-anchor' method='post'>
                          <input type='hidden' name='action' value='delete-brand' />
                          <input type='hidden' name='id' value='<?= $brand['id'] ?>' />
                          <button type='submit'>Usuń</button>
                        </form>
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
                      <td>
                        <a href="?action=edit-type&id=<?= $type['id'] ?>">Edytuj</a> |
                        <form class='as-anchor' method='post'>
                          <input type='hidden' name='action' value='delete-type' />
                          <input type='hidden' name='id' value='<?= $type['id'] ?>' />
                          <button type='submit'>Usuń</button>
                        </form>
                      </td>
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
