<?php
namespace Bx
{
  /**
   * Исключение, возникающее для генерации ошибки HTTP 400
   * Bad Request.
   */
  class AjaxError extends \Exception
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
   * @param  string|array  $errors Сообщение об ошибке или коллекция ошибок.
   * @return \Bx\AjaxError         Экземпляр ошибки.
   */
  function bx_ajax_error($errors = null) {
    return new Bx\AjaxError($errors);
  }
}