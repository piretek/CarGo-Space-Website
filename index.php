<?php

$pageName = 'Strona główna';
$auth = false;
require_once './includes/init.php';

include './includes/header.php';
?>

<style>
  <?php

  $cars = $db->query("SELECT id FROM cars");
  if ($cars->num_rows !== 0){
    while($car = $cars->fetch_assoc()) {
      $carInfo = carinfo($car['id']); ?>

      .image-car#image-car-<?= $car['id'] ?> {
        background-image: url('<?= $carInfo['image'] ?>');
      }

    <?php }
  }
  ?>
</style>

<div class="columns col-center">
  <div class="column col-75">
    <div class="fleet">
      <h1>Nasza flota</h1>

      <div class='fleet-container'>
        <?php

        $cars = $db->query("SELECT id FROM cars");
        if ($cars->num_rows == 0) {
          echo "Brak pojazdów w naszym systemie.";
        }
        else {
          while($car = $cars->fetch_assoc()) {
            $carInfo = carinfo($car['id']); ?>

              <a href="<?= $config['site_url'].'/contact.php?car='.$car['id']?>" class="car">
                <div class="image-car" id='image-car-<?= $car['id'] ?>'>
                  <div class="title-car">
                    <h2 class="title-text"><?= "{$carInfo['brand']} {$carInfo['model']}" ?></h2>
                  </div>
                </div>
                <div class="car-supporting-text">
                  <ul class="car-info">
                    <li><strong>Typ:</strong> <?= $carInfo['type'] ?></li>
                    <li><strong>Rok produkcji:</strong> <?= $carInfo['year'] ?></li>
                    <li><strong>Silnik:</strong> <?= $carInfo['engine'] ?></li>
                    <li><strong>Skrzynia biegów:</strong> <?= $carInfo['clutch'] ?></li>
                    <li><strong>Numer rejestracyjny:</strong> <?= $carInfo['registration'] ?></li>
                  </ul>
                </div>
              </a>
          <?php }
        }
      ?>
      </div>
    </div>
  </div>
</div>

<?php include './includes/footer.php'; ?>
