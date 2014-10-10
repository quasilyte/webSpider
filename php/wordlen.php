<?php
if($_GET['sort'] != 'lenSort' && $_GET['sort'] != 'nameSort') {
  $_GET['sort'] = 'lenSort';
}

$page['title'] = 'разрядности';

function renderTable() {
echo <<<T_HEAD
<tr>
  <th><a href="browse.php?page=wordlen&sort=lenSort">
    Средняя длина
  </a></th>

  <th><a href="browse.php?page=wordlen&sort=nameSort">
    Музыкальный жанр
  </a></th>
</tr>
T_HEAD;

  $genres = fgetJson('json/genresInfo.json');

  if($_GET['sort'] == 'lenSort') {
    uasort($genres, $_GET['sort']);
  } else {
    uksort($genres, $_GET['sort']);
  }

  foreach($genres as $genreName => $genre) {
    echo <<<ROW
<tr>
  <td>{$genre['avgLength']}</td>
  <td>$genreName</td>
</tr>
ROW;
  }
}
?>
