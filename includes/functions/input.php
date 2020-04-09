<?php

if (!defined('SECURE_BOOT')) exit;

function input( $id, $label, $value = '', $placeholder = '', $type = 'text', $errorPrefix = null, $addtiotionalAttributes = []) {
  global $_SESSION;

  $defaultAttributes = [
    'id' => $id,
    'type' => $type,
    'name' => $type == 'checkbox' ? $placeholder.'[]' : $type == 'radio' ? $placeholder : $id,
    'placeholder' => $placeholder,
    'value' => $value,
  ];

  $attributes = array_merge($defaultAttributes, $addtiotionalAttributes);

  $attributesText = '';
  foreach($attributes as $attribute => $value) {
    $attributesText .= "{$attribute}='{$value}' ";
  }

  if ($errorPrefix === null) $errorPrefix = pathinfo(__DIR__.$_SERVER['PHP_SELF'], PATHINFO_FILENAME);

  if ($type == 'checkbox' || $type == 'radio') : ?>
    <div class='input--container rc'>
      <input class='input' <?= $attributesText ?>>
      <label class='input--label' for="<?= $id ?>"><?= $label ?></label>
    </div>
    <span class='input--error'><?php $errField = $id; if (isset($_SESSION[$errorPrefix.'-form-error-'.$errField])) { echo $_SESSION[$errorPrefix.'-form-error-'.$errField]; unset($_SESSION[$errorPrefix.'-form-error-'.$errField]); } ?></span>
  <?php else : ?>
    <div class='input--container'>
      <label class='input--label' for="<?= $id ?>"><?= $label ?></label>
      <input class='input' <?= $attributesText ?>>
      <span class='input--error'><?php $errField = $id; if (isset($_SESSION[$errorPrefix.'-form-error-'.$errField])) { echo $_SESSION[$errorPrefix.'-form-error-'.$errField]; unset($_SESSION[$errorPrefix.'-form-error-'.$errField]); } ?></span>
    </div>
  <?php endif;
}
