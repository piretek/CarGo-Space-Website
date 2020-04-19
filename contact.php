<?php

$pageName = 'Kontakt';
$auth = false;
require_once './includes/init.php';

if(isset($_POST['email'])){

  $ok = true;
  $errors = [];

  foreach($_POST as $key => $value){
    if(empty($value)){
      $ok = false;
      $errors[$key] = "Pole nie może być puste!";
    }
  }

  if (!isset($_POST['agreement-1'])) {
    $ok = false;
    $errors["agreement-1"] = "Wymagana zgoda.";
  }

  if (!isset($_POST['agreement-2'])) {
    $ok = false;
    $errors["agreement-2"] = "Wymagana zgoda.";
  }

  if(!$ok){
    foreach($errors as $field => $error){
      $_SESSION["contact-form-error-$field"] = $error;
    }

    $_SESSION["contact-form-error"] = "Popraw błędy w formularzu!";
    header("Location: {$config['site_url']}/contact.php");
    exit;
  }

  list($from_year, $from_month, $from_day) = explode("-",$_POST['from']);
  $from = mktime(0, 0, 0, $from_month, $from_day, $from_year);

  list($to_year, $to_month, $to_day) = explode("-",$_POST['to']);
  $to = mktime(23, 59, 59, $to_month, $to_day, $to_year);

  $availableFrom = strtotime("now +1 day midnight");
  $availableTo = strtotime("now +3 months");
  $availableToFrom = strtotime("now +2 day midnight");

  if($from < $availableFrom){
    $ok = false;
    $errors["from"] = "Data nie może być wcześniejsza niż ".date("d.m.Y", $availableFrom)."!";
  }

  if($to < $availableToFrom){
    $ok = false;
    $errors["to"] = "Data nie może być wcześniejsza niż ".date("d.m.Y", $availableToFrom)."!";
  }

  if($to > $availableTo){
    $ok = false;
    $errors["to"] = "Data nie może być późniejsza niż ".date("d.m.Y", $availableTo)."!";
  }

  if(strlen($_POST['pesel']) !== 11){
    $ok = false;
    $errors["pesel"] = "PESEL musi mieć 11 cyfr!";
  }

  if(strlen($_POST['phone']) !== 9){
    $ok = false;
    $errors["phone"] = "Numer telefonu musi mieć 9 cyfr!";
  }

  $rentedCarsQuery = sprintf("SELECT * FROM rents WHERE car = '%s' AND ((begin <= '%s' AND end >= '%s') OR (begin <= '%s' AND end >= '%s')) AND (status = '3' OR status = '2')",
    $db->real_escape_string($_POST['car']),
    $db->real_escape_string($from),
    $db->real_escape_string($from),
    $db->real_escape_string($to),
    $db->real_escape_string($to)
  );

  $cars = $db->query($rentedCarsQuery);

  if($cars->num_rows != 0){
    $ok = false;
    $errors['car'] = "Pojazd jest wypożyczony w tym czasie!";
  }

  if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    $ok = false;
    $errors["email"] = "Niepoprawny email";
  }

  if(!$ok){
    foreach($errors as $field => $error){
      $_SESSION["contact-form-error-$field"] = $error;
    }

    $_SESSION["contact-form-error"] = "Popraw błędy w formularzu!";
    header("Location: {$config['site_url']}/contact.php");
    exit;
  }
  else {

    $clients = $db->query(sprintf("SELECT * FROM clients WHERE pesel = '%s'", $db->real_escape_string($_POST['pesel'])));
    if($clients->num_rows == 0){

      $insertClientQuery = sprintf("INSERT INTO clients VALUES (null,'%s','%s','%s','%s','%s','%s','%s','%s')",
        $db->real_escape_string($_POST['name']),
        $db->real_escape_string($_POST['surname']),
        $db->real_escape_string($_POST['city']),
        $db->real_escape_string($_POST['street']),
        $db->real_escape_string($_POST['number']),
        $db->real_escape_string($_POST['phone']),
        $db->real_escape_string($_POST['email']),
        $db->real_escape_string($_POST['pesel'])
      );

      if (!$db->query($insertClientQuery)) {
        $_SESSION['contact-form-error'] = 'Błąd podczas rejestracji klienta. '.$db->error;
        header("Location: {$config['site_url']}/contact.php");
        exit;
      }

      $client_id = $db->insert_id;
    }
    else{
      $client_id = $clients->fetch_assoc()['id'];
    }

    $client = [
      'name' => $_POST['name'],
      'surname' => $_POST['surname'],
      'email' => $_POST['email']
    ];

    $rentedCar = carinfo($_POST['car']);

    $insertRentQuery = sprintf("INSERT INTO rents VALUES (null,'%s','%s','%s','%s', '%s', '0', '%d')",
      $db->real_escape_string($client_id),
      $db->real_escape_string($_POST['car']),
      $from,
      $to,
      $db->real_escape_string($_POST['insurance'] == '1' ? '1' : '0'),
      time()
    );

    $successful = $db->query($insertRentQuery);

    if ($successful) {
      $sent = send_mail($client, 'rent-created', [
        'rent-id' => $db->insert_id,
        'rent-car' => "{$rentedCar['brand']} {$rentedCar['model']} {$rentedCar['engine']} {$rentedCar['fuel']} {$rentedCar['registration']}",
        'rent-time' => date('d.m.Y', $from).' - '.date('d.m.Y', $to),
        'rent-price' => rent_price($db->insert_id).' zł'
      ]);

      if (!$sent) {
        $_SESSION['contact-form-error'] = 'Potwierdzenie nie zostało wysłane.';
      }

      $_SESSION['contact-form-success'] = 'Samochód został wypożyczony. Oczekuj na odpowiedź naszego pracownika. Dziękujemy za zainsteresowanie ofertą CarGo Space!';
      header("Location: {$config['site_url']}/contact.php");
      exit;
    }
    else {
      $_SESSION['contact-form-error'] = 'Błąd podczas wysyłania wiadomości. Skontaktuj się z administratorem.';
      header("Location: {$config['site_url']}/contact.php");
      exit;
    }
  }
}
include './includes/header.php';
?>

