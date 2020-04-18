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

<div class="columns col-center min-page-height">
  <div class="column page-column">
    <h1>Jak to działa?</h1>
    <div class="instruction columns col-center columns__no-spacing">
      <ol class="how-to">
        <li>
          <span>Wybierz samochód z naszej floty </span>
          <br/>
          <span class="adnotation">Wskazanie samochodu przekieruje cię do formularza zgłoszeniowego.</span>
        </li>
        <li>
          <span>Wypełnij formularz zgłoszeniowy</span>
          <br/>
          <span class="adnotation">Podaj potrzebne dane, wybierz wariant wskazanego przez siebie modelu i wskaż okres wypożyczenia.
        </li>
        <li>
          <span>Ciesz się jazdą najlepszymi samochodami</span>
          <br/>
          <span class="adnotation">Po zaakceptowaniu twojego zgłoszenia otrzymasz maila z informacją, że możesz odebrać samochód w oddziale.</span>
        </li>
      </ol>
    </div>

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
