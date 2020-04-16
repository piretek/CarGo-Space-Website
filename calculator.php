<?php

$pageName = 'Kalkulator wynajmu';
$auth = false;
require_once './includes/init.php';

include './includes/header.php';
?>
<div class="columns col-center">
  <div class="column page-column">
    <h1>Kalkulator wynajmu</h1>
    <p>Poniżej prosmy o wybranie pojazdu oraz ilość dni, na jaką chcą go Państwo wypożyczyć.</p>
    <div class='columns calculator'>
      <div class='column col-50'>
        <h3>Wybierz pojazd</h3><?php
        $cars = $db->query('SELECT id FROM cars');
        if ($cars->num_rows != 0) : ?>
          <div class="columns car-selector-container">
            <?php while($car = $cars->fetch_assoc()) :
              $carInfo = carinfo($car['id']); ?>
              <div class="car-selector column col-50 <?= !empty($carInfo['image']) ? 'has-image': '' ?>">
                <input type='hidden' name='car' value='<?= $car['id'] ?>' />
                <input type='hidden' name='price' value='<?= str_replace(',', '.',rent_price($car['id'], true)) ?>' />
                <?php if (!empty($carInfo['image'])) : ?>
                  <div class="car-image">
                    <img src='<?= $carInfo['image'] ?>' alt='<?= $carInfo['brand'].' '.$carInfo['model'] ?>' />
                  </div>
                <?php endif; ?>
                <div class='car-content'>
                  <p>
                    <span class='car-brand'><?= $carInfo['brand'] ?></span> <span class='car-model'><?= $carInfo['model'] ?></span><br />
                    <span class='car-type'><?= $carInfo['type']?></span> <span class='car-engine'><?= $carInfo['engine'] ?></span> | <span class='car-fuel'><?= $carInfo['fuel'] ?></span><br />
                    <span class='car-clutch'><?= 'Skrzynia '.$carInfo['clutch'] ?></span>
                  </p>
                </div>
              </div>
            <?php endwhile; ?>
          </div>

        <?php endif; ?>
      </div>
      <div class='column col-25'>
        <h3>Wybrany pojazd</h3>
        <div class='selected-car'>
          <p class='instruction'>Wybierz pojazd</p>
        </div>
        <hr />
        <h3>Podaj niezbędne informacje</h3>
        <?php input('begin', 'Od:', date('Y-m-d', strtotime('tomorrow')), '', 'date') ?>
        <?php input('end', 'Do:', '', '', 'date') ?>
      </div>
      <div class='column col-25'>
        <h3>Koszty wynajmu</h3>
        <p>Wybierz pojazd i wypełnij formularz z informacjami</p>
      </div>
    </div>
  </div>
</div>

<script src='assets/js/calculator.js'></script>
<?php include './includes/footer.php'; ?>