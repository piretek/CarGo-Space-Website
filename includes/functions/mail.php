<?php

function send_mail($client, $mailType, $vars = []) {
  global $rentStatus;

  $mail_to = "{$client['name']} {$client['surname']} <{$client['email']}>";
  $mail_from = 'CarGo Space <no-reply@cargospace.com>';
  $mail_reply = 'CarGo Space <contact@cargospace.com>';

  $headers = [
    'MIME-Version' => '1.0',
    'Content-type' => 'text/html; charset=utf-8',
    'From' => $mail_from,
    'Reply-To' => $mail_reply,
    'X-Mailer' => 'PHP/' . phpversion()
  ];

  switch($mailType) {
    case 'rent-created' :
      $subject = 'Zarezerwowałeś wypożyczenie auta '.$vars['rent-car'];

      $message = prepare_mail_content("{$client['name']} {$client['surname']}", [
        'Informujemy, że przyjęliśmy informację dot. wypożyczenia:',
        '<strong>Nr. wypożyczenia:</strong> '.$vars['rent-id'],
        '<strong>Pojazd:</strong> '.$vars['rent-car'],
        '<strong>Okres:</strong> '.$vars['rent-time'],
        '<strong>Cena wynajmu:</strong> '.$vars['rent-price'],
        '',
        'Prosimy o oczekiwanie na wiadomość od pracownika, potwierdzającą rezerwację.'
      ]);

      break;
    case 'rent-edited' :
      $subject = 'Zmieniliśmy informacje o Twoim wypożyczeniu';

      $message = prepare_mail_content("{$client['name']} {$client['surname']}", [
        "Informujemy, że zmieniliśmy informacje dot. wypożyczenia nr {$vars['rent-id']}:",
        '<strong>Pojazd:</strong> '.$vars['rent-car'],
        '<strong>Okres:</strong> '.$vars['rent-time'],
        '<strong>Cena wynajmu:</strong> '.$vars['rent-price'],
        '',
        'Jeżeli mają Państwo jakieś pytania, prosimy o skontaktowanie się z naszym Biurem Obsługi Klienta przez adres email lub telefon.'
      ]);

      break;
    case 'rent-status-changed' :
      $subject = $vars['rent-newstatus'] == 2 || $vars['rent-newstatus'] == 1 ? 'Twoje wypożyczenie zostało '.strtolower(htmlentities($rentStatus[(int) $vars['rent-newstatus']], ENT_QUOTES, 'UTF-8')) : 'Zwrot auta dokonany. Dziękujemy za wspólną podróż z CarGo Space!';

      $rentMessage[2] = [
        "Uprzejmie informujemy, że Twoje wypożyczenie zostało ".strtolower($rentStatus[(int) $vars['rent-newstatus']]).".",
        '<strong>Pojazd:</strong> '.$vars['rent-car'],
        '<strong>Okres:</strong> '.$vars['rent-time'],
        '<strong>Cena wynajmu:</strong> '.$vars['rent-price'],
        '',
        'Prosimy o stawienie się w naszym oddziale w dniu odbioru po odbiór auta i dokumentów. Umowa wynajmu zostanie podpisana przed oddaniem kluczyków.',
        'Jeszcze raz dziękujemy za skorzystanie z naszych usług',
        'Życzymy szerokiej drogi i zapraszamy ponownie!'
      ];

      $rentMessage[1] = [
        "Uprzejmie informujemy, że Twoje wypożyczenie w terminie: <br />{$vars['rent-time']}, auta: <br />{$vars['rent-car']}<br /> zostało ".strtolower($rentStatus[$vars['rent-newstatus']]).".",
        'W celu zapoznania się z powodem odrzucenia, nasz pracownik skontaktuje się z Państwem w ciągu 24H.'
      ];

      $rentMessage[4] = [
        'Odebraliśmy od Ciebie kluczyki do naszego auta. Mamy nadzieję, że podróż z CarGo Space przebiegła wyśmienicie!',
        'Jeżeli Ci się spodobało i masz taką możliwość możesz polecić nas swoim znajomym.',
        'Jeszcze raz dziękujemy za skorzystanie z naszych usług,',
        'Życzymy szerokich dróg i zapraszamy ponownie!'
      ];

      $message = prepare_mail_content("{$client['name']} {$client['surname']}", $rentMessage[(int) $vars['rent-newstatus']]);

      break;
  }

  return mail($mail_to, "=?UTF-8?B?".base64_encode($subject)."?=", $message, $headers);
}

function prepare_mail_content($user, $mailContent) {

  $mailTemplate = file('includes/mail.html');

  foreach($mailTemplate as $line => $value) {
    $pattern = "/[^{\{]+(?=}\})/";
    $matches = [];

    $replacement['user'] = $user;
    $replacement['mail_content'] = is_array($mailContent) ? implode("<br />", $mailContent) : $mailContent;

    if (preg_match($pattern, $value, $matches) == 1) {
      $mailTemplate[$line] = str_replace(['{{', '}}'], '', preg_replace($pattern, $replacement[$matches[0]], $value));
    }
  }

  return implode("\n", $mailTemplate);
}
