<?php
namespace Bx
{
  /**
   * Обертка для класса валидации данных.
   */
  class Validator implements \ArrayAccess
  {
    /**
     * Экземпляр библиотечного класса для валидации.
     * @var \Bx\Vendor\Validator
     */
    protected $validator;

    /**
     * Создает экземпляр класса.
     * @param array|Request $array Данные для валидации.
     */
    public function __construct($array)
    {
      $this->validator = new \Validator($array);
    }



    /**
     * Коллекция существующих на данный момент валидаторов полей.
     * @var array
     */
    protected $fields = array();

    /**
     * Получает валидатор свойства.
     * @param  string         $name Имя свойства.
     * @return ValidatorField       Валидатор свойства.
     */
    protected function getField($name)
    {
      $field = empty($this->fields[$name])
        ? ($this->fields[$name] = new ValidatorField($name, $this->validator))
        : $this->fields[$name];

      return $field;
    }

    /**
     * Получает валидатор свойства.
     * @param  string         $name Имя свойства.
     * @return ValidatorField       Валидатор свойства.
     */
    public function __get($name)
    {
      return $this->getField($name);
    }

    /**
     * Вызывается при проверке существования свойства.
     * @param  string  $name Имя свойства.
     * @return boolean       Всегда true.
     */
    public function __isset($name)
    {
      return true;
    }

    /**
     * Получает валидатор свойства.
     * @param  string         $name Имя свойства.
     * @return ValidatorField       Валидатор свойства.
     */
    public function offsetGet($name)
    {
      return $this->getField($name);
    }

    /**
     * Выполняется при попытке установить значение свойства.
     * @param  string $name  Имя свойства.
     * @param  mixed  $value Значение.
     * @return void
     */
    public function offsetSet($name, $value)
    {
      return;
    }

    /**
     * Выполняется при проверке существования свойства.
     * @param  string  $name Имя свойства.
     * @return boolean       Всегда true.
     */
    public function offsetExists($name)
    {
      return true;
    }

    /**
     * Выполняется при удалении свойства.
     * @param  string $name Имя свойства.
     * @return void
     */
    public function offsetUnset($name)
    {
      return;
    }



    /**
     * Возвращает коллекцию всех ошибок валидации.
     * @return array Именованная коллекция ошибок валидации.
     */
    public function validate()
    {
      foreach ($this->fields as $field) {
        $field->validate();
      }

      return $this->validator->hasErrors()
        ? $this->validator->getAllErrors()
        : null;
    }



    /**
     * Коллекция сообщений об ошибке по умолчанию.
     * @var array
     */
    protected static $messages = array();

    /**
     * Задает указанному правилу сообщение об ошибке по умолчанию.
     * @param  string $rule    Имя правила.
     * @param  string $message Сообщение об ошибке.
     * @return void
     */
    public static function setMessage($rule, $message = null)
    {
      self::$messages[$rule] = $message;
    }

    /**
     * Получает сообщение об ошибке по умолчанию для указанного правила.
     * @param  string $name Имя правила.
     * @return string       Сообщение об ошибке.
     */
    public static function getMessage($name)
    {
      return isset(self::$messages[$name])
        ? self::$messages[$name]
        : '';
    }
  }
}

namespace
{
  /**
   * Получает экземпляр валидатора для набора данных.
   * @param  array         $data Данные.
   * @return \Bx\Validator       Валидатор.
   */
  function bx_validate($data) {
    return new \Bx\Validator($data);
  }
}