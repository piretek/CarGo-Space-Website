<?php

function verifyConfig($config) {
  $configErrors = [];
  if (!isset($config['site_url']) || empty($config['site_url'])) {
    $configErrors[] = "Opcja 'site_url': Brak zdefiniowanego adresu url witryny.";
  }

  if (!isset($config['db'])) {
    $configErrors[] = "Zbiór 'db': Brak zdefiniowanego zbioru konfigruacji ustawień do połączenia z bazą danych.";
  }

  if (!isset($config['db']['host']) || empty($config['db']['host'])) {
    $configErrors[] = "Opcja 'db -> host': Brak zdefiniowanego hosta bazy danych.";
  }

  if (!isset($config['db']['login']) || empty($config['db']['login'])) {
    $configErrors[] = "Opcja 'db -> login': Brak zdefiniowanego loginu do bazy danych.";
  }

  if (!isset($config['db']['pass'])) {
    $configErrors[] = "Opcja 'db -> pass': Brak zdefiniowanego hasła do bazy danych.";
  }

  if (!isset($config['db']['name']) || empty($config['db']['name'])) {
    $configErrors[] = "Opcja 'db -> pass': Brak zdefiniowanej nazwy używanej bazy danych.";
  }

  return $configErrors;
}
