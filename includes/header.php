<?php

if (!defined('SECURE_BOOT')) exit();

?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageName) && !empty($pageName) ? $pageName.' - ' : ''  ?>Wypożyczalnia samochodów</title>

  <link rel='stylesheet' type='text/css' href='assets/css/style.css' />

  <link rel="icon" type="image/png" href="<?= $config['site_url'] ?>/assets/images/logo-16.png">

  <meta name="theme-color" content="#D6D6C2">
  <link rel="icon" sizes="192x192" href="<?= $config['site_url'] ?>/assets/images/logo-192.png">

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

  <script>
    const site_url = '<?= $config['site_url'] ?>';
  </script>

  <script src='assets/js/background.js' type='text/javascript'></script>
</head>
<body <?= isset($_COOKIE['dark-mode']) && $_COOKIE['dark-mode'] == 1 ? 'class=\'dark-mode\'' : '' ?>>

<?php

$pages = [
  [
    "name" => "Strona główna",
    "url" => $config['site_url'].'/'
  ],
  [
    "name" => "O firmie",
    "url" => $config['site_url'].'/about.php'
  ],
  [
    "name" => "Kontakt",
    "url" => $config['site_url'].'/contact.php'
  ],
  [
    "name" => "Kalkulator",
    "url" => $config['site_url'].'/calculator.php'
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
    <img class="logo" src="<?= $config['site_url'] ?>/assets/images/logo-42.png" alt="CarGo Space">
    <h1 class='name'><span>CarGo Space</span></h1>
    <ul class='nav'>
      <?php
        foreach($pages as $page){
        ?>
          <li <?= $page['name'] == 'Panel zarządzania' ? 'class=\'dashboard-link\'' : '' ?>>
            <a href='<?= $page['url'] ?>'><?= $page['name'] ?></a>
          </li>
        <?php
        }
      ?>
    </ul>
  </div>

  <?php if ( $db->query("SELECT * FROM users")->num_rows == 0 ) : ?>
    <div class='no-user-consent'>
      <p>W systemie nie jest zarejestrowany żaden użytkownik. Aby w pełni wykorzystać potencjał systemu, proszę o założenie konta do zarządzania zasobami. <?= basename($_SERVER['PHP_SELF']) != 'auth.php' ? "Aby to zrobić, <a href='auth.php'>kliknij tutaj</a>." : '' ?></p>
    </div>
  <?php endif; ?>

  <?php if (basename($_SERVER['PHP_SELF']) == 'index.php') : ?>
  <div class="header-background">
    <button class='bg-bttn bg-left'>&lt;</button>
    <div class='backgrounds'>

      <?php foreach($backgrounds as $i => $background) : ?>
        <div class='background <?= $i == 0 ? 'showing' : '' ?>' id='background-<?= $i ?>'></div>
      <?php endforeach; ?>

    </div>
    <div class='text'>
      <h1>CarGo Space</h1>
      <p>Car no do that, car no fly...</p>
    </div>
    <button class='bg-bttn bg-right'>&gt;</button>
  </div>
  <?php endif; ?>
</div>
