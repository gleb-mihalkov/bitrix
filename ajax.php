<?php
/**
 * Набор функций для реализации асинхронного API.
 */

/**
 * Служебные результаты запроса.
 */
define('BX_NOT_RESOLVED', '_BX_AJAX_NOT_RESOLVED');
define('BX_NO_BODY', '_BX_AJAX_NO_BODY');
define('BX_BAD_REQUEST', '_BX_AJAX_BAD_REQUEST');
define('BX_FORBIDDEN', '_BX_AJAX_FORBIDDEN');
define('BX_IM_A_TEAPOT', '_BX_AJAX_IM_A_TEAPOT');
define('BX_NOT_FOUND', '_BX_AJAX_NOT_FOUND');

define('BX_SUCCESS', 'ok');

/**
 * Вызывает указанную функцию как замыкание, если запрос содержит указанные параметры.
 * @param  array    $args Имя параметра.
 * @return mixed          Значение.
 */
function _bx_ajax($param, $value, $callback) {

  if ($value == null && $callback == null) {
    $callback = $param;
    $param = null;
  }

  if ($callback == null) {
    $callback = $value;
    $value = null;
  }

  $request = \Bx\Request::getInstance();

  if ($param) {
    $isExit = !isset($request[$param]);
    if ($isExit) return BX_NOT_RESOLVED;
  }

  if ($value) {
    $isExit = $request[$param] !== $value;
    if ($isExit) return BX_NOT_RESOLVED;
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

  if ($result === BX_FORBIDDEN) {
    header('HTTP/1.1 403 Forbidden', true, 403);
    return BX_NO_BODY;
  }

  if ($result === BX_NOT_FOUND) {
    header('HTTP/1.1 404 Not Found', true, 404);
    return BX_NO_BODY;
  }

  if ($result === BX_BAD_REQUEST) {
    header('HTTP/1.1 400 Bad Request', true, 400);
    return BX_NO_BODY;
  }

  if ($result === BX_IM_A_TEAPOT) {
    header('HTTP/1.1 418 I\'m a teapot', true, 418);
    return BX_NO_BODY;
  }

  if ($is_exception) {
    header('HTTP/1.1 500 Internal Server Error', true, 500);
  }

  return $result;
}

/**
 * Вызывает указанную функцию в качестве замыкания и возвращает
 * результат её работы как ответ сервера в формате JSON.
 * @param  string   $param    Опционально: имя параметра, который должен присутствовать быть в запросе,
 *                            чтобы переданная функция выполнилась. Если параметр задан не будет,
 *                            то функция выполнится в любом случае.
 * @param  string   $value    Опционально: требуемое значение параметра, указанного выше.
 * @param  callable $callback Замыкание, принимающее объект с параметрами запроса и возвращающее
 *                            ответ сервера.
 * @return void
 */
function bx_ajax_json($param, $value = null, $callback = null) { 
  $result = _bx_ajax($param, $value, $callback);
  if ($result === BX_NOT_RESOLVED) return;

  header('Content-Type: application/x-javascript; charset=utf8');
  if ($result === BX_NO_BODY) return;

  echo json_encode($result);
}

/**
 * Вызывает указанную функцию в качестве замыкания и возвращает результат её работы как ответ сервера в виде обычного текста.
 * @param  string   $param    Опционально: имя параметра, который должен присутствовать быть в запросе,
 *                            чтобы переданная функция выполнилась. Если параметр задан не будет,
 *                            то функция выполнится в любом случае.
 * @param  string   $value    Опционально: требуемое значение параметра, указанного выше.
 * @param  callable $callback Замыкание, принимающее объект с параметрами запроса и возвращающее
 *                            ответ сервера.
 * @return void
 */
function bx_ajax($param, $value = null, $callback = null) {
  $result = _bx_ajax($param, $value, $callback);
  if ($result === BX_NOT_RESOLVED) return;

  header('Content-Type: text/plain; charset=utf8');
  if ($result === BX_NO_BODY) return;

  echo $result;
}