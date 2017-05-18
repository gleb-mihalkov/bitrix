<?php
/**
 * Набор функций для работы Highload-блоками.
 */

/**
 * Получает класс для управления записями в блоке.
 * @param  string $block_id ID блока.
 * @return string           Имя класса.
 */
function bx_hl_class($block_id) {
  static $classes = array();
  if ($classes[$block_id]) return $classes[$block_id];

  CModule::IncludeModule('highloadblock');

  $block_table = Bitrix\Highloadblock\HighloadBlockTable::getById($block_id)->fetch();
  $block_entity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($block_table);
  $block_class = $block_entity->getDataClass($block_entity);

  return $classes[$block_id] = $block_class;
}

/**
 * Получает элемент по его ID.
 * @param  string $block_id    ID блока.
 * @param  string $instance_id ID элемента.
 * @return array               Элемент.
 */
function bx_hl_get($block_id, $instance_id) {
  $class = bx_hl_class($block_id);
  return $class::getById($instance_id)->Fetch();
}

/**
 * Получает перечисление элементов из блока.
 * @param  string             $block_id ID блока.
 * @param  array              $filter   Фильтр элементов.
 * @param  array              $order    Порядок сортировки элементов.
 * @param  array|integer      $limit    Максимальное количество элементов и (опционально) начальная позиция перечисления.
 * @return \Bx\ResultHighload           Перечисление элементов блока.
 */
function bx_hl_select($block_id, $filter = null, $order = null, $limit = null) {
  $class = bx_hl_class($block_id);
  $result = new Bx\ResultHighload($class);

  if ($filter) $result = $result->where($filter);
  if ($order) $result = $result->order($order);
  if ($limit) $result = $result->limit($limit);

  return $result;
}

/**
 * Получает значение свойства элемента, используя блок как фиксированный словарь значений.
 * @param  string $block_id      ID блока-словаря.
 * @param  string $instance_id   ID элемента словаря.
 * @param  string $instance_prop Имя свойства, содержащего значение словаря.
 * @return string                Значение из словаря.
 */
function bx_hl_enum($block_id, $instance_id, $instance_prop = null) {
  static $enums = array();
  $is_init = !isset($enums[$block_id]);

  if ($is_init) {
    $enum = bx_hl_select($block_id)->index('ID');
    $enums[$block_id] = $enum;
  }

  $enum = $enums[$block_id];
  $item = isset($enum[$instance_id]) ? $enum[$instance_id] : null;
  $item = $instance_prop ? (isset($item[$instance_prop]) ? $item[$instance_prop] : null) : $item;

  return $item;
}



/**
 * Обрабатывает результат скалярного запроса к блоку (обновление, удаление, вставка).
 * @param  \Bitrix\Main\Entity\Result $result Результат запроса.
 * @param  string                     $id     ID элемента.
 * @return string                             ID элемента.
 */
function bx_hl_result($result, $id = null) {
  $is_success = $result->isSuccess();

  if ($is_success) {
    if ($id === null) $id = $result->getId();
    return $id;
  }

  $errors = $result->getErrors();

  foreach ($errors as $error) {
    $message = $error->getMessage();
    $code = $error->getCode();

    throw new Exception($message, $code);
  }

  $message = 'Unknown HighloadBlock error.';
  throw new Exception($message, 0);
}

/**
 * Добавляет в блок новый элемент.
 * @param  string $block_id ID блока.
 * @param  array  $instance Свойства элемента.
 * @return string           ID нового элемента.
 */
function bx_hl_insert($block_id, $instance) {
  $class = bx_hl_class($block_id);
  $result = $class::add($instance);
  return bx_hl_result($result);
}

/**
 * Обновляет существующий элемент.
 * @param  string $block_id    ID блока.
 * @param  string $instance_id ID элемента.
 * @param  array  $instance    Свойства элемента.
 * @return string              ID нового элемента.
 */
function bx_hl_update($block_id, $instance_id, $instance) {
  $class = bx_hl_class($block_id);
  $result = $class::update($instance_id, $instance);
  return bx_hl_result($result, $instance_id);
}

/**
 * Добавляет новый или обновляет существующий элемент.
 * @param  string $block_id ID блока.
 * @param  string $instance Свойства элемента.
 * @return string           ID элемента.
 */
function bx_hl_save($block_id, $instance) { 
  $instance_id = isset($instance['ID']) ? $instance['ID'] : null;
  unset($instance['ID']);

  return $instance_id
    ? bx_hl_update($block_id, $instance_id, $instance)
    : bx_hl_insert($block_id, $instance);
}

/**
 * Удаляет элемент.
 * @param  string $block_id    ID блока.
 * @param  string $instance_id ID элемента.
 * @return string              ID элемента.
 */
function bx_hl_delete($block_id, $instance_id) {
  $class = bx_hl_class($block_id);
  $result = $class::delete($instance_id);
  return bx_hl_result($result, $instance_id);
}