<div class='columns col-center min-page-height'>
  <div class="column page-column">
    <h1>Kontakt</h1>
    <?php if (isset($_SESSION['contact-form-error'])) : ?>
      <span class='error'>Błąd: <?= $_SESSION['contact-form-error'] ?></span>
      <?php unset($_SESSION['contact-form-error']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['contact-form-success'])) : ?>
      <span class='success'><?= $_SESSION['contact-form-success'] ?></span>
      <?php unset($_SESSION['contact-form-success']); ?>
    <?php endif; ?>
    <div class="columns">
      <div class="column col-40 contact-form">
        <form action="contact.php" method="POST">
          <div class="columns contact-form--container">
            <div class="column col-50">
              <?php input('name', 'Imię:', '', 'np. Jan') ?>
              <?php input('surname', 'Nazwisko:', '', 'np. Kowalski') ?>
              <?php input('pesel', 'PESEL:', '', 'Numer PESEL (11 cyfr)', 'text', null, [
                'minlength' => '11',
                'maxlength' => '11'
              ]) ?>
              <?php input('city', 'Miejscowość:', '', 'np. Warszawa') ?>
              <?php input('street', 'Ulica:', '', 'np. ul. Wiejska') ?>
              <?php input('number', 'Numer domu/bloku:', '', 'np. 3/1') ?>
              <?php input('phone', 'Numer telefonu:', '', 'np. 555 555 555') ?>
             </div>
            <div class="column col-50">
              <?php input('from', 'Od:', isset($_GET['from']) && !empty($_GET['from']) ? $_GET['from'] : '', '', 'date') ?>
              <?php input('to', 'Do:', isset($_GET['to']) && !empty($_GET['to']) ? $_GET['to'] : '', '', 'date') ?>
              <div class='input--container'>
                <label class='input--label' for='car'>Samochód:</label>
                <?php

                $carsQuery = "SELECT id FROM cars";
                $cars = $db->query($carsQuery);
                if ($cars->num_rows != 0) {
                  echo "<span>";

                  while($car = $cars->fetch_assoc()) : ?>
                    <span class='data-tag' data-car='<?= $car["id"] ?>' data-car-price='<?= rent_price($car["id"], true) ?>'></span>
                  <?php endwhile;

                  echo "</span>";
                }

                $cars = $db->query($carsQuery);
                if($cars->num_rows != 0){ ?>

                  <select class="input" id="car" name="car">
                    <?php $ix = 0; while($car = $cars->fetch_assoc()) { $carInfo = carinfo($car['id']); ?>

                    <option <?= (isset($_GET['car']) && $car['id'] == $_GET['car']) || $ix == 0 ? 'selected' : '' ?> value='<?= $car["id"] ?>'><?= $carInfo["brand"]." ".$carInfo["model"]." ".$carInfo["type"]." ".$carInfo["year"]." ".$carInfo["engine"]." ".$carInfo["clutch"]; ?></option>

                    <?php $ix++; } ?>
                  </select>

                <?php
                }
                else {
                  echo "Brak pojazdów w systemie";
                }

                ?>
                <span class='input--error'><?php $errField = 'car'; if (isset($_SESSION['contact-form-error-'.$errField])) { echo $_SESSION['contact-form-error-'.$errField]; unset($_SESSION['contact-form-error-'.$errField]); } ?></span>
              </div>

              <p>Dodatkowe ubezpieczenie:</p>
              <?php input('insurance-yes', 'Tak (+ 39,90 zł/każdy dzień)', '1', 'insurance', 'radio', null, (isset($_GET['insurance']) && $_GET['insurance'] == '1') || !isset($_GET['insurance']) ? ['checked' => 'checked'] : []) ?>
              <?php input('insurance-no', 'Nie', '0', 'insurance', 'radio', null, isset($_GET['insurance']) && $_GET['insurance'] == '0' ? ['checked' => 'checked'] : []) ?>
              <hr />
              <?php input('cats', 'Ile kotków dołączyć do auta?', '1', '', 'number', null, ['disabled' => 'disabled']) ?>
              <p class='no-cats'>Przepraszamy, ale skończyły nam się kotki. Jednakże nadal mogą Państwo wynająć auto.</p>
              <hr />
              <p class='contact-price-calculation'>
                <strong>Koszt wynajmu:</strong> <span></span>
              </p>
              <?php input('agreement-1', 'Wyrażam zgodę na przetwarzanie moich danych osobowych zgodnie z RODO przez firmę CarGo Space Polska S.A (wymagane)', 'i-agree', 'agreement-1', 'checkbox', null, ['required' => 'required']) ?>
              <?php input('agreement-2', 'Wyrażam zgodę na na przesyłanie informacji dot. wypożyczenia drogą elektroniczną przez CarGo Space Polska S.A (wymagane)', 'i-agree', 'agreement-2', 'checkbox', null, ['required' => 'required']) ?>
            </div>
          </div>
          <div>
            <button type="submit" class='beautiful'>Wypożycz</button>
          </div>
        </form>
      </div>
      <div class="column col-35 cities--container">
        <h2>
          Gdzie działamy?
        </h2>
        <div class="column col-center">
          <div class="cities">
            <div class="city">
              <img src="assets/images/cities/lublin.png" alt="Lublin">
              <p>
                CarGo Space Lublin<br/>
                ul. Sezamkowa 20 (2. piętro, pokój nr 14)<br />
                20-000 Lublin<br/>
                E-mail: contact.lublin@cargospace.com
              </p>
            </div>
            <div class="city">
              <img src="assets/images/cities/warsaw.png" alt="Warszawa">
              <p>
                CarGo Space Warszawa<br/>
                ul. Wiejska 3 (parter, pokój nr 12)<br/>
                00-003 Warszawa<br/>
                E-mail: contact.waraw@cargospace.com
              </p>
            </div>
            <div class="city">
              <img src="assets/images/cities/cracow.png" alt="Kraków">
              <p>
                CarGo Space Kraków<br/>
                ul. Smocza 1<br/>
                30-002 Kraków<br/>
                E-mail: contact.cracow@cargospace.com
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class='contact-align column col-25'>
        <img class="contact-logo" src="<?= $config['site_url'] ?>/assets/images/logo.png" alt="CarGo Space">
        <h2>CarGo Space Polska S.A.</h2>
        <h5>Wypożyczalnia aut osobowych</h5>
        <p>
          Tel.: <a href='tel:555555555'>555 555 555</a><br />
          Email: contact@cargospace.com
        </p>
        <p>
          NIP: 5198865856<br />
          REGON: 893831005
        </p>
        <p>
         Numer rachunku bankowego:<br /> 10 9029 1023 8549 3843 3600 5157
        </p>
      </div>
    </div>
  </div>
</div>

<?php include './includes/footer.php'; ?>
