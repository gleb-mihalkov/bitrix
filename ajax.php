<?php
/**
 * Набор функций для реализации асинхронного API.
 */

/**
 * Служебные результаты запроса.
 */

// Ошибка HTTP 400 Bad Request (в запросе переданы некорректные данные).
define('BX_BAD_REQUEST', '_BX_AJAX_BAD_REQUEST');

// Ошибка HTTP 403 Forbidden (доступ к операции запрещен для данного пользователя).
define('BX_FORBIDDEN', '_BX_AJAX_FORBIDDEN');

// Ошибка HTTP 418 I'm a teapot (Ошибка, возникающая если сервер - чайник.
// Да, такой код действительно существует в спецификации.
// Как я мог не вставить этот чудесный статус ответа)?
define('BX_IM_A_TEAPOT', '_BX_AJAX_IM_A_TEAPOT');

// Ошибка HTTP 404 Not Found (ресурс не найден).
define('BX_NOT_FOUND', '_BX_AJAX_NOT_FOUND');

// Запрос не должен возвращать результатов, но прошел успешно.
define('BX_SUCCESS', 'ok');

/**
 * Вызывает указанную функцию как замыкание, если запрос содержит указанные параметры.
 * @param  string    $param     Имя параметра. Метод будет вызван, если данный параметр
 *                              присутствует в запросе.
 * @param  string    $value     Значение параметра. Метод будет вызван, если значение параметра
 *                              из запроса соответствует указанному.
 * @param  callable  $callback  Функция, возвращающая данные для выдачи клиенту.
 * @param  string    $mime      MIME-тип для установки заголовока ответа Content-Type.
 * @param  callable  $serialize Функция, сериализующая значение в строку.
 * @return mixed                Результат работы функции.
 */
function _bx_ajax($param, $value, $callback, $mime, $serialize) {

  if ($value === null && $callback === null) {
    $callback = $param;
    $param = null;
  }

  if ($callback === null) {
    $callback = $value;
    $value = null;
  }

  $request = \Bx\Request::getInstance();

  if ($param) {
    $isExit = !isset($request[$param]);
    if ($isExit) return;
  }

  if ($value) {
    $isExit = $request[$param] !== $value;
    if ($isExit) return;
  }

  $is_exception = true;
  $result = null;

  try {
    $result = $callback($request);
    $is_exception = false;
  }
  catch (Exception $e) {
    $result = array(
      'message' => $e->getMessage(),
      'code' => $e->getCode()
    );
  }

  header("Content-Type: $mime; charset=utf8");

  if ($result === BX_FORBIDDEN) {
    header('HTTP/1.1 403 Forbidden', true, 403);
    return;
  }

  if ($result === BX_NOT_FOUND) {
    header('HTTP/1.1 404 Not Found', true, 404);
    return;
  }

  if ($result === BX_BAD_REQUEST) {
    header('HTTP/1.1 400 Bad Request', true, 400);
    return;
  }

  if ($result === BX_IM_A_TEAPOT) {
    header('HTTP/1.1 418 I\'m a teapot', true, 418);
    return;
  }

  if ($is_exception) {
    header('HTTP/1.1 500 Internal Server Error', true, 500);
  }

  if ($result === null) return;
  echo $serialize($result);
}

/**
 * Сериализует значение в ответ сервера в формате JSON.
 * @param  mixed  $value Значение.
 * @return string        Строка ответа сервера.
 */
function _bx_ajax_serialize_json($value) {
  return json_encode($value);
}

/**
 * Сериализует значение в обычный текст.
 * @param  mixed  $value Значение.
 * @return string        Строка ответа сервера.
 */
function _bx_ajax_serialize_text($value) {
  return (string) $value;
}

/**
 * Сериализует значение в HTML.
 * @param  mixed  $value Значение.
 * @return string        Строка ответа сервера.
 */
function _bx_ajax_serialize_html($value) {
  return (string) $value;
}

/**
 * Вызывает указанную функцию как замыкание, если запрос содержит указанные параметры.
 * Ответ сервера в текстовом формате.
 * @param  string    $param     Опционально: Имя параметра. Метод будет вызван, если данный параметр
 *                              присутствует в запросе.
 * @param  string    $value     Опционально: Значение параметра. Метод будет вызван, если значение параметра
 *                              из запроса соответствует указанному.
 * @param  callable  $callback  Функция, возвращающая данные для выдачи клиенту.
 * @return void
 */
function bx_ajax($param, $value = null, $callback = null) {
  _bx_ajax($param, $value, $callback, 'text/plain', '_bx_ajax_serialize_text');
}

/**
 * Вызывает указанную функцию как замыкание, если запрос содержит указанные параметры.
 * Ответ сервера в формате JSON.
 * @param  string    $param     Имя параметра. Метод будет вызван, если данный параметр
 *                              присутствует в запросе.
 * @param  string    $value     Значение параметра. Метод будет вызван, если значение параметра
 *                              из запроса соответствует указанному.
 * @param  callable  $callback  Функция, возвращающая данные для выдачи клиенту.
 * @return void
 */
function bx_ajax_json($param, $value = null, $callback = null) { 
  _bx_ajax($param, $value, $callback, 'application/x-javascript', '_bx_ajax_serialize_json');
}

/**
 * Вызывает указанную функцию как замыкание, если запрос содержит указанные параметры.
 * Ответ сервера в формате HTML.
 * @param  string    $param     Опционально: Имя параметра. Метод будет вызван, если данный параметр
 *                              присутствует в запросе.
 * @param  string    $value     Опционально: Значение параметра. Метод будет вызван, если значение параметра
 *                              из запроса соответствует указанному.
 * @param  callable  $callback  Функция, возвращающая данные для выдачи клиенту.
 * @return void
 */
function bx_ajax_html($param, $value = null, $callback = null) {
  _bx_ajax($param, $value, $callback, 'text/html', '_bx_ajax_serialize_html');
}