<?php

$auth = false;
require_once './includes/init.php';

if(isset($_POST['email'])){

  list($from_year, $from_month, $from_day) = explode("-",$_POST['from']);
  $from = mktime(0, 0, 0, $from_month, $from_day, $from_year);

  list($to_year, $to_month, $to_day) = explode("-",$_POST['to']);
  $to = mktime(23, 59, 59, $to_month, $to_day, $to_year);

  $cars = $db->query("SELECT * FROM rents WHERE (begin >= $from AND end <= $from) OR (begin >= $to AND end <= $to)");

  if($cars->num_rows != 0){
    $_SESSION['contact-form-error'] = "Pojazd jest wypożyczony w tym czasie!";
    header("Location: {$config['site_url']}/contact.php");
    exit;
  }

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
    <form action="contact.php" method="POST">
      <div class='input--container'>
        <label class='input--label' for="name">Imie:</label>
        <input class='input' id="name" type="text" name="name" placeholder="podaj imie">
      </div>
      <div class='input--container'>
        <label class='input--label' for='surname'>Nazwisko:</label>
        <input class='input' id='surname' type="text" name="surname" placeholder="podaj nazwisko">
      </div>
      <div class='input--container'>
        <label class='input--label' for="PESEL">PESEL:</label>
        <input class='input' id="pesel" type="text" name="pesel" placeholder="podaj numer PESEL">
      </div>
      <div class='input--container'>
        <label class='input--label' for='city'>Miejscowość:</label>
        <input class='input' id='city' type="text" name="city" placeholder="podaj miejscowość">
      </div>
      <div class='input--container'>
        <label class='input--label' for='street'>Ulica:</label>
        <input class='input' id='street' type="text" name="street" placeholder="podaj ulice">
      </div>
      <div class='input--container'>
        <label class='input--label' for='number'>Numer domu/bloku:</label>
        <input class='input' id='number' type="text" name="number" placeholder="podaj numer">
      </div>
      <div class='input--container'>
        <label class='input--label' for='phone'>Numer telefonu:</label>
        <input class='input' id='phone' type="text" name="phone" placeholder="podaj nr telefonu">
      </div>
      <div class='input--container'>
        <label class='input--label' for="email">Email:</label>
        <input class='input' id="email" type="text" name="email" placeholder="podaj email">
      </div>
      <div class='input--container'>
        <label class='input--label' for="from">Od:</label>
        <input class='input' id="from" type="date" name="from">
      </div>
      <div class='input--container'>
        <label class='input--label' for="to">Do:</label>
        <input class='input' id="to" type="date" name="to">
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
      </div>
      <div>
        <button type="submit">WYPOŻYCZ</button>
      </div>
    </form>
  </div>
</div>

<?php include './includes/footer.php'; ?>
