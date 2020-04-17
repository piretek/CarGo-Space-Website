# System zarządzania wypożyczalnią samochodów
## Instrukcja instalacji
1. Po wypakowaniu plików, należy skopiować plik ```config.sample.php``` do pliku ```config.php```
2. Następnie należy wypełnić w pliku ```config.php``` dane do połączenia z bazą danych oraz adres witryny wg. następującego formatu:
```php
<?php

// Database credentials config
$config['db']['host'] = 'localhost';      // <- Tutaj adres serwera bazy danych.
$config['db']['login'] = 'root';          // <- Tutaj login do serwera bazy danych.
$config['db']['pass'] = '';               // <- Tutaj hasło do serwera bazy danych.
$config['db']['name'] = 'name';           // <- Tutaj nazwę używanej bazy danych.

$config['site_url'] = 'http://localhost'; // <- Tutaj adres, na którym będzie działać witryna.
$config['car_photo_upload_dir'] = '/assets/cars';

return $config;
```
3. Po konfiguracji, wystarczy wejść na stronę główną, aby załadować bazę danych.
4. Na samym końcu należy założyć konto użytkownika w systemie.

## Autorzy projektu
- Paulina Sznajder 3ft
- Michał Kowalski 3ft
- Piotr Czarnecki 3ft
### Lista zadań:
- [x] min. 8 podstron:
  - [x] Strona główna
  - [x] O firmie
  - [x] Kontakt (z formularzem)
  - [x] Kalkulator wynajmu
  - [x] Panel zarządzania
  - [x] Dodawanie pojazdów do bazy danych
  - [x] Usuwanie pojazdów z bazy danych
  - [x] Edycja samochodów w bazie danych
- [x] Wykorzystanie sesji
- [x] Wymagane elementy:
  - [x] formularz
  - [x] tabela
  - [x] lista numerowana/nienumerowana
  - [x] obrazki
- [x] Podłączony zewnętrzny arkusz CSS
- [x] Logowanie do panelu zarządzania z wykorzystaniem hashowania!
- [x] Tabele w bazie danych z rozbudowaną strukturą po 5 kolumn, połączone relacjami.
