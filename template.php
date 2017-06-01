<?php
/**
 * Набор функций для работы с представлениями.
 */

/**
 * Выполняет указанное представление и выводит результат в поток вывода.
 * @param  string $path     Путь до файла представления.
 * @param  array  $arResult Массив дополнительных параметров,
 *                          передаваемых в представление.
 * @return void
 */
function bx_tp_include($path, $arResult = array()) {
  global $APPLICATION;

  $APPLICATION->IncludeFile($path, $arResult, array(
    'SHOW_BORDER' => false,
    'MODE' => 'php'
  ));
}

/**
 * Выполняет указанное представление и возвращает результат в виде строки.
 * @param  string $path     Путь до файла представления.
 * @param  array  $arResult Массив дополнительных параметров, передаваемых
 *                          в представление.
 * @return string           Строка.
 */
function bx_tp_include_raw($path, $arResult = array()) {
  ob_start();
  bx_tp_include($path, $arResult);
  $content = ob_get_contents();
  ob_end_clean();
  return $content;
}

/**
 * Отображает содержимое или записывает значение в буфер отложенного вывода шаблона.
 * @param  string $name  Имя буфера.
 * @param  mixed  $value Значение для отображения.
 * @return void
 */
function bx_tp_buffer($name, $value) {
  // @todo: implement;
}

/**
 * Получает ID элемента HTML в представлении для подключения возможности
 * редактировать элемент в публичной части.
 * @param  Bitrix\Template $template Шаблон компонента.
 * @param  array           $element  Элемент.
 * @return string                    ID для вставки в HTML.
 */
function bx_tp_edit($template, $element) {
  $iblock_id = $element['IBLOCK_ID'];
  $element_id = $element['ID'];
  
  $edit_link = $element['EDIT_LINK'];
  $edit_params = CIBlock::GetArrayByID($iblock_id, 'ELEMENT_EDIT');

  $delete_link = $element['DELETE_LINK'];
  $delete_params = CIBlock::GetArrayByID($iblock_id, 'ELEMENT_DELETE');
  $delete_opts = array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM'));

  $template->AddEditAction($element_id, $edit_link, $edit_params);
  $template->AddDeleteAction($element_id, $delete_link, $delete_params, $delete_opts);
  
  return $template->GetEditAreaId($element_id);
}

/**
 * Записывает значение по ключу в кэш компонента.
 * @param  Bitrix\Template $template  Шаблон компонента Bitrix.
 * @param  array           &$arResult Массив данных для отображения в представлении.
 * @param  string          $name      Имя свойства в массиве данных.
 * @param  mixed           $value     Значение.
 * @return mixed                      Значение.
 */
function bx_tp_cache($template, &$arResult, $name, $value) {
  $is_new = !in_array($key, $template->__component->arResultCacheKeys);

  if ($is_new) {
    $new_keys = array_merge($template->__component->arResultCacheKeys, array($key));
    $template->__component->arResultCacheKeys = $new_keys;
  }

  $template->__component->arResult[$key] = $value;
  return $arResult[$key] = $value;
}