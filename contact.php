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

  if(!$ok){
    foreach($errors as $field => $error){
      $_SESSION["contact-error-$field"] = $error;
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

  if($from < $availableFrom){
    $ok = false;
    $errors["from"] = "Data nie może być wcześniejsza niż ".date("d.m.Y", $availableFrom)."!";
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

  $cars = $db->query("SELECT * FROM rents WHERE (begin >= $from AND end <= $from) OR (begin >= $to AND end <= $to)");
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
      $_SESSION["contact-error-$field"] = $error;
    }

    $_SESSION["contact-form-error"] = "Popraw błędy w formularzu!";
    header("Location: {$config['site_url']}/contact.php");
    exit;
  }
  else {

    $clients = $db->query("SELECT * FROM clients WHERE pesel = '{$_POST['pesel']}'");
    if($clients->num_rows == 0){
      $db->query("INSERT INTO clients VALUES (null,'{$_POST['name']}','{$_POST['surname']}','{$_POST['city']}','{$_POST['street']}','{$_POST['number']}','{$_POST['phone']}','{$_POST['email']}','{$_POST['pesel']}')");

      $client_id = $db->insert_id;
    }
    else{
      $client_id = $clients->fetch_assoc()['id'];
    }

    $mail_to = "{$_POST['name']} {$_POST['surname']} <{$_POST['email']}>";
    $mail_from = 'CarGo Space <no-reply@cargospace.com>';
    $subject = 'Samochód został wypożyczony';
    $message = 'Samochód został wypożyczony';
    $headers = [
      'MIME-Version' => '1.0',
      'Content-type' => 'text/html; charset=iso-8859-1',
      'To' => $mail_to,
      'From' => $mail_from,
      'Reply-To' => $mail_from,
      'X-Mailer' => 'PHP/' . phpversion()
    ];

    // $emailSent = mail(null, $subject, $message, $headers);
    $emailSent = false;

    $db->query("INSERT INTO rents VALUES (null,'{$client_id}','{$_POST['car']}','{$from}','{$to}', '".($emailSent ? '1' : '0')."')");

    $_SESSION['contact-form-success'] = 'Samochód został wypożyczony. Oczekuj na odpowiedź naszego pracownika. Dziękujemy za zainsteresowanie ofertą CarGo Space!';
    header("Location: {$config['site_url']}/contact.php");
    exit;
  }
}
include './includes/header.php';
?>

<div class='columns col-center'>
  <div class="column col-75">
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
      <div class="column col-50">
        <form action="contact.php" method="POST">
          <div class="columns">
            <div class="column col-50">
              <div class='input--container'>
                <label class='input--label' for="name">Imie:</label>
                <input class='input' id="name" type="text" name="name" placeholder="podaj imie">
                <span class='input--error'><?php $errField = 'name'; if (isset($_SESSION['contact-error-'.$errField])) { echo $_SESSION['contact-error-'.$errField]; unset($_SESSION['contact-error-'.$errField]); } ?></span>
              </div>
              <div class='input--container'>
                <label class='input--label' for='surname'>Nazwisko:</label>
                <input class='input' id='surname' type="text" name="surname" placeholder="podaj nazwisko">
                <span class='input--error'><?php $errField = 'surname'; if (isset($_SESSION['contact-error-'.$errField])) { echo $_SESSION['contact-error-'.$errField]; unset($_SESSION['contact-error-'.$errField]); } ?></span>
              </div>
              <div class='input--container'>
                <label class='input--label' for="PESEL">PESEL:</label>
                <input class='input' id="pesel" type="text" name="pesel" placeholder="podaj numer PESEL">
                <span class='input--error'><?php $errField = 'pesel'; if (isset($_SESSION['contact-error-'.$errField])) { echo $_SESSION['contact-error-'.$errField]; unset($_SESSION['contact-error-'.$errField]); } ?></span>
              </div>
              <div class='input--container'>
                <label class='input--label' for='city'>Miejscowość:</label>
                <input class='input' id='city' type="text" name="city" placeholder="podaj miejscowość">
                <span class='input--error'><?php $errField = 'city'; if (isset($_SESSION['contact-error-'.$errField])) { echo $_SESSION['contact-error-'.$errField]; unset($_SESSION['contact-error-'.$errField]); } ?></span>
              </div>
              <div class='input--container'>
                <label class='input--label' for='street'>Ulica:</label>
                <input class='input' id='street' type="text" name="street" placeholder="podaj ulice">
                <span class='input--error'><?php $errField = 'street'; if (isset($_SESSION['contact-error-'.$errField])) { echo $_SESSION['contact-error-'.$errField]; unset($_SESSION['contact-error-'.$errField]); } ?></span>
              </div>
              <div class='input--container'>
                <label class='input--label' for='number'>Numer domu/bloku:</label>
                <input class='input' id='number' type="text" name="number" placeholder="podaj numer">
                <span class='input--error'><?php $errField = 'number'; if (isset($_SESSION['contact-error-'.$errField])) { echo $_SESSION['contact-error-'.$errField]; unset($_SESSION['contact-error-'.$errField]); } ?></span>
              </div>
              <div class='input--container'>
                <label class='input--label' for='phone'>Numer telefonu:</label>
                <input class='input' id='phone' type="text" name="phone" placeholder="podaj nr telefonu">
                <span class='input--error'><?php $errField = 'phone'; if (isset($_SESSION['contact-error-'.$errField])) { echo $_SESSION['contact-error-'.$errField]; unset($_SESSION['contact-error-'.$errField]); } ?></span>
              </div>
              <div class='input--container'>
                <label class='input--label' for="email">Email:</label>
                <input class='input' id="email" type="text" name="email" placeholder="podaj email">
                <span class='input--error'><?php $errField = 'email'; if (isset($_SESSION['contact-error-'.$errField])) { echo $_SESSION['contact-error-'.$errField]; unset($_SESSION['contact-error-'.$errField]); } ?></span>
              </div>
            </div>
            <div class="column col-50">
              <div class='input--container'>
                <label class='input--label' for="from">Od:</label>
                <input class='input' id="from" type="date" name="from">
                <span class='input--error'><?php $errField = 'from'; if (isset($_SESSION['contact-error-'.$errField])) { echo $_SESSION['contact-error-'.$errField]; unset($_SESSION['contact-error-'.$errField]); } ?></span>
              </div>
              <div class='input--container'>
                <label class='input--label' for="to">Do:</label>
                <input class='input' id="to" type="date" name="to">
                <span class='input--error'><?php $errField = 'to'; if (isset($_SESSION['contact-error-'.$errField])) { echo $_SESSION['contact-error-'.$errField]; unset($_SESSION['contact-error-'.$errField]); } ?></span>
              </div>
              <div class='input--container'>
                <label class='input--label' for='car'>Samochód:</label>
                <?php

                  $cars = $db->query("SELECT cars.id, brands.name as brand, models.model as model, types.name as type, year, engine, clutch FROM (((cars INNER JOIN models ON cars.model = models.id) INNER JOIN types ON types.id = models.type) INNER JOIN brands ON brands.id = models.brand)");
                  if($cars->num_rows != 0){ ?>

                    <select class="input" id="car" name="car">
                      <?php while($car = $cars->fetch_assoc()){ ?>

                      <option value='<?= $car["id"] ?>'><?= $car["brand"]." ".$car["model"]." ".$car["type"]." ".$car["year"]." ".$car["engine"]." ".$car["clutch"]; ?></option>

                      <?php } ?>
                    </select>

                  <?php
                  }
                  else{
                    echo "Brak pojazdów w systemie";
                  }
                ?>
              <span class='input--error'><?php $errField = 'car'; if (isset($_SESSION['contact-error-'.$errField])) { echo $_SESSION['contact-error-'.$errField]; unset($_SESSION['contact-error-'.$errField]); } ?></span>
              </div>
            </div>
          </div>
          <div>
            <button type="submit">WYPOŻYCZ</button>
          </div>
        </form>
      </div>
      <div class='column col-50'>
        <h2>CarGo Space S.A.</h2>
        <h5>Wypożyczalnia aut osobowych</h5>
        <p>
          ul. Sezamkowa 20<br />
          20-000 Lublin
        </p>
        <p>
          Tel.: <a href='tel:555555555'>555 555 555</a><br />
          Email: contact@cargospace.com
        </p>
        <p>
          NIP: 5198865856<br />
          REGON: 893831005
        </p>
        <p>
         Numer rachunku bankowego:<br /> 10902910238549384336005157
        </p>
        </p>
      </div>
    </div>
  </div>
</div>

<?php include './includes/footer.php'; ?>
