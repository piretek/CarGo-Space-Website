<?php

if (!defined('SECURE_BOOT')) exit();

?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CarGo Space</title>

  <link rel='stylesheet' type='text/css' href='assets/css/style.css' />

  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">

  <style>
  <?php
    $backgroundsPath = 'assets/images/backgrounds';
    $acceptedFormats = ['png', 'jpg', 'jpeg', 'jfif'];
    $backgrounds = [];

    if (file_exists($backgroundsPath)) {
      $images = array_values( array_diff( scandir($backgroundsPath), ['.', '..'] ) );

      foreach($images as $image) {
        if ( in_array( pathinfo("{$backgroundsPath}/{$image}", PATHINFO_EXTENSION ), $acceptedFormats ) ) {
          $backgrounds[] = "{$backgroundsPath}/{$image}";
        }
      }
    }

    foreach($backgrounds as $i => $background) {
      echo "#background-{$i} { background-image: url('{$background}'); }\n";
    }
  ?>
  </style>

  <script src='assets/js/background.js' type='text/javascript'></script>
</head>
<body>

<?php

$pages = [
  [
    "name" => "Strona główna",
    "url" => $config['site_url'].'/'
  ],
  [
    "name" => "O firmie",
    "url" => $config['site_url'].'/'
  ],
  [
    "name" => "Kontakt",
    "url" => $config['site_url'].'/contact.php'
  ],
  [
    "name" => "Kalkulator",
    "url" => $config['site_url'].'/'
  ],
  [
    "name" => "Panel zarządzania",
    "url" => $config['site_url'].'/auth.php'
  ]
];

?>
<div class='wrapper'>
<div class='header'>
  <div class="menu">
    <h1 class='name'>CarGo Space</h1>
    <ul class='nav'>
      <?php
        foreach($pages as $page){
        ?>
          <li>
            <a href='<?= $page['url'] ?>'><?= $page['name'] ?></a>
          </li>
        <?php
        }
      ?>
    </ul>
  </div>
  <?php if (basename($_SERVER['PHP_SELF']) == 'index.php') : ?>
  <div class="header-background">
    <button class='bg-bttn bg-left'>&lt;</button>
    <div class='backgrounds'>

      <?php foreach($backgrounds as $i => $background) : ?>
        <div class='background <?= $i == 0 ? 'showing' : '' ?>' id='background-<?= $i ?>'></div>
      <?php endforeach; ?>

    </div>
    <button class='bg-bttn bg-right'>&gt;</button>
  </div>
  <?php endif; ?>
</div>
