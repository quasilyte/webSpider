<?php
define('EMPTY_ARTIST', 13);
define('CHORD_THRESHOLD', 80);

// LyricsHarvester является обёрткой для go программы, которая выполняет
// GET-запросы. Полученные данные парсит регулярками.

class LyricsHarvester {
  private $addr;
  private $index;

  private $artistRegex;
  private $lyricsRegex;
  private $genresRegex;

  public function __construct($addr, $startIndex = 1) {
    $this->addr = $addr;
    $this->index = $startIndex;

    $this->artistRegex = cp1251("/Исполнитель.*<a href=\"(.*)\"/");
    $this->lyricsRegex = "/chordsBlock[^>]*(.*)<\/li/";
    $this->genresRegex = cp1251("/Жанр.*<span>(.*)<\/span*/");
  }

  public function getIndex() {
    return $this->index;
  }

  // Получение и предварительная фильтрация текста песни.
  public function fetchLyrics() {
    $results = array(
      'artist' => array(),
      'lyrics' => array()
    );

    $buf = $this->reap($this->addr . '/song/' . $this->index . '/');

    try {
      if(!$buf) {
        throw new Exception('GET request failed');
      }

      preg_match($this->artistRegex, $buf, $results['artist']);
      preg_match($this->lyricsRegex, $buf, $results['lyrics']);

      $results['artist'] = $results['artist'][1];
      $results['lyrics'] = $results['lyrics'][1];

      if(substr_count($results['lyrics'], '-') > CHORD_THRESHOLD) {
        throw new Exception('Chords detected. Ignoring');
      }

      if(strlen($results['artist']) == EMPTY_ARTIST || !$results['lyrics']) {
        throw new Exception('Empty artist or lyrics. Ignoring');
      }
    } catch(Exception $err) {
      printf("[-] at index=%d {%s}\n", $this->index++, $err->getMessage());
      return false;
    }

    printf("[+] at index=%d\n", $this->index++);
    return $results;
  }

  // Извлечения жанра по ссылке, извлеченной ранее на этапе fetchLyrics.
  public function fetchGenres($sub) {
    $result = array();
    $buf = $this->reap($this->addr . $sub);

    preg_match($this->genresRegex, $buf, $result);

    return explode(',', $result[1]);
  }

  // Запуск go-приложения.
  private function reap($url) {
    shell_exec("go\getter.exe \"$url\"");
    $response = file_get_contents("response.txt");

    if($response != '$ERROR$') {
      return $response;
    } else {
      return false;
    }
  }
}
?>
