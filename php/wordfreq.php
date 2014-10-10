<?php
if($_GET['sort'] != 'freqSort' && $_GET['sort'] != 'nameSort') {
  $_GET['sort'] = 'freqSort';
}

$page['title'] = 'частота';

function renderTable() {
echo <<<T_HEAD
<tr>
  <th><a href="browse.php?page=wordfreq&sort=freqSort&word={$_GET['word']}">
    Вхождений
  </a></th>

  <th><a href="browse.php?page=wordfreq&sort=nameSort&word={$_GET['word']}">
    Музыкальный жанр
  </a></th>
</tr>
T_HEAD;

  $genres = array();

  foreach(getDirFilenames('kb') as $genreName) {
    $genreName = utf8($genreName);

    $genres[] = array(
      'name' => $genreName,
      'freq' => getFreq($genreName, $_GET['word'])
    );
  }

  usort($genres, $_GET['sort']);

  foreach($genres as $genre) {
    echo "<tr><td>{$genre['freq']}</td><td>{$genre['name']}</td></tr>";
  }
}

function getFreq($genre, $word) {
  $filename = (
    "kb/$genre/" . 
    mb_substr($word, 0, 1,'UTF-8') . '/' . 
    mb_strlen($word, 'UTF-8') . '.xcsv'
  );

  $filename = cp1251($filename);

  if(file_exists($filename)) {
    $xcsv = xcsvGet($filename);
    return $xcsv[$word] ? $xcsv[$word] : 0;
  } else {
    return 0;
  }
}
?>
