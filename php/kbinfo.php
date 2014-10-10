<?php
if($_GET['sort'] != 'cntSort' && $_GET['sort'] != 'nameSort') {
  $_GET['sort'] = 'cntSort';
}

$page['title'] = 'другое';

function renderTable() {
echo <<<T_HEAD
<tr>
  <th><a href="browse.php?page=kbinfo&sort=cntSort">
    Количество слов в KB
  </a></th>

  <th><a href="browse.php?page=kbinfo&sort=nameSort">
    Музыкальный жанр
  </a></th>
</tr>
T_HEAD;

  $genres = fgetJson('json/genresInfo.json');

  if($_GET['sort'] == 'cntSort') {
    uasort($genres, $_GET['sort']);
  } else {
    uksort($genres, $_GET['sort']);
  }
  
  foreach($genres as $genreName => $genre) {

    echo <<<ROW
<tr>
  <td>{$genre['wordCount']}</td>
  <td>$genreName</td>
</tr>
ROW;
  }
}
?>
