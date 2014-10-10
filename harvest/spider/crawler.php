<?php
require 'harvester.php';
require 'filter.php';
require 'bookkeeper.php';

class SiteCrawler {
  private $delay;
  private $limit;

  private $harvester;
  private $filter;
  private $bookkeeper;

  public function __construct($params) {
    $this->delay = $params['delay'];
    $this->limit = $params['limit'];

    $this->harvester = new LyricsHarvester(
      $params['addr'],
      $params['index']
    );

    $this->filter = new LyricsFilter(
      $params['dictionary'],
      $params['translations'],
      $params['aliases']
    );

    $this->bookkeeper = new WordsBookkeeper();
  }

  // Запуск паука.
  public function raid() {
    $data;

    echo "\tSiteCrawler::raid() launched\n";

    for($i = 0; $i < $this->limit; ++$i) {
      if($data = $this->harvester->fetchLyrics()) {
        $this->filter->filtrate($data['lyrics']);
        $data['genres'] = $this->harvester->fetchGenres($data['artist']);

        $this->filter->translate($data['genres']);
        $data['lyrics'] = $this->filter->extractWords($data['lyrics']);
        $data['index'] = $this->harvester->getIndex();

        $this->bookkeeper->manage($data);

        usleep($this->delay);
      } else {
        usleep($this->delay / 2);
      }
    }

    echo "\tSiteCrawler::raid() returned\n";
  }
}
?>
