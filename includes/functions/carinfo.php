<?php

if (!defined('SECURE_BOOT')) exit;

function carinfo($carId) {
  global $db, $config;

  $query = sprintf("SELECT cars.id, brands.name as brand, models.model as model, types.name as type, year, engine, clutch, fuel, IF(cars.price = 0.00, models.price, cars.price) as price, image, registration FROM (((cars INNER JOIN models ON cars.model = models.id) INNER JOIN types ON types.id = models.type) INNER JOIN brands ON brands.id = models.brand) WHERE cars.id = '%s'",
    $db->real_escape_string($carId)
  );

  $cars = $db->query($query);
  if ($cars->num_rows == 0) {
    return null;
  }
  else {
    $car = $cars->fetch_assoc();
    if (!empty($car['image'])) $car['image'] = $config['site_url'].'/'.$config['car_photo_upload_dir'].'/'.$car['image'];

    return $car;
  }
}
