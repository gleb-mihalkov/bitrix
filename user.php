<?php
/**
 * Набор функций для работы с пользователями.
 */

/**
 * Получает пользователя по его ID.
 * @param  string $user_id ID пользователя.
 * @return array           Пользователь.
 */
function bx_user_get($user_id) {
  // @todo: implement;
}

/**
 * Возвращает список пользователей.
 * @param  array          $filter Фильтр пользователей.
 * @param  array          $order  Порядок сортировки пользователей.
 * @param  array          $limit  Максимальное количество пользователей
 *                                и начальное смещение элементов.
 * @return \Bx\ResultUser         Перечисление пользователей.
 */
function bx_user_select($filter = null, $order = null, $limit = null) {
  // @todo: implement;
}

/**
 * Добавляет нового пользователя.
 * @param  array   $user Данные пользователя.
 * @return integer       ID нового пользователя.
 */
function bx_user_insert($user) {
  // @todo: implement;
}

/**
 * Обновляет существующего пользователя.
 * @param  integer $user_id ID пользователя.
 * @param  array   $user    Данные пользователя.
 * @return integer          ID пользователя.
 */
function bx_user_update($user_id, $user) {
  // @todo: implement;
}

/**
 * Сохраняет пользователя.
 * @param  array   $user Пользователь.
 * @return integer       ID пользователя.
 */
function bx_user_save($user) {
  // @todo: implement;
}

/**
 * Удаляет пользователя.
 * @param  integer $user_id ID пользователя.
 * @return integer          ID пользователя.
 */
function bx_user_delete($user_id) {
  // @todo: implement;
}

/**
 * Генерирует случайный пароль заданной длины из указанных символов.
 * @param  integer $length Длина пароля.
 * @param  string  $chars  Список символов, из которых будет сгененирован пароль.
 * @return string          Случайный пароль.
 */
function bx_user_password($length = 6, $chars = null) {
  if (!$chars) {
    $chars = 'absdefjhikglmnopqrstuvwxyzABCDEFJHIKGLMNOPQRSTUVWXYZ0123456789_';
  }

  $chars_length = strlen($chars);
  $word = '';

  for ($i = 0; $i < $length; $i++) {
    $index = rand(0, $chars_length - 1);
    $char = $chars[$index];
    $word .= $char;
  }

  return $word;
}