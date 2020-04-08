<?php

$auth = false;
require_once './includes/init.php';
require_once './includes/functions/carinfo.php';

include './includes/header.php';
?>

<div class="columns col-center">
  <div class="column col-75">
    <div class="fleet">
      <h1>Nasza flota</h1>

      <div class='fleet-container'>
        <?php

        $cars = $db->query("SELECT * FROM cars");
        if ($cars->num_rows == 0){
          echo "Brak pojazdów w naszym systemie.";
        }
        else{
          while($car = $cars->fetch_assoc()) {
            $carInfo = carinfo($car['id']); ?>

              <a href="<?= $config['site_url'].'/contact.php?car='.$car['id']?>" class="car">
                <div class="image-car" style="  background: url('assets/images/cars/<?= $carInfo['image']?>') center / cover;">
                  <div class="title-car">        
                    <h2 class="title-text"><?= "{$carInfo['brand']} {$carInfo['model']}" ?></h2>
                  </div>    
                </div>
                <div class="car-supporting-text">
                  <ul class="car-info">
                    <li><strong>Typ:</strong> <?= $carInfo['type'] ?></li>
                    <li><strong>Rok produkcji:</strong> <?= $carInfo['year'] ?></li>
                    <li><strong>Pojemność silnika:</strong> <?= $carInfo['engine'] ?></li>
                    <li><strong>Rodzaj paliwa:</strong> <?= $carInfo['fuel'] ?></li>
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
