<?php
/**
 * Скрипт, автоматически обновляющий библиотеку из репозитория Github.
 */
define('BX_SRC_URL', 'https://github.com/gleb-mihalkov/bitrix/archive/master.tar.gz');
define('BX_SRC_DIR', 'bitrix-master');

function remove_item($item) {
  if (is_file($item)) {
    unlink($item);
    return;
  }

  $files = scandir($item);

  foreach ($files as $file) {
    if ($file == '.' || $file === '..') continue;
    $file = $item.'/'.$file;
    remove_item($file);
  }

  rmdir($item);
}

function rename_item($item, $item_new) {
  $is_remove = file_exists($item_new) && is_dir($item_new);
  if ($is_remove) remove_item($item_new);
  rename($item, $item_new);
}

$path = dirname(__FILE__);
$src_path = $path.'/'.trim(BX_SRC_DIR, '/');

$file_name = tempnam($path, 'bx_');
$file_name_zip = $file_name.'.tar.gz';
rename_item($file_name, $file_name_zip);
$file_name = $file_name_zip;

$file_raw = file_get_contents(BX_SRC_URL);
file_put_contents($file_name, $file_raw);

$cmd = 'cd '.$path.' && tar -xvf '.basename($file_name);
$cmd_int = null;
$cmd_out = system($cmd, $cmd_int);
remove_item($file_name);

$src = opendir($src_path);

while ($item = readdir($src)) {
  if ($item === '.' || $item === '..') continue;
  $item_old = $src_path.'/'.$item;
  $item_new = $path.'/'.$item;
  rename_item($item_old, $item_new);
}

closedir($src);
remove_item($src_path);

echo 'Library successfully updated.'.PHP_EOL;