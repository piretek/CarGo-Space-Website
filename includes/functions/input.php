<?php

if (!defined('SECURE_BOOT')) exit;

function input( $id, $label, $value = '', $placeholder = '', $type = 'text', $errorPrefix = null) {
  global $_SESSION;

  if ($errorPrefix === null) $errorPrefix = pathinfo(__DIR__.$_SERVER['PHP_SELF'], PATHINFO_FILENAME); ?>

    <div class='input--container'>
      <label class='input--label' for="<?= $id ?>"><?= $label ?></label>
      <input class='input' id="<?= $id ?>" type="<?= $type ?>" name="<?= $id ?>" placeholder="<?= $placeholder ?>">
      <span class='input--error'><?php $errField = $id; if (isset($_SESSION[$errorPrefix.'-form-error-'.$errField])) { echo $_SESSION[$errorPrefix.'-form-error-'.$errField]; unset($_SESSION[$errorPrefix.'-form-error-'.$errField]); } ?></span>
    </div>

  <?php
}
