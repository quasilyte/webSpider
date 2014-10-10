<?php
define('MAX_CSV_LINE_LEN', 48);

// Принимает закодированную в UTF-8 строку и возвращает CP-1251 аналог.
function cp1251($string) {
  return iconv('UTF-8', 'windows-1251', $string);
}

// Принимает закодированную в CP-1251 строку и возвращает UTF-8 аналог.
function utf8($string) {
  return iconv('windows-1251', 'UTF-8', $string);
}

// Вернёт false, если строка содержит хотя бы один многобайтовый символ.
function isLatin($string) {
  return strlen($string) == mb_strlen($string, 'UTF-8');
}

function fputJson($filename, $json) {
  file_put_contents($filename, json_encode($json));
}

function fgetJson($filename) {
  return json_decode(file_get_contents($filename), true);
}

function getDirFilenames($path) {
  $filenames = scandir($path);
  unset($filenames[0]);
  unset($filenames[1]);

  return $filenames;
}

// Производительность этого вида данных ниже на 10-15%, чем у нативных
// функций json decode/encode, однако ключами json не могут быть символы
// кириллицы, отсюда принуждение использовать utf8 - что накладно.
// Этот подход даёт аналогичный ассоциативный массив на выходе и позволяет
// хранить данные в cp1251.
function xcsvGet($filename) {
  $data = array();
  $buf = file_get_contents($filename);

  for($i = 0, $j = 0, $key, $len = strlen($buf); $i < $len - 4; ++$i) {
    while($buf[++$i] != '=');
    $key = substr($buf, $j, $i - $j);
    $j = ++$i;

    while($buf[++$i] != ',');
    $data[$key] = (int) substr($buf, $j, $i - $j);
    $j = ++$i;
  }

  return $data;
}

function xcsvPut($filename, $xcsv) {
  $file = fopen($filename, 'wb');

  foreach($xcsv as $word => $occurs) {
    fwrite($file, "$word=$occurs,");
  }

  fclose($file);
}
?>
