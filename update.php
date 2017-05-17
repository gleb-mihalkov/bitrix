<?php
/**
 * Скрипт, автоматически обновляющий библиотеку из репозитория Github.
 */

define('BX_SRC_URL', 'https://github.com/gleb-mihalkov/bitrix/archive/master.zip');
define('BX_SRC_DIR', 'bitrix-master');

$path = dirname(__FILE__);
$src_path = $path.'/'.trim(BX_SRC_DIR, '/');

$file_raw = file_get_contents(BX_SRC_URL);
$file_name = tempnam($path, 'bx_');
file_put_contents($file_name, $file_raw);

$zip = new ZipArchive();
$zip->open($file_name);
$zip->extractTo('.');
$zip->close();
unlink($file_name);

$src_path = opendir(BX_SRC_DIR);

while ($item = readdir($src_path)) {
  if ($item === '.' || $item === '..') continue;
  $item_old = $src_path.'/'.$item;
  $item_new = $path.'/'.$item;
  rename($item_old, $item_new);
}

closedir($src_path);
rmdir($src_path);

echo 'Library successfully updated.'.PHP_EOL;