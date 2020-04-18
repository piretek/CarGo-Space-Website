<?php

function send_mail($client, $mailType, $vars = []) {

  $mail_to = "{$client['name']} {$client['surname']} <{$client['email']}>";
  $mail_from = 'CarGo Space <no-reply@cargospace.com>';
  $mail_reply = 'CarGo Space <contact@cargospace.com>';

  $headers = [
    'MIME-Version' => '1.0',
    'Content-type' => 'text/html; charset=iso-8859-1',
    'To' => $mail_to,
    'From' => $mail_from,
    'Reply-To' => $mail_reply,
    'X-Mailer' => 'PHP/' . phpversion()
  ];

  switch($mailType) {
    case 'rent-created' :
      $subject = 'Samochód został wypożyczony';

      $message = prepare_mail_content("{$client['name']} {$client['surname']}", [
        'Informujemy, że przyjęliśmy wiadomość dot. wypożyczenia:',
        'Nr. wypożyczenia: '.$vars['rent-id'],
        'Pojazd: '.$vars['rent-car'],
        'Okres: '.$vars['rent-time'],
        'Cena wynajmu: '.$vars['rent-price'],
        '',
        'Prosimy o oczekiwanie na wiadomość od pracownika, potwierdzającą rezerwację.'
      ]);

      break;
  }


  return mail(null, $subject, $message, $headers);
}

function prepare_mail_content($user, $mailContent) {

  $mailTemplate = file('includes/mail.html');

  foreach($mailTemplate as $line => $value) {
    $pattern = "/[^{\{]+(?=}\})/";
    $matches = [];

    $replacement['user'] = $user;
    $replacement['mail_content'] = is_array($mailContent) ? implode("\n", $mailContent) : $mailContent;

    if (preg_match($pattern, $value, $matches) == 1) {
      $mailTemplate[$line] = str_replace(['{{', '}}'], '', preg_replace($pattern, $replacement[$matches[0]], $value));
    }
  }

  return implode("\n", $mailTemplate);
}
