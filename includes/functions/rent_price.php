<?php

function rent_price( $id, $isCar = false, $from = null, $to = null ) {
  global $db;

  if (!$isCar) {
    $query = sprintf("SELECT rents.id, rents.begin, rents.end, IF(cars.price = 0.00, models.price, cars.price) as price FROM (rents INNER JOIN cars ON rents.car = cars.id) INNER JOIN models ON cars.model = models.id WHERE rents.id = '%s'",
      $db->real_escape_string($id)
    );
  }
  else {
    $query = sprintf("SELECT cars.id, IF(cars.price = 0.00, models.price, cars.price) as price FROM cars INNER JOIN models ON cars.model = models.id WHERE cars.id = '%s'",
      $db->real_escape_string($id)
    );
  }

  $rents = $db->query($query);
  if ($rents->num_rows == 0) {
    return null;
  }
  else {
    $rent = $rents->fetch_assoc();

    if (array_key_exists('begin', $rent) && array_key_exists('end', $rent)) {
      $price = (float) (round(abs($rent['end'] - $rent['begin']) / 60 / 60 / 24, 2) - 1) * (float) $rent['price'];
    }
    else if ($from !== null && $to !== null) {
      list($from_year, $from_month, $from_day) = explode("-", $from);
      $from = mktime(0, 0, 0, $from_month, $from_day, $from_year);

      list($to_year, $to_month, $to_day) = explode("-", $to);
      $to = mktime(23, 59, 59, $to_month, $to_day, $to_year);
      $price = (float) (abs($from - $to) / 60 / 60 / 24 ) * (float) $rent['price'];
    }
    else {
      $price = (float) $rent['price'];
    }

    return number_format($price, 2);
  }
}
