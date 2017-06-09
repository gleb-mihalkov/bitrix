<?php
namespace Bx
{
  Validator::setMessage('required', 'Поле дожно быть заполнено.');
  Validator::setMessage('email', 'Неверный почтовый адрес.');
  Validator::setMessage('float', 'Неверный формат числа.');
  Validator::setMessage('integer', 'Неверный формат числа.');
  Validator::setMessage('digits', 'Неверный формат числа.');
  Validator::setMessage('ccnum', 'Неверный номер банковской карты.');
  Validator::setMessage('oneOf', 'Недопустимое значение.');
  Validator::setMessage('date', 'Неверный формат даты.');
  Validator::setMessage('ip', 'Неверный IP-адрес.');
  Validator::setMessage('url', 'Неверный URL.');

  Validator::setMessage('min', function($limit, $include) {
    return "Число не может быть меньше $limit.";
  });

  Validator::setMessage('max', function($limit, $include) {
    return "Число не может быть больше $limit.";
  });

  Validator::setMessage('between', function($min, $max, $include) {
    return "Число должно быть в интервале между $min и $max.";
  });

  Validator::setMessage('minLength', function($length) {
    return "Строка должна быть длинее $length символов.";
  });

  Validator::setMessage('maxLength', function($length) {
    return "Строка должна быть короче $length символов.";
  });

  Validator::setMessage('length', function($length) {
    return "Длина строка должна быть $length символов.";
  });

  Validator::setMessage('matches', function($field, $label) {
    return "Поле должно совпадать c полем $label.";
  });

  Validator::setMessage('notMatches', function($field, $label) {
    return "Поле должно отличаться от $label.";
  });

  Validator::setMessage('startsWith', function($sub) {
    return "Строка должна начинаться с '$sub'.";
  });

  Validator::setMessage('notStartsWith', function($sub) {
    return "Строка не должна начинаться с '$sub'.";
  });

  Validator::setMessage('endsWith', function($sub) {
    return "Строка должна оканчиваться на '$sub'.";
  });

  Validator::setMessage('notEndsWith', function($sub) {
    return "Строка не должна оканчиваться на '$sub'.";
  });

  Validator::setMessage('minDate', function($date, $format) {
    return "Дата не должна быть меньше $date.";
  });

  Validator::setMessage('maxDate', function($date, $format) {
    return "Дата не должна быть больше $date.";
  });

}