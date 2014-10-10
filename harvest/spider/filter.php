<?php
// LyricsFilter обрабатывает данные, полученные сборщиком.

class LyricsFilter {
  private $regex;
  private $KB;
  private $forbiddenLexemes;

  public function __construct($dictionary, $translations, $aliases) {
    $this->regex = array(
      'metaCarving' => "/<font.*<\/font>/",
      'signatureCarving' => cp1251("/(п|П)рислал.*?<br\s*\/>/"),
      'cleansing' => cp1251("/[^а-яА-Я]/")
    );

    $this->KB = array(
      'dictionary' => file_get_contents($dictionary),
      'translations' => json_decode(file_get_contents($translations), true),
      'aliases' => json_decode(file_get_contents($aliases), true)
    );

    $this->forbiddenLexemes = array(
      cp1251('Припев')
    );
  }

  public function filtrate(&$lyrics) {
    $lyrics = preg_replace($this->regex['metaCarving'], '', $lyrics);
    $lyrics = preg_replace($this->regex['signatureCarving'], '', $lyrics);
    $lyrics = preg_replace($this->regex['cleansing'], '`', $lyrics);

    foreach($this->forbiddenLexemes as $lexeme) {
      $lyrics = str_replace($lexeme, '', $lyrics);
    }

    $lyrics = preg_replace("/`+/", '`', $lyrics);
  }

  // Переводит жанр, если он на латинице.
  // Если же жанр уже руссифицирован, проверяет, нужно ли привести название
  // к другому виду (правила в файле data/genre_rus2rus).
  public function translate(&$genres) {
    for($i = 0, $len = count($genres); $i < $len; ++$i) {
      $genres[$i] = trim($genres[$i]);
      $genres[$i] = strtolower($genres[$i]);

      if(isLatin($genres[$i])) {
        if(isset($this->KB['translations'][$genres[$i]])) {
          $genres[$i] = cp1251($this->KB['translations'][$genres[$i]]);
        } else {
          $genres[$i] = cp1251('разное');
        }
      } else {
        if(isset($this->KB['aliases'][utf8($genres[$i])])) {
          $genres[$i] = cp1251($this->KB['aliases'][utf8($genres[$i])]);
        }
      }
    }
  }

  // Возвращает массив, ключи которого - слова, а значения - вхождения слов
  // в тексте песни.
  public function extractWords($lyrics) {
    $words = array();
    $lexemes = explode('`', $lyrics);

    foreach($lexemes as $lexeme) {
      if($lexeme) {
        $lexeme = mb_strtolower(utf8($lexeme), 'UTF-8');

        if(!preg_match("/\n" . cp1251($lexeme) . "\r/", $this->KB['dictionary'])) {
          continue;
        }

        if(isset($words[$lexeme])) {
          $words[$lexeme] += 1;
        } else {
          $words[$lexeme] = 1;
        }
      }
    }

    return $words;
  }
}
?>
