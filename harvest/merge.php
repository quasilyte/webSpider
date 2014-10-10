<?php
error_reporting(E_ALL);
set_time_limit(0);

// Скрипт для слияния папок с данными.

require 'utils.php';

function osCpCall($src, $dest) {
  // windows.
  exec("echo d|xcopy \"$src\" \"$dest\" /E");

  // unix.
  // exec("cp \"$src\" \"$dest\" -y");
}

class Dir {
  public $path;
  public $files;

  public function __construct($path) {
    $this->path = $path;

    $this->files = scandir($path);
    unset($this->files[0]);
    unset($this->files[1]);
  }

  public function dir($path) {
    return $this->path . "/$path";
  }
}

function dirMerge($dstPath, $srcPath, $depth = 0) {
  $host = new Dir($dstPath);
  $guest = new Dir($srcPath);

  foreach($guest->files as $filename) {
    if(in_array($filename, $host->files)) {
      if($depth < 2) {
        dirMerge("$dstPath/$filename", "$srcPath/$filename", $depth + 1);
      } else {
        fileMerge("$dstPath/$filename", "$srcPath/$filename");
      }
    } else {
      osCpCall($guest->dir("$filename"), $host->dir("$filename"));
    }
  }
}

function fileMerge($dstFile, $srcFile) {
  $hostData = xcsvGet($dstFile);
  $guestData = xcsvGet($srcFile);

  foreach($guestData as $word => $occurs) {
    if(isset($hostData[$word])) {
      $hostData[$word] += $occurs;
    } else {
      $hostData[$word] = $occurs;
    }
  }

  xcsvPut($dstFile, $hostData);
} 

echo "Merging...\n";
dirMerge('dst', 'src');
echo "All done\n";
?>
