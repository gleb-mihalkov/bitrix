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

/**
 * Возвращает слово, относящееся к числу, в нужной форме.
 * @param  integer $number Число.
 * @param  string $one     Форма слова для единицы. Пример: один стол.
 * @param  string $two     Форма слова для двойки. Пример: два стола.
 * @param  string $five    Форма слова для пяти. Пример: пять столов.
 * @return string          Форма слова, соответствующая указанному числу.
 */
function bx_fl_words($number, $one, $two, $five) {
  $number = intval($number);
  if ($number >= 11 && $number <= 14) return $five;

  $number = $number % 10;
  if ($number >= 2 && $number <= 4) return $two;
  if ($number == 1) return $one;

  return $five;
}