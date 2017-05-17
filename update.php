<?php
/**
 * Скрипт, автоматически обновляющий библиотеку из репозитория Github.
 */

define('BX_SRC_URL', 'https://github.com/gleb-mihalkov/bitrix/archive/master.zip');
define('BX_SRC_DIR', './bitrix-master');

$file_raw = file_get_contents(BX_SRC_URL);
$file_name = tempnam('.', 'bx_');
file_put_contents($file_name, $file_raw);

$zip = new ZipArchive();
$zip->open($file_name);
$zip->extractTo('.');
$zip->close();
unlink($file_name);

$dir = opendir(BX_SRC_DIR);

while ($item = readdir($dir)) {
  if ($item === '.' || $item === '..') continue;
  $item_old = BX_SRC_DIR.'/'.$item;
  $item_new = './'.$item;
  rename($item_old, $item_new);
}

closedir($dir);
rmdir(BX_SRC_DIR);

echo 'Library successfully updated.'.PHP_EOL;