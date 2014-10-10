<?php
error_reporting(E_ALL);
set_time_limit(0);
define('SECOND', 1000000);
// 12012
set_error_handler('noiceHandler', E_WARNING);
set_error_handler('noiceHandler', E_NOTICE);

function noiceHandler($errorCode, $errorString) {
  echo "{Error[$errorCode]} $errorString\n";
  die("Terminating...\n");
}

require 'utils.php';
require 'spider/crawler.php';

$crawler = new SiteCrawler(
  array(
    'addr' => 'http://pesni.ru',
    'index' => (int) file_get_contents('spider/data/index.txt'),
    'delay' => SECOND / 50,
    'limit' => 40000,
    'dictionary' => 'spider/data/rus_words.txt',
    'translations' => 'spider/data/genres_eng2rus.json',
    'aliases' => 'spider/data/genres_rus2rus.json'
  )
);

$crawler->raid();
?>
