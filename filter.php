<?php
/**
 * Набор фильтров для отображения данных.
 */

/**
 * Возвращает текстовое представление числа.
 * @param  double  $number             Исходное число.
 * @param  integer $precision          Количество знаков после запятой.
 * @param  string  $float_separator    Разделитель целой и дробной части числа. По умолчанию - запятая.
 * @param  string  $division_separator Разделитель разрядов числа. По умолчанию - пробел.
 * @return string                      Текстовое представление числа.
 */
function bx_fl_number($number, $precision = 2, $float_separator = ',', $division_separator = ' ') {
  $number = round($number, $precision);
  $text = number_format($number, $precision, $float_separator, $division_separator);
  return $text;
}