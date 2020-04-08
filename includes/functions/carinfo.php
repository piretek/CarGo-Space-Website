<?php

if (!defined('SECURE_BOOT')) exit;

function carinfo($carId) {
  global $db;

  $cars = $db->query("SELECT cars.id, brands.name as brand, models.model as model, types.name as type, year, engine, clutch, registration FROM (((cars INNER JOIN models ON cars.model = models.id) INNER JOIN types ON types.id = models.type) INNER JOIN brands ON brands.id = models.brand) WHERE cars.id = '{$carId}'");
  if ($cars->num_rows == 0) {
    return null;
  }
  else {
    return $cars->fetch_assoc();
  }
}
