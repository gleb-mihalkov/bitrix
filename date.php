<?php
/**
 * Набор функций для работы с датами.
 */

/**
 * Возвращает модель даты - времени, или, если передан выходной формат,
 * строку, представляющую дату - время.
 * @param  string|integer|DateTime|Bitrix\DateTime $source Входные данные для
 *                                                         получения даты.
 * @param  string                                  $format Выходной формат даты.
 * @return Bitrix\DateTime|string                          Модель или строка даты.
 */
function bx_date($source, $format = null) {
	if (is_integer($source)) {
		$date = \Bitrix\Main\Type\DateTime::createFromTimestamp($source);
	}

	if (is_object($source)) {
		$is_native = $source instanceof DateTime;
		$date = $is_native ? \Bitrix\Main\Type\DateTime::createFromPhp($source) : $source;
	}
	else {
		$source = bx_date_preprocess($source);
		$date = new \Bitrix\Main\Type\DateTime($source);
	}

	return $format === null ? $date : $date->format($format);
}

/**
 * Пре-форматирует строку даты.
 * @param  string $date Строка с датой.
 * @return string       Отформатированная строка.
 */
function bx_date_preprocess($date) {
	return $date;
}