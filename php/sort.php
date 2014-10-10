<?php
function nameSort($a, $b) {
  return $a > $b;
}

function freqSort($a, $b) {
  return $a['freq'] < $b['freq'];
}

function lenSort($a, $b) {
  return $a['avgLength'] > $b['avgLength'] ? -1 : 1;
}

function cntSort($a, $b) {
  return $a['wordCount'] > $b['wordCount'] ? -1 : 1;
}
?>
