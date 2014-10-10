<?php
require 'utils.php';
set_time_limit(0);

// Скрипт для формирования данных о жанрах и их содержимом.

$data = array();
$nest = '../kb';
$genres = getDirFilenames($nest);

foreach($genres as $genre) {
  $key = utf8($genre);

  $data[$key] = array();
  genreInfo($nest, $genre);

  $avgLength = $data[$key]['charCount'] / $data[$key]['wordCount']; 
  $data[$key]['avgLength'] = number_format($avgLength, 2, '.', '');
}

function genreInfo($nest, $genre) {
  $literals = getDirFilenames("$nest/$genre");

  foreach($literals as $literal) {
    getLiteralInfo("$nest/$genre/$literal", $genre);
  }
}

function getLiteralInfo($literal, $genre) {
  global $data;

  $wordFiles = getDirFilenames($literal);

  foreach($wordFiles as $wordFile) {
    $path = "$literal/$wordFile";
    $count = count(xcsvGet($path));

    $data[utf8($genre)]['wordCount'] += $count;
    $data[utf8($genre)]['charCount'] += $count * basename($path, '.xcsv');
  }
}

file_put_contents('genresInfo.json', json_encode($data));
?>
