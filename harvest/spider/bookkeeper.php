<?php
define('NO_DATA', cp1251('нет данных'));

// WordsBookkeeper осуществляет действия с данными:
// обновляет старые записи или добавляет новые.
class WordsBookkeeper {
  private $words;

  // Процедура занесения данных в накопитель (директория stash).
  public function manage($data) {
    $path;
    $filename;

    foreach($data['lyrics'] as $word => $occurs) {
      $word = cp1251($word);

      foreach($data['genres'] as $genre) {
        // Если нет сведений о жанре - не сохраняем данные.
        if($genre == NO_DATA) {
          continue;
        }

        $path = $this->pathGen($genre, $word);
        $filename = "$path/" . strlen($word) . '.xcsv';

        $this->manageDir($path);
        $this->manageFile($filename, utf8($word), $occurs);
      }
    }

    // Обновляем последний обработанный индекс для удобства последующих
    // запусков паука (он начнет именно с этого индекса).
    file_put_contents('spider/data/index.txt', $data['index']);
  }

  private function pathGen($genre, $word) {
    return "stash/$genre/{$word[0]}";
  }

  private function manageDir($path) {
    if(!file_exists($path)) {
      mkdir($path, 0777, true);
    }
  }

  // Если файла не существовало - создаст его,
  // иначе перезапишет с новыми данными.
  private function manageFile($filename, $word, $occurs) {
  $xcsv;

  if(file_exists($filename)) {
      $xcsv = xcsvGet($filename);

      if(isset($xcsv[$word])) {
        $xcsv[$word] += $occurs;
      } else {
        $xcsv[$word] = $occurs;
      }

      xcsvPut($filename, $xcsv);
    } else {
      xcsvPut($filename, array($word => $occurs));
    } 
  }
}
?>
