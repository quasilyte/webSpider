<?php
header('Content-Type: text/html; charset=utf-8');

$page['title'] = 'главная';

include 'html/' . basename(__FILE__, '.php') . '.html';
?>
