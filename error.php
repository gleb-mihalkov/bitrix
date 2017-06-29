<?php
namespace Bx
{
	/**
	 * Класс пользовательских исключений.
	 */
	class Error extends \Exception {}
}

namespace
{
	/**
	 * Возвращает экземпляр пользовательского исключения.
	 * @param  string     $message  Текст сообщения об ошибке.
	 * @param  string     $code     Код сообщения об ошибке.
	 * @param  \Exception $previous Предыдущее исключение.
	 * @return \Bx\Error            Пользовательское исключение.
	 */
	function bx_error($message = null, $code = null, $previous = null) {
		return new \Bx\Error($message, $code, $previous);
	}
}