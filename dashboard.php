<?php

$pageName = 'Panel zarządzania';
$auth = true;
require_once './includes/init.php';

include './includes/header.php';
?>

<h1>Dashboard</h1>

<?php var_dump($_SESSION); ?>

<a href='logout.php'>
  <button>Wyloguj się</button>
</a>

<?php include './includes/footer.php'; ?>
