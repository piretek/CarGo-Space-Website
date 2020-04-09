<?php

function rent_price( $rentId ) {
  global $db;

  $query = sprintf("SELECT rents.id, rents.begin, rents.end, IF(cars.price = 0.00, models.price, cars.price) as price FROM (rents INNER JOIN cars ON rents.car = cars.id) INNER JOIN models ON cars.model = models.id WHERE rents.id = '%s'",
    $db->real_escape_string($rentId)
  );

  $rents = $db->query($query);
  if ($rents->num_rows == 0) {
    return null;
  }
  else {
    $rent = $rents->fetch_assoc();
    $price = (float) (abs($rent['begin'] - $rent['end']) / 60 / 60 / 24 ) * (float) $rent['price'];

    return str_replace('.', ',', number_format($price, 2));
  }
}
