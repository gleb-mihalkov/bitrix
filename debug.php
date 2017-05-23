<?php
/**
 * Набор функций для отладки.
 */

/**
 * Выводит отладочную информацию о значении.
 * @param  mixed $value Значение.
 * @return void
 */
function bx_dump($value) {
  $debug = bx_dump_raw($value);
  echo "<!-- $debug -->";
}

/**
 * Возвращает отладочную информацию о значении.
 * @param  mixed  $value Значение.
 * @return string        Отладочная информация.
 */
function bx_dump_raw($value) {
  ob_start();
  var_dump($value);
  $debug = ob_get_contents();
  ob_end_clean();
  return $debug;
}