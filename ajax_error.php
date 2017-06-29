<?php
namespace Bx
{
  /**
   * Исключение, возникающее для генерации ошибки HTTP 400
   * Bad Request.
   */
  class AjaxError extends Error
  {
    /**
     * Коллекция именованных ошибок.
     * @var array
     */
    protected $errors;

    /**
     * Показывает, описана ли ошибка с помощью единичного
     * сообщения.
     * @var boolean
     */
    public $isMessage;

    /**
     * Создает экземпляр класса.
     * @param string|array $errors Сообщение об ошибке или коллекция
     *                             именованных ошибок.
     */
    public function __construct($errors)
    {
      $isValue = is_scalar($errors);
      $this->isMessage = $isValue;

      if ($isValue) {
        parent::__construct($errors, 400);
        return;
      }

      $this->errors = $errors;
      parent::__construct();
    }

    /**
     * Возвращает именованную коллекцию ошибок.
     * @return array Массив.
     */
    public function getErrors()
    {
      return $this->errors
        ? $this->errors
        : array();
    }
  }
}

namespace
{
  /**
   * Возвращает экземпляр ошибки запроса.
   * @param  string|array  $name  Имя поля с ошибкой,
   *                              или коллекция полей с сообщениями об их ошибках,
   *                              или текст сообщения об ошибке без привязке к имени
   *                              поля.
   * @param  string        $value Сообщение об ошибке (если задается имя поля).
   * @return \Bx\AjaxError         Экземпляр ошибки.
   */
  function bx_ajax_error($name = '', $value = null) {
    if (is_array($name)) return new \Bx\AjaxError($name);

    $argsCount = func_num_args();

    if ($argsCount < 2) {
      return new \Bx\AjaxError($name);
    }

    $errors = array();
    $errors[$name] = $value;
    return new \Bx\AjaxError($errors);
  }
}