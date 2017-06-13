<?php
/**
 * Набор функций для работы с путями.
 */

/**
 * Возвращает путь до файла, отсекая часть пути до корня сайта.
 * @param  string $path Абсолютный путь.
 * @return string       Путь, относительно корня сайта.
 */
function bx_path($path) {
  $base = $_SERVER['DOCUMENT_ROOT'];

  $no_cut = strpos($path, $base) !== 0;
  if ($no_cut) return $path;

  $base_length = strlen($base);

  $no_cut = isset($path[$base_length]) && $path[$base_length] !== '/';
  if ($no_cut) return $path;

  $path = substr($path, $base_length);

  $path = $path ? $path : '/';
  return $path;
}