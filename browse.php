<?php
header('Content-Type: text/html; charset=utf-8');

require 'utils.php';
require 'php/sort.php';

$page = array();

if(file_exists("php/{$_GET['page']}.php")) {
  require "php/{$_GET['page']}.php";
} else {
  die('Запрашиваемая страница не существует');
}

include "html/{$_GET['page']}.html";
?>
