<?php
/**
 * Скрипт, автоматически обновляющий библиотеку из репозитория Github.
 */
define('BX_SRC_URL', 'https://github.com/gleb-mihalkov/bitrix/archive/master.tar.gz');
define('BX_SRC_DIR', 'bitrix-master');

$path = dirname(__FILE__);
$src_path = $path.'/'.trim(BX_SRC_DIR, '/');

$file_name = tempnam($path, 'bx_');
$file_name_zip = $file_name.'.tar.gz';
rename($file_name, $file_name_zip);
$file_name = $file_name_zip;

$file_raw = file_get_contents(BX_SRC_URL);
file_put_contents($file_name, $file_raw);

$cmd = 'cd '.$path.' && tar -xvf '.basename($file_name);
$cmd_int = null;
$cmd_out = system($cmd, $cmd_int);
unlink($file_name);

$src = opendir($src_path);

while ($item = readdir($src)) {
  if ($item === '.' || $item === '..') continue;
  $item_old = $src_path.'/'.$item;
  $item_new = $path.'/'.$item;
  rename($item_old, $item_new);
}

closedir($src);
rmdir($src_path);

echo 'Library successfully updated.'.PHP_EOL